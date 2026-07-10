<?php
/**
 * GET /api/speed-test.php?size=N — download payload
 * POST with raw body — upload test (echo size back as JSON)
 */

require_once dirname(__DIR__) . '/config.php';

header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $max = defined('SPEED_TEST_MAX_BYTES') ? (int) SPEED_TEST_MAX_BYTES : 5242880;
    $raw = file_get_contents('php://input');
    $size = strlen($raw);
    if ($size > $max) {
        http_response_code(413);
        echo json_encode(['ok' => false, 'error' => 'Payload too large.']);
        exit;
    }
    echo json_encode(['ok' => true, 'bytes' => $size]);
    exit;
}

header('Content-Type: application/octet-stream');

$max = defined('SPEED_TEST_MAX_BYTES') ? (int) SPEED_TEST_MAX_BYTES : 5242880;
$size = (int) ($_GET['size'] ?? 1048576);
$size = max(65536, min($max, $size));

$chunk = str_repeat('0', 65536);
$remaining = $size;
while ($remaining > 0) {
    $write = min(65536, $remaining);
    echo substr($chunk, 0, $write);
    $remaining -= $write;
}
