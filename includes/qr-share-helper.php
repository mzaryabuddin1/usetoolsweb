<?php
/**
 * QR file share helper — temporary uploads with auto-expiry.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

function qr_share_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function qr_share_dir(): string
{
    $dir = defined('QR_SHARE_TMP_DIR') ? QR_SHARE_TMP_DIR : (dirname(__DIR__) . '/tmp/qr-shares');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function qr_share_retention_seconds(): int
{
    $days = defined('QR_SHARE_RETENTION_DAYS') ? (int) QR_SHARE_RETENTION_DAYS : 10;
    return max(1, $days) * 86400;
}

function qr_share_max_bytes(): int
{
    return defined('QR_SHARE_MAX_BYTES') ? (int) QR_SHARE_MAX_BYTES : (25 * 1024 * 1024);
}

function qr_share_allowed_ext(string $filename): ?string
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $blocked = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8',
        'html', 'htm', 'svg', 'js', 'mjs', 'cjs', 'exe', 'bat', 'cmd', 'sh', 'ps1',
        'htaccess', 'cgi', 'pl', 'py', 'rb', 'asp', 'aspx', 'jsp',
    ];
    if ($ext === '' || in_array($ext, $blocked, true)) {
        return null;
    }

    $allowed = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic',
        'zip', 'rar', '7z', 'tar', 'gz',
        'mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac',
        'mp4', 'webm', 'mov', 'mkv', 'avi',
        'json', 'xml', 'md',
    ];

    return in_array($ext, $allowed, true) ? $ext : null;
}

function qr_share_mime_for_ext(string $ext): string
{
    static $map = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif'  => 'image/gif', 'webp' => 'image/webp',
        'zip'  => 'application/zip',
        'mp3'  => 'audio/mpeg', 'mp4' => 'video/mp4',
        'txt'  => 'text/plain', 'json' => 'application/json',
    ];
    return $map[$ext] ?? 'application/octet-stream';
}

function qr_share_cleanup_expired(): void
{
    $dir = qr_share_dir();
    $now = time();

    foreach (glob($dir . '/*.json') ?: [] as $metaFile) {
        $meta = json_decode(@file_get_contents($metaFile), true);
        if (!is_array($meta)) {
            @unlink($metaFile);
            continue;
        }

        $expires = (int) ($meta['expires_at'] ?? 0);
        $token   = $meta['token'] ?? basename($metaFile, '.json');
        $dataFile = $dir . '/' . $token . '.dat';

        if ($expires > 0 && $expires <= $now) {
            @unlink($metaFile);
            @unlink($dataFile);
        }
    }
}

function qr_share_token_path(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
        return null;
    }

    $dir      = qr_share_dir();
    $metaFile = $dir . '/' . $token . '.json';
    $dataFile = $dir . '/' . $token . '.dat';

    if (!is_file($metaFile) || !is_file($dataFile)) {
        return null;
    }

    $meta = json_decode(file_get_contents($metaFile), true);
    if (!is_array($meta)) {
        return null;
    }

    if ((int) ($meta['expires_at'] ?? 0) <= time()) {
        @unlink($metaFile);
        @unlink($dataFile);
        return null;
    }

    return [
        'meta'      => $meta,
        'meta_file' => $metaFile,
        'data_file' => $dataFile,
    ];
}

function qr_share_public_url(string $token): string
{
    return rtrim(SITE_URL, '/') . '/f/' . $token;
}

function qr_share_save_upload(array $file): array
{
    qr_share_cleanup_expired();

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        qr_share_json_error(400, 'No file uploaded.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        qr_share_json_error(400, 'Upload failed (error ' . $file['error'] . ').');
    }

    if ($file['size'] > qr_share_max_bytes()) {
        $mb = (int) (qr_share_max_bytes() / (1024 * 1024));
        qr_share_json_error(413, 'File is too large. Maximum size is ' . $mb . ' MB.');
    }

    $original = basename($file['name']);
    $original = preg_replace('/[^\w.\- ()]/u', '_', $original) ?: 'file';
    $ext = qr_share_allowed_ext($original);

    if (!$ext) {
        qr_share_json_error(400, 'File type not allowed. Upload documents, images, audio, video, or ZIP archives.');
    }

    $token     = bin2hex(random_bytes(16));
    $dir       = qr_share_dir();
    $dataFile  = $dir . '/' . $token . '.dat';
    $metaFile  = $dir . '/' . $token . '.json';
    $expiresAt = time() + qr_share_retention_seconds();

    if (!move_uploaded_file($file['tmp_name'], $dataFile)) {
        qr_share_json_error(500, 'Could not save uploaded file.');
    }

    $meta = [
        'token'         => $token,
        'original_name' => $original,
        'ext'           => $ext,
        'mime'          => qr_share_mime_for_ext($ext),
        'size'          => (int) $file['size'],
        'uploaded_at'   => time(),
        'expires_at'    => $expiresAt,
    ];

    file_put_contents($metaFile, json_encode($meta, JSON_PRETTY_PRINT));

    return [
        'ok'          => true,
        'token'       => $token,
        'url'         => qr_share_public_url($token),
        'filename'    => $original,
        'size'        => (int) $file['size'],
        'expires_at'  => gmdate('c', $expiresAt),
        'expires_in_days' => (int) (qr_share_retention_seconds() / 86400),
    ];
}

function qr_share_serve(string $token): void
{
    qr_share_cleanup_expired();

    $entry = qr_share_token_path($token);
    if (!$entry) {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><title>Link expired</title></head><body style="font-family:sans-serif;text-align:center;padding:3rem;">';
        echo '<h1>File not found or expired</h1>';
        echo '<p>This shared file was deleted after 10 days or the link is invalid.</p>';
        echo '<p><a href="' . htmlspecialchars(rtrim(SITE_URL, '/') . '/qr-code-generator') . '">Create a new QR share</a></p>';
        echo '</body></html>';
        exit;
    }

    $meta = $entry['meta'];
    $path = $entry['data_file'];
    $name = $meta['original_name'] ?? 'download';
    $mime = $meta['mime'] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($path));
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $name) . '"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');

    readfile($path);
    exit;
}
