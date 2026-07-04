<?php
/**
 * POST /api/qr-share-upload.php — upload a file for temporary QR sharing.
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/qr-share-helper.php';

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    qr_share_json_error(405, 'Method not allowed.');
}

if (!isset($_FILES['file'])) {
    qr_share_json_error(400, 'No file uploaded.');
}

$result = qr_share_save_upload($_FILES['file']);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
