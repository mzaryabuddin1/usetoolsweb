<?php
/**
 * Shared desk API — AirForShare-style live board.
 *
 * GET  ?desk=abc12345           — load board
 * POST { "action": "create" }   — new board
 * POST { "action": "save", "desk": "...", "text": "..." }
 * POST multipart desk + file    — add file to board
 * POST { "action": "remove-file", "desk": "...", "token": "..." }
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/air-share-helper.php';

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $desk = strtolower(trim((string) ($_GET['desk'] ?? '')));
    if ($desk === '') {
        air_share_json_error(400, 'Missing desk id.');
    }
    air_share_desk_get($desk);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    air_share_json_error(405, 'Method not allowed.');
}

if (isset($_POST['desk'], $_FILES['file'])) {
    $desk = strtolower(trim((string) $_POST['desk']));
    air_share_desk_add_file($desk, $_FILES['file']);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    air_share_json_error(400, 'Invalid JSON.');
}

$action = $input['action'] ?? '';

if ($action === 'create') {
    air_share_json_ok(air_share_desk_create());
}

if ($action === 'join-network') {
    air_share_json_ok(air_share_desk_join_network());
}

if ($action === 'save') {
    $desk = strtolower(trim((string) ($input['desk'] ?? '')));
    $text = (string) ($input['text'] ?? '');
    air_share_desk_save_text($desk, $text);
}

if ($action === 'remove-file') {
    $desk = strtolower(trim((string) ($input['desk'] ?? '')));
    $token = (string) ($input['token'] ?? '');
    air_share_desk_remove_file($desk, $token);
}

air_share_json_error(400, 'Unknown action.');
