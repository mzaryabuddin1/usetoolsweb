<?php
require_once __DIR__ . '/config.php';

$retentionDays = defined('QR_SHARE_RETENTION_DAYS') ? (int) QR_SHARE_RETENTION_DAYS : 10;
$maxMb = (int) (QR_SHARE_MAX_BYTES / (1024 * 1024));

$meta = page_meta(
    'QR Code Generator',
    'Free QR code generator. Create QR codes from text, URLs, or uploaded files. Temporary file links auto-delete after ' . $retentionDays . ' days.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>QR Code Generator</h1>
        <p>Generate QR codes from text, a URL, or an uploaded file. Share documents and media — scan to download.</p>
    </div>

    <div class="tool-panel qr-generator-panel">
        <div class="qr-mode-tabs" role="tablist">
            <button type="button" class="qr-mode-tab active" data-mode="text" role="tab" aria-selected="true">Text / URL</button>
            <button type="button" class="qr-mode-tab" data-mode="file" role="tab" aria-selected="false">Upload file</button>
        </div>

        <div id="qr-mode-text" class="qr-mode-panel">
            <label for="qr-text">Text or URL</label>
            <input type="text" id="qr-text" placeholder="https://example.com or any text">
        </div>

        <div id="qr-mode-file" class="qr-mode-panel hidden">
            <div class="alert alert-info qr-share-disclaimer">
                <strong>Temporary file hosting</strong> — Your file is stored on our server and linked via QR code.
                Files are <strong>automatically deleted after <?= (int) $retentionDays ?> days</strong>.
                Do not upload private or sensitive data. Max file size: <?= (int) $maxMb ?> MB.
            </div>

            <div class="drop-zone" id="qr-file-drop">
                <p><strong>Drop a file here</strong> or click to browse</p>
                <p>Uploads automatically and generates a QR code — PDF, images, docs, audio, video, ZIP</p>
            </div>
            <input type="file" id="qr-file-input" class="hidden">

            <div id="qr-file-selected" class="qr-file-selected hidden">
                <span id="qr-file-name"></span>
                <button type="button" class="btn btn-secondary btn-sm" id="qr-file-clear">Remove</button>
            </div>

            <div id="qr-share-result" class="qr-share-result hidden">
                <label for="qr-share-url">Share link (in QR code)</label>
                <div class="qr-share-url-row">
                    <input type="text" id="qr-share-url" readonly>
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-copy-share-url">Copy</button>
                </div>
                <p id="qr-share-expiry" class="hint"></p>
            </div>
        </div>

        <label for="qr-size" class="qr-size-label">Size: <span id="qr-size-value">256</span>px</label>
        <input type="range" id="qr-size" min="128" max="512" value="256" step="32">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-generate-qr">Generate QR Code</button>
            <button type="button" class="btn btn-secondary" id="btn-download-qr" disabled>Download PNG</button>
        </div>

        <div class="preview-area" id="qrcode-wrap">
            <div id="qrcode"></div>
        </div>

        <div id="qr-error" class="alert alert-error hidden"></div>
        <div id="qr-status" class="alert alert-info hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/qr-code.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
