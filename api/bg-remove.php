<?php
/**
 * POST /api/bg-remove.php — remove image background (Python rembg).
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/bg-remover-helper.php';

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    bg_remove_json_error(405, 'Method not allowed.');
}

if (!isset($_FILES['image'])) {
    bg_remove_json_error(400, 'No image uploaded. Use field name "image".');
}

$png = bg_remove_process_upload($_FILES['image']);

header('Content-Type: image/png');
header('Content-Disposition: inline; filename="no-background.png"');
header('Cache-Control: no-store');
echo $png;
