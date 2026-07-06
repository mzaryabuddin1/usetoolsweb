<?php
/**
 * WebRTC room signaling for local network sharing.
 *
 * POST JSON: { action: "create"|"join"|"signal", room?, from?, type?, payload? }
 * GET: ?room=123456&since=0
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/air-share-helper.php';

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $room  = preg_replace('/\D/', '', (string) ($_GET['room'] ?? ''));
    $since = max(0, (int) ($_GET['since'] ?? 0));
    air_share_signal_poll($room, $since);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        air_share_json_error(400, 'Invalid JSON.');
    }
    air_share_signal_post($input);
}

air_share_json_error(405, 'Method not allowed.');
