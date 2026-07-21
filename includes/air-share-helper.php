<?php
/**
 * Air Share helper — temporary text/file links + WebRTC room signaling.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

function air_share_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function air_share_json_ok(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['ok' => true], $data));
    exit;
}

function air_share_dir(): string
{
    $dir = defined('AIR_SHARE_TMP_DIR') ? AIR_SHARE_TMP_DIR : (dirname(__DIR__) . '/tmp/air-shares');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function air_share_rooms_dir(): string
{
    $dir = air_share_dir() . '/rooms';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function air_share_retention_seconds(): int
{
    $days = defined('AIR_SHARE_RETENTION_DAYS') ? (int) AIR_SHARE_RETENTION_DAYS : 7;
    return max(1, $days) * 86400;
}

function air_share_signal_ttl(): int
{
    return defined('AIR_SHARE_SIGNAL_TTL') ? (int) AIR_SHARE_SIGNAL_TTL : 1800;
}

function air_share_text_max_bytes(): int
{
    return defined('AIR_SHARE_TEXT_MAX_BYTES') ? (int) AIR_SHARE_TEXT_MAX_BYTES : (512 * 1024);
}

function air_share_file_max_bytes(): int
{
    return defined('AIR_SHARE_MAX_BYTES') ? (int) AIR_SHARE_MAX_BYTES : (50 * 1024 * 1024);
}

function air_share_allowed_ext(string $filename): ?string
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $blocked = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8',
        'html', 'htm', 'js', 'mjs', 'cjs', 'exe', 'bat', 'cmd', 'sh', 'ps1',
        'htaccess', 'cgi', 'pl', 'py', 'rb', 'asp', 'aspx', 'jsp',
    ];
    if ($ext === '' || in_array($ext, $blocked, true)) {
        return null;
    }

    $allowed = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic', 'svg',
        'zip', 'rar', '7z', 'tar', 'gz',
        'mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac',
        'mp4', 'webm', 'mov', 'mkv', 'avi',
        'json', 'xml', 'md',
    ];

    return in_array($ext, $allowed, true) ? $ext : null;
}

function air_share_mime_for_ext(string $ext): string
{
    static $map = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif'  => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        'zip'  => 'application/zip',
        'mp3'  => 'audio/mpeg', 'mp4' => 'video/mp4',
        'txt'  => 'text/plain', 'json' => 'application/json', 'md' => 'text/markdown',
    ];
    return $map[$ext] ?? 'application/octet-stream';
}

function air_share_public_url(string $token): string
{
    return rtrim(SITE_URL, '/') . '/s/' . $token;
}

function air_share_valid_token(string $token): bool
{
    return (bool) preg_match('/^[a-f0-9]{32}$/', $token);
}

function air_share_valid_room(string $room): bool
{
    return (bool) preg_match('/^[0-9]{6}$/', $room);
}

function air_share_cleanup_expired(): void
{
    $dir = air_share_dir();
    $now = time();

    foreach (glob($dir . '/*.json') ?: [] as $metaFile) {
        $meta = json_decode(@file_get_contents($metaFile), true);
        if (!is_array($meta)) {
            @unlink($metaFile);
            continue;
        }
        $expires = (int) ($meta['expires_at'] ?? 0);
        $token   = $meta['token'] ?? basename($metaFile, '.json');
        if ($expires > 0 && $expires <= $now) {
            @unlink($metaFile);
            @unlink($dir . '/' . $token . '.dat');
        }
    }

    foreach (glob(air_share_rooms_dir() . '/*.json') ?: [] as $roomFile) {
        $room = json_decode(@file_get_contents($roomFile), true);
        if (!is_array($room) || (int) ($room['expires_at'] ?? 0) <= $now) {
            @unlink($roomFile);
        }
    }

    foreach (glob(air_share_desks_dir() . '/*.json') ?: [] as $deskFile) {
        $desk = json_decode(@file_get_contents($deskFile), true);
        if (!is_array($desk) || (int) ($desk['expires_at'] ?? 0) <= $now) {
            @unlink($deskFile);
        }
    }
}

function air_share_token_path(string $token): ?array
{
    if (!air_share_valid_token($token)) {
        return null;
    }

    $dir      = air_share_dir();
    $metaFile = $dir . '/' . $token . '.json';
    if (!is_file($metaFile)) {
        return null;
    }

    $meta = json_decode(file_get_contents($metaFile), true);
    if (!is_array($meta)) {
        return null;
    }

    if ((int) ($meta['expires_at'] ?? 0) <= time()) {
        @unlink($metaFile);
        @unlink($dir . '/' . $token . '.dat');
        return null;
    }

    return [
        'meta'      => $meta,
        'meta_file' => $metaFile,
        'data_file' => $dir . '/' . $token . '.dat',
    ];
}

function air_share_save_text(string $content): array
{
    air_share_cleanup_expired();

    $bytes = strlen($content);
    if ($bytes === 0) {
        air_share_json_error(400, 'Text cannot be empty.');
    }
    if ($bytes > air_share_text_max_bytes()) {
        $kb = (int) (air_share_text_max_bytes() / 1024);
        air_share_json_error(413, 'Text is too large. Maximum is ' . $kb . ' KB.');
    }

    $token     = bin2hex(random_bytes(16));
    $expiresAt = time() + air_share_retention_seconds();
    $metaFile  = air_share_dir() . '/' . $token . '.json';

    $meta = [
        'token'       => $token,
        'type'        => 'text',
        'content'     => $content,
        'size'        => $bytes,
        'uploaded_at' => time(),
        'expires_at'  => $expiresAt,
    ];

    file_put_contents($metaFile, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    return air_share_response($token, $meta);
}

function air_share_save_file(array $file): array
{
    air_share_cleanup_expired();

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        air_share_json_error(400, 'No file uploaded.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        air_share_json_error(400, 'Upload failed (error ' . $file['error'] . ').');
    }
    if ($file['size'] > air_share_file_max_bytes()) {
        $mb = (int) (air_share_file_max_bytes() / (1024 * 1024));
        air_share_json_error(413, 'File is too large. Maximum size is ' . $mb . ' MB.');
    }

    $original = basename($file['name']);
    $original = preg_replace('/[^\w.\- ()]/u', '_', $original) ?: 'file';
    $ext = air_share_allowed_ext($original);
    if (!$ext) {
        air_share_json_error(400, 'File type not allowed.');
    }

    $token     = bin2hex(random_bytes(16));
    $dir       = air_share_dir();
    $dataFile  = $dir . '/' . $token . '.dat';
    $metaFile  = $dir . '/' . $token . '.json';
    $expiresAt = time() + air_share_retention_seconds();

    if (!move_uploaded_file($file['tmp_name'], $dataFile)) {
        air_share_json_error(500, 'Could not save uploaded file.');
    }

    $meta = [
        'token'         => $token,
        'type'          => 'file',
        'original_name' => $original,
        'ext'           => $ext,
        'mime'          => air_share_mime_for_ext($ext),
        'size'          => (int) $file['size'],
        'uploaded_at'   => time(),
        'expires_at'    => $expiresAt,
    ];

    file_put_contents($metaFile, json_encode($meta, JSON_PRETTY_PRINT));

    return air_share_response($token, $meta);
}

function air_share_response(string $token, array $meta): array
{
    return [
        'ok'              => true,
        'token'           => $token,
        'url'             => air_share_public_url($token),
        'type'            => $meta['type'],
        'filename'        => $meta['original_name'] ?? null,
        'size'            => (int) ($meta['size'] ?? 0),
        'expires_at'      => gmdate('c', (int) $meta['expires_at']),
        'expires_in_days' => (int) (air_share_retention_seconds() / 86400),
    ];
}

function air_share_serve(string $token): void
{
    air_share_cleanup_expired();

    $entry = air_share_token_path($token);
    if (!$entry) {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Link expired</title></head>';
        echo '<body style="font-family:sans-serif;text-align:center;padding:3rem;">';
        echo '<h1>Share not found or expired</h1>';
        echo '<p>This link was deleted or is invalid.</p>';
        echo '<p><a href="' . htmlspecialchars(rtrim(SITE_URL, '/') . '/air-share') . '">Create a new share</a></p>';
        echo '</body></html>';
        exit;
    }

    $meta = $entry['meta'];
    $type = $meta['type'] ?? 'file';

    if ($type === 'text') {
        header('Content-Type: text/html; charset=utf-8');
        $content = (string) ($meta['content'] ?? '');
        $expires = gmdate('F j, Y', (int) $meta['expires_at']);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Shared text — ' . htmlspecialchars(SITE_NAME) . '</title>';
        echo '<style>body{font-family:system-ui,sans-serif;max-width:720px;margin:2rem auto;padding:0 1rem;color:#1a1a1a;}';
        echo 'pre{white-space:pre-wrap;word-break:break-word;background:#f5f5f5;padding:1rem;border-radius:8px;border:1px solid #ddd;}';
        echo '.meta{color:#666;font-size:.9rem;margin-bottom:1rem;}a{color:#0a2558;}</style></head><body>';
        echo '<h1>Shared text</h1>';
        echo '<p class="meta">Expires ' . htmlspecialchars($expires) . ' · <a href="' . htmlspecialchars(rtrim(SITE_URL, '/') . '/air-share') . '">Share your own</a></p>';
        echo '<pre id="shared-text">' . htmlspecialchars($content) . '</pre>';
        echo '<p><button onclick="navigator.clipboard.writeText(document.getElementById(\'shared-text\').innerText)">Copy text</button></p>';
        echo '</body></html>';
        exit;
    }

    $path = $entry['data_file'];
    if (!is_file($path)) {
        http_response_code(404);
        exit('File missing.');
    }

    $name = $meta['original_name'] ?? 'download';
    $mime = $meta['mime'] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($path));
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $name) . '"');
    header('X-Content-Type-Options: nosniff');
    if (($meta['ext'] ?? '') === 'svg') {
        header('Content-Security-Policy: default-src \'none\'; sandbox');
    }
    header('Cache-Control: no-store');
    readfile($path);
    exit;
}

function air_share_room_path(string $room): string
{
    return air_share_rooms_dir() . '/' . $room . '.json';
}

function air_share_room_create(): array
{
    air_share_cleanup_expired();
    $roomsDir = air_share_rooms_dir();
    $now = time();
    $expires = $now + air_share_signal_ttl();

    for ($i = 0; $i < 20; $i++) {
        $room = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $path = $roomsDir . '/' . $room . '.json';
        if (is_file($path)) {
            continue;
        }
        $data = [
            'room'       => $room,
            'created_at' => $now,
            'expires_at' => $expires,
            'messages'   => [],
        ];
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
        return ['room' => $room, 'expires_at' => gmdate('c', $expires)];
    }

    air_share_json_error(503, 'Could not create room. Try again.');
}

function air_share_room_load(string $room): ?array
{
    if (!air_share_valid_room($room)) {
        return null;
    }
    $path = air_share_room_path($room);
    if (!is_file($path)) {
        return null;
    }
    $data = json_decode(file_get_contents($path), true);
    if (!is_array($data) || (int) ($data['expires_at'] ?? 0) <= time()) {
        @unlink($path);
        return null;
    }
    return $data;
}

function air_share_room_save(string $room, array $data): void
{
    file_put_contents(air_share_room_path($room), json_encode($data, JSON_PRETTY_PRINT));
}

function air_share_signal_post(array $input): void
{
    air_share_cleanup_expired();

    $action = $input['action'] ?? '';
    if ($action === 'create') {
        air_share_json_ok(air_share_room_create());
    }

    $room = preg_replace('/\D/', '', (string) ($input['room'] ?? ''));
    if (!air_share_valid_room($room)) {
        air_share_json_error(400, 'Invalid room code. Use 6 digits.');
    }

    $data = air_share_room_load($room);
    if (!$data) {
        air_share_json_error(404, 'Room not found or expired.');
    }

    if ($action === 'join') {
        air_share_json_ok(['room' => $room, 'expires_at' => gmdate('c', (int) $data['expires_at'])]);
    }

    if ($action === 'signal') {
        $from = ($input['from'] ?? '') === 'guest' ? 'guest' : 'host';
        $type = (string) ($input['type'] ?? '');
        if (!in_array($type, ['offer', 'answer', 'ice', 'text', 'file-meta', 'file-chunk', 'ping'], true)) {
            air_share_json_error(400, 'Invalid signal type.');
        }
        $payload = $input['payload'] ?? null;
        if ($payload === null && $type !== 'ping') {
            air_share_json_error(400, 'Missing payload.');
        }

        $messages = $data['messages'] ?? [];
        $messages[] = [
            'id'      => count($messages),
            'from'    => $from,
            'type'    => $type,
            'payload' => $payload,
            'ts'      => time(),
        ];
        if (count($messages) > 500) {
            $messages = array_slice($messages, -400);
        }
        $data['messages'] = $messages;
        air_share_room_save($room, $data);
        air_share_json_ok(['id' => count($messages) - 1]);
    }

    air_share_json_error(400, 'Unknown action.');
}

function air_share_signal_poll(string $room, int $since): void
{
    air_share_cleanup_expired();

    if (!air_share_valid_room($room)) {
        air_share_json_error(400, 'Invalid room code.');
    }

    $data = air_share_room_load($room);
    if (!$data) {
        air_share_json_error(404, 'Room not found or expired.');
    }

    $messages = $data['messages'] ?? [];
    $new = [];
    foreach ($messages as $msg) {
        if ((int) ($msg['id'] ?? -1) >= $since) {
            $new[] = $msg;
        }
    }

    air_share_json_ok([
        'room'     => $room,
        'messages' => $new,
        'latest'   => count($messages),
    ]);
}

/* ---------- Shared desk (AirForShare-style live board) ---------- */

function air_share_desks_dir(): string
{
    $dir = air_share_dir() . '/desks';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function air_share_valid_desk(string $desk): bool
{
    return (bool) preg_match('/^[a-z0-9]{8}$/', $desk);
}

function air_share_desk_url(string $desk): string
{
    return rtrim(SITE_URL, '/') . '/air-share?d=' . $desk;
}

function air_share_desk_path(string $desk): string
{
    return air_share_desks_dir() . '/' . $desk . '.json';
}

function air_share_desk_load(string $desk): ?array
{
    if (!air_share_valid_desk($desk)) {
        return null;
    }
    $path = air_share_desk_path($desk);
    if (!is_file($path)) {
        return null;
    }
    $data = json_decode(file_get_contents($path), true);
    if (!is_array($data) || (int) ($data['expires_at'] ?? 0) <= time()) {
        @unlink($path);
        return null;
    }
    return $data;
}

function air_share_desk_save(string $desk, array $data): void
{
    file_put_contents(air_share_desk_path($desk), json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function air_share_desk_create(): array
{
    air_share_cleanup_expired();
    $now = time();
    $expires = $now + air_share_retention_seconds();

    for ($i = 0; $i < 25; $i++) {
        $desk = bin2hex(random_bytes(4));
        if (air_share_desk_load($desk)) {
            continue;
        }
        $data = [
            'desk'       => $desk,
            'text'       => '',
            'files'      => [],
            'board_type' => 'remote',
            'created_at' => $now,
            'updated_at' => $now,
            'expires_at' => $expires,
        ];
        air_share_desk_save($desk, $data);
        return air_share_desk_payload($data);
    }

    air_share_json_error(503, 'Could not create a share board. Try again.');
}

function air_share_client_ip(): string
{
    $candidates = [
        $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',
        $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    foreach ($candidates as $raw) {
        if ($raw === '') {
            continue;
        }
        $ip = trim(explode(',', (string) $raw)[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return '0.0.0.0';
}

function air_share_is_private_ip(string $ip): bool
{
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }

    return !filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    );
}

function air_share_lan_scope_key(): string
{
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $host = preg_replace('/:\d+$/', '', $host) ?? $host;
    $host = strtolower(trim($host));

    if ($host === 'localhost' || $host === '127.0.0.1' || air_share_is_private_ip($host)) {
        return 'host:' . $host;
    }

    return 'ip:' . air_share_client_ip();
}

function air_share_lan_desk_id(string $scopeKey): string
{
    return substr(hash('sha256', 'air-lan:' . $scopeKey), 0, 8);
}

function air_share_lan_scope_hint(string $scopeKey): string
{
    if (str_starts_with($scopeKey, 'host:')) {
        $host = substr($scopeKey, 5);
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return 'This browser only — open Air Share using your computer\'s LAN address (e.g. http://192.168.x.x/air-share) so other devices on Wi‑Fi join the same board.';
        }

        return 'Shared with everyone who opens Air Share at http://' . $host . '/air-share on your network.';
    }

    return 'Shared with everyone on your Wi‑Fi — they can open Air Share without a link.';
}

function air_share_desk_join_lan(): array
{
    air_share_cleanup_expired();

    $scopeKey = air_share_lan_scope_key();
    $desk     = air_share_lan_desk_id($scopeKey);
    $data     = air_share_desk_load($desk);

    if (!$data) {
        $now     = time();
        $expires = $now + air_share_retention_seconds();
        $data    = [
            'desk'       => $desk,
            'text'       => '',
            'files'      => [],
            'board_type' => 'lan',
            'scope'      => $scopeKey,
            'created_at' => $now,
            'updated_at' => $now,
            'expires_at' => $expires,
        ];
        air_share_desk_save($desk, $data);
    }

    $payload = air_share_desk_payload($data);
    $payload['board_type'] = 'lan';
    $payload['scope_hint'] = air_share_lan_scope_hint($scopeKey);

    return $payload;
}

function air_share_desk_payload(array $data): array
{
    $files = [];
    foreach ($data['files'] ?? [] as $f) {
        if (!is_array($f)) {
            continue;
        }
        $files[] = [
            'token' => $f['token'] ?? '',
            'name'  => $f['name'] ?? 'file',
            'size'  => (int) ($f['size'] ?? 0),
            'url'   => air_share_public_url($f['token'] ?? ''),
        ];
    }

    return [
        'desk'            => $data['desk'],
        'url'             => air_share_desk_url($data['desk']),
        'text'            => (string) ($data['text'] ?? ''),
        'files'           => $files,
        'board_type'      => (string) ($data['board_type'] ?? 'remote'),
        'updated_at'      => (int) ($data['updated_at'] ?? 0),
        'expires_at'      => gmdate('c', (int) ($data['expires_at'] ?? 0)),
        'expires_in_days' => (int) (air_share_retention_seconds() / 86400),
    ];
}

function air_share_desk_get(string $desk): void
{
    air_share_cleanup_expired();
    $data = air_share_desk_load($desk);
    if (!$data) {
        air_share_json_error(404, 'Share board not found or expired.');
    }
    air_share_json_ok(air_share_desk_payload($data));
}

function air_share_desk_save_text(string $desk, string $text): void
{
    air_share_cleanup_expired();

    if (!air_share_valid_desk($desk)) {
        air_share_json_error(400, 'Invalid share board.');
    }

    if (strlen($text) > air_share_text_max_bytes()) {
        $kb = (int) (air_share_text_max_bytes() / 1024);
        air_share_json_error(413, 'Text is too large. Maximum is ' . $kb . ' KB.');
    }

    $data = air_share_desk_load($desk);
    if (!$data) {
        air_share_json_error(404, 'Share board not found or expired.');
    }

    $now = time();
    $data['text'] = $text;
    $data['updated_at'] = $now;
    $data['expires_at'] = $now + air_share_retention_seconds();
    air_share_desk_save($desk, $data);
    air_share_json_ok(air_share_desk_payload($data));
}

function air_share_desk_add_file(string $desk, array $file): void
{
    air_share_cleanup_expired();

    if (!air_share_valid_desk($desk)) {
        air_share_json_error(400, 'Invalid share board.');
    }

    $data = air_share_desk_load($desk);
    if (!$data) {
        air_share_json_error(404, 'Share board not found or expired.');
    }

    $files = $data['files'] ?? [];
    if (count($files) >= 20) {
        air_share_json_error(413, 'Maximum 20 files per board.');
    }

    $stored = air_share_save_file($file);
    $entry = [
        'token'       => $stored['token'],
        'name'        => $stored['filename'] ?? 'file',
        'size'        => (int) ($stored['size'] ?? 0),
        'uploaded_at' => time(),
    ];
    $files[] = $entry;
    $now = time();
    $data['files'] = $files;
    $data['updated_at'] = $now;
    $data['expires_at'] = $now + air_share_retention_seconds();
    air_share_desk_save($desk, $data);
    air_share_json_ok(air_share_desk_payload($data));
}

function air_share_desk_remove_file(string $desk, string $token): void
{
    air_share_cleanup_expired();

    if (!air_share_valid_desk($desk) || !air_share_valid_token($token)) {
        air_share_json_error(400, 'Invalid request.');
    }

    $data = air_share_desk_load($desk);
    if (!$data) {
        air_share_json_error(404, 'Share board not found or expired.');
    }

    $files = $data['files'] ?? [];
    $found = false;
    $newFiles = [];
    foreach ($files as $f) {
        if (($f['token'] ?? '') === $token) {
            $found = true;
            $entry = air_share_token_path($token);
            if ($entry) {
                @unlink($entry['meta_file']);
                @unlink($entry['data_file']);
            }
            continue;
        }
        $newFiles[] = $f;
    }

    if (!$found) {
        air_share_json_error(404, 'File not found on this board.');
    }

    $now = time();
    $data['files'] = $newFiles;
    $data['updated_at'] = $now;
    $data['expires_at'] = $now + air_share_retention_seconds();
    air_share_desk_save($desk, $data);
    air_share_json_ok(air_share_desk_payload($data));
}
