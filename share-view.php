<?php
/**
 * GET /s/{token} — view shared text or download shared file.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/air-share-helper.php';

$token = $_GET['token'] ?? '';
if ($token === '' && isset($_SERVER['REQUEST_URI'])) {
    if (preg_match('#/s/([a-f0-9]{32})#', $_SERVER['REQUEST_URI'], $m)) {
        $token = $m[1];
    }
}

if ($token === '') {
    http_response_code(400);
    exit('Invalid link.');
}

air_share_serve($token);
