<?php
/**
 * POST /api/curl-proxy.php — execute HTTP request server-side (bypasses browser CORS).
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/curl-proxy-helper.php';

header('X-Content-Type-Options: nosniff');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    curl_proxy_json_error(405, 'Method not allowed.');
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    curl_proxy_json_error(400, 'Invalid JSON body.');
}

$method = $data['method'] ?? 'GET';
$url    = $data['url'] ?? '';
$body   = isset($data['body']) ? (string) $data['body'] : null;
$headers = [];

if (!empty($data['headers']) && is_array($data['headers'])) {
    foreach ($data['headers'] as $name => $value) {
        if (is_string($name) && (is_string($value) || is_numeric($value))) {
            $headers[$name] = (string) $value;
        }
    }
}

$result = curl_proxy_execute($method, $url, $headers, $body);
echo json_encode($result);
