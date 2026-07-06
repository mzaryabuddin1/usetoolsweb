<?php
/**
 * Cron endpoint — refresh trending tools from Google Analytics 4.
 *
 * GET /api/cron-trending.php?key=YOUR_SECRET
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/trending-tools.php';

header('X-Content-Type-Options: nosniff');
header('Content-Type: application/json; charset=utf-8');

$secret = defined('TRENDING_CRON_SECRET') ? TRENDING_CRON_SECRET : '';
$key    = (string) ($_GET['key'] ?? '');

if ($secret === '' || !hash_equals($secret, $key)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Forbidden. Set TRENDING_CRON_SECRET in config.php and pass ?key=']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed.']);
    exit;
}

try {
    $result = trending_refresh_from_ga4();
    echo json_encode([
        'ok'         => true,
        'updated_at' => $result['updated_at'],
        'count'      => count($result['tools']),
        'tools'      => array_map(function ($t) {
            return [
                'slug'  => $t['slug'],
                'title' => $t['title'],
                'views' => $t['views'],
            ];
        }, $result['tools']),
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => $e->getMessage(),
    ]);
}
