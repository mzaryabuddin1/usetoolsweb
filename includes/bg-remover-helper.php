<?php
/**
 * Background remover — calls cPanel Python API or local rembg script.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

function bg_remove_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function bg_remove_tmp_dir(): string
{
    $dir = defined('BG_REMOVE_TMP_DIR') ? BG_REMOVE_TMP_DIR : (dirname(__DIR__) . '/tmp/bg-remove');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function bg_remove_max_bytes(): int
{
    return defined('BG_REMOVE_MAX_BYTES') ? (int) BG_REMOVE_MAX_BYTES : (15 * 1024 * 1024);
}

function bg_remove_api_url(): string
{
    return defined('BG_REMOVE_API_URL') ? rtrim((string) BG_REMOVE_API_URL, '/') : '';
}

function bg_remove_allowed_mime(string $mime): bool
{
    return in_array(strtolower($mime), ['image/jpeg', 'image/png', 'image/webp'], true);
}

function bg_remove_validate_upload(array $file): string
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        bg_remove_json_error(400, 'No image uploaded.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        bg_remove_json_error(400, 'Upload failed (error ' . $file['error'] . ').');
    }
    if ($file['size'] > bg_remove_max_bytes()) {
        $mb = (int) (bg_remove_max_bytes() / (1024 * 1024));
        bg_remove_json_error(413, 'Image is too large. Maximum is ' . $mb . ' MB.');
    }

    $mime = $file['type'] ?? '';
    if (!bg_remove_allowed_mime($mime)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
        if ($finfo) {
            finfo_close($finfo);
        }
    }
    if (!bg_remove_allowed_mime((string) $mime)) {
        bg_remove_json_error(400, 'Please upload a JPG, PNG, or WebP image.');
    }

    return (string) $mime;
}

/**
 * POST image to cPanel Python app /remove endpoint.
 */
function bg_remove_via_api(array $file, string $mime): string
{
    $base = bg_remove_api_url();
    if ($base === '') {
        bg_remove_json_error(500, 'BG_REMOVE_API_URL is not configured.');
    }

    $url = $base . '/remove';
    $timeout = defined('BG_REMOVE_TIMEOUT') ? (int) BG_REMOVE_TIMEOUT : 120;

    $post = [
        'image' => new CURLFile($file['tmp_name'], $mime, $file['name'] ?? 'image.jpg'),
    ];

    $ch = curl_init($url);
    $headers = [];
    $secret = defined('BG_REMOVE_API_SECRET') ? BG_REMOVE_API_SECRET : '';
    if ($secret !== '') {
        $headers[] = 'X-BG-Remove-Key: ' . $secret;
    }

    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => $headers,
    ]);

    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        bg_remove_json_error(502, 'Python app unreachable: ' . ($err ?: 'connection failed'));
    }

    if ($code !== 200) {
        $msg = 'Python app error (HTTP ' . $code . ')';
        $json = json_decode($body, true);
        if (is_array($json) && !empty($json['error'])) {
            $msg = $json['error'];
        }
        bg_remove_json_error($code >= 400 ? $code : 502, $msg);
    }

    if (stripos($ctype, 'image/png') === false && stripos($ctype, 'application/octet-stream') === false) {
        bg_remove_json_error(500, 'Python app returned unexpected response.');
    }

    if ($body === '') {
        bg_remove_json_error(500, 'Python app returned empty image.');
    }

    return $body;
}

/* ---------- Local shell fallback (dev only) ---------- */

function bg_remove_script_path(): string
{
    if (defined('REMBG_SCRIPT') && REMBG_SCRIPT !== '') {
        return REMBG_SCRIPT;
    }
    return dirname(__DIR__) . '/scripts/rembg-remove.py';
}

function bg_remove_python_binary(): ?string
{
    static $cached = null;
    static $resolved = false;
    if ($resolved) {
        return $cached;
    }
    $resolved = true;

    $candidates = [];
    if (defined('PYTHON_BINARY') && PYTHON_BINARY !== '') {
        $candidates[] = PYTHON_BINARY;
    }
    if (PHP_OS_FAMILY === 'Windows') {
        $candidates[] = 'py -3';
        $candidates[] = 'python';
    } else {
        $candidates[] = 'python3';
        $candidates[] = 'python';
    }

    foreach ($candidates as $bin) {
        $out = @shell_exec($bin . ' -c "import sys; print(sys.version_info[0])" 2>&1');
        if ($out && trim($out) === '3') {
            $cached = $bin;
            return $cached;
        }
    }
    return null;
}

function bg_remove_via_shell(array $file): string
{
    $python = bg_remove_python_binary();
    $script = bg_remove_script_path();
    if (!$python || !is_file($script)) {
        bg_remove_json_error(503, 'Local Python/rembg not available. Set BG_REMOVE_API_URL for production.');
    }

    $out = @shell_exec($python . ' -c "import rembg; print(1)" 2>&1');
    if (trim((string) $out) !== '1') {
        bg_remove_json_error(503, 'rembg not installed locally.');
    }

    $dir   = bg_remove_tmp_dir();
    $token = bin2hex(random_bytes(8));
    $in    = $dir . '/' . $token . '-in';
    $outFile = $dir . '/' . $token . '-out.png';

    if (!copy($file['tmp_name'], $in)) {
        bg_remove_json_error(500, 'Could not prepare image.');
    }

    $timeout = defined('BG_REMOVE_TIMEOUT') ? (int) BG_REMOVE_TIMEOUT : 120;
    $cmd = $python . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($in) . ' ' . escapeshellarg($outFile) . ' 2>&1';
    $output = (string) @shell_exec($cmd);
    @unlink($in);

    if (!is_file($outFile) || filesize($outFile) === 0) {
        @unlink($outFile);
        bg_remove_json_error(500, trim($output) !== '' ? trim($output) : 'Background removal failed.');
    }

    $png = file_get_contents($outFile);
    @unlink($outFile);
    return $png ?: '';
}

/**
 * Process uploaded image; returns PNG bytes.
 */
function bg_remove_process_upload(array $file): string
{
    $mime = bg_remove_validate_upload($file);

    if (bg_remove_api_url() !== '') {
        return bg_remove_via_api($file, $mime);
    }

    return bg_remove_via_shell($file);
}
