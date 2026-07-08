<?php
/**
 * POST /api/email-test.php — send one test email using user-supplied config.
 * Credentials are used only for this request and are never saved or logged.
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/email-test-helper.php';

header('X-Content-Type-Options: nosniff');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    email_test_json_error(405, 'Method not allowed.');
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    email_test_json_error(400, 'Invalid JSON body.');
}

try {
    $result = email_test_execute($data);
    echo json_encode([
        'ok'        => $result['ok'],
        'transport' => $result['transport'],
        'message'   => $result['message'],
        'log'       => $result['log'] ?? [],
        'time_ms'   => $result['time_ms'],
    ]);
} catch (InvalidArgumentException $e) {
    email_test_json_error(400, $e->getMessage());
} catch (Throwable $e) {
    email_test_json_error(500, $e->getMessage());
}
