<?php
/**
 * POST /api/air-share-create.php
 * Create a temporary text or file share link.
 *
 * Text: POST { "type": "text", "content": "..." }
 * File: POST multipart with field "file"
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/air-share-helper.php';

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    air_share_json_error(405, 'Method not allowed.');
}

if (isset($_FILES['file'])) {
    $result = air_share_save_file($_FILES['file']);
    air_share_json_ok($result);
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    air_share_json_error(400, 'Invalid request. Send JSON or upload a file.');
}

$type = $input['type'] ?? '';
if ($type === 'text') {
    $content = (string) ($input['content'] ?? '');
    $result = air_share_save_text($content);
    air_share_json_ok($result);
}

air_share_json_error(400, 'Unknown share type. Use type "text" or upload a file.');
