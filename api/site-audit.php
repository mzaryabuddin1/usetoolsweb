<?php
/**
 * POST /api/site-audit.php — SEO, VAPT, or Lighthouse-style report for a URL.
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/site-audit-helper.php';

header('X-Content-Type-Options: nosniff');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    site_audit_json_error(405, 'Method not allowed.');
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    site_audit_json_error(400, 'Invalid JSON body.');
}

$url  = trim((string) ($data['url'] ?? ''));
$type = trim((string) ($data['type'] ?? 'seo'));

if ($url === '') {
    site_audit_json_error(400, 'URL is required.');
}

try {
    $report = site_audit_run($url, $type);
    echo json_encode(['ok' => true, 'report' => $report], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    site_audit_json_error(400, $e->getMessage());
} catch (Throwable $e) {
    site_audit_json_error(500, $e->getMessage());
}
