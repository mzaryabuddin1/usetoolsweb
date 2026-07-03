<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'QR Code Generator',
    'Free QR code generator. Create QR codes from text or URLs and download as PNG instantly.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>QR Code Generator</h1>
        <p>Enter text or a URL below to generate a scannable QR code. Download as PNG when ready.</p>
    </div>

    <div class="tool-panel">
        <label for="qr-text">Text or URL</label>
        <input type="text" id="qr-text" placeholder="https://example.com or any text">

        <label for="qr-size" style="margin-top:1rem;">Size: <span id="qr-size-value">256</span>px</label>
        <input type="range" id="qr-size" min="128" max="512" value="256" step="32">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-generate-qr">Generate QR Code</button>
            <button type="button" class="btn btn-secondary" id="btn-download-qr" disabled>Download PNG</button>
        </div>

        <div class="preview-area" id="qrcode-wrap" style="margin-top:1.5rem;">
            <div id="qrcode"></div>
        </div>

        <div id="qr-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/qr-code.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
