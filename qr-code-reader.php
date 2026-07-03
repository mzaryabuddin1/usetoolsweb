<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'QR Code Reader',
    'Free QR code reader. Scan QR codes using your camera and decode the text instantly.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>QR Code Reader</h1>
        <p>Use your camera to scan and decode QR codes. All processing happens locally.</p>
    </div>

    <div class="tool-panel">
        <div class="btn-row" style="margin-top:0;">
            <button type="button" class="btn btn-primary" id="btn-qr-start">Start Camera</button>
            <button type="button" class="btn btn-secondary" id="btn-qr-stop" disabled>Stop Camera</button>
        </div>

        <div class="preview-area" style="position:relative;">
            <video id="qr-video" playsinline style="max-width:100%;border-radius:8px;display:none;"></video>
            <canvas id="qr-canvas" style="display:none;"></canvas>
        </div>

        <label for="qr-decoded" style="margin-top:1.5rem;">Decoded text</label>
        <textarea id="qr-decoded" readonly placeholder="Scanned QR code content will appear here..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-secondary" id="btn-qr-copy" disabled>Copy</button>
        </div>

        <div id="qr-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/qr-code-reader.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
