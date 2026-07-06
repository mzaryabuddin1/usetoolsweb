<?php
require_once __DIR__ . '/config.php';

$retentionDays = defined('QR_SHARE_RETENTION_DAYS') ? (int) QR_SHARE_RETENTION_DAYS : 10;
$maxMb = (int) (QR_SHARE_MAX_BYTES / (1024 * 1024));

$meta = page_meta(
    'QR Code Generator',
    'Free custom QR code generator — URL, WiFi, vCard, email, SMS, social media, logo, colors, and temporary file sharing.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>QR Code Generator</h1>
        <p>Create custom QR codes for URLs, WiFi, contacts, social links, and more — plus <strong>upload a file</strong> and get a scannable download link (auto-deleted after <?= (int) $retentionDays ?> days).</p>
    </div>

    <div class="tool-panel qr-studio">
        <div class="qr-studio-layout">
            <div class="qr-studio-main">
                <section class="qr-studio-section">
                    <h2 class="qr-studio-heading">1. Enter content</h2>
                    <div id="qr-type-grid" class="qr-type-grid" role="tablist"></div>
                    <div id="qr-type-form" class="qr-type-form"></div>
                </section>

                <section class="qr-studio-section qr-design-section">
                    <h2 class="qr-studio-heading">2. Customize design</h2>
                    <div class="qr-design-grid">
                        <div>
                            <label for="qr-fg-color">Foreground</label>
                            <input type="color" id="qr-fg-color" value="#0a2558">
                        </div>
                        <div>
                            <label for="qr-bg-color">Background</label>
                            <input type="color" id="qr-bg-color" value="#ffffff">
                        </div>
                        <div>
                            <label for="qr-dot-style">Body shape</label>
                            <select id="qr-dot-style">
                                <option value="square">Square</option>
                                <option value="dots">Dots</option>
                                <option value="rounded" selected>Rounded</option>
                                <option value="extra-rounded">Extra rounded</option>
                                <option value="classy">Classy</option>
                            </select>
                        </div>
                        <div>
                            <label for="qr-corner-style">Corner shape</label>
                            <select id="qr-corner-style">
                                <option value="square">Square</option>
                                <option value="extra-rounded" selected>Extra rounded</option>
                                <option value="dot">Dot</option>
                            </select>
                        </div>
                        <div class="qr-design-full">
                            <label for="qr-logo-input">Logo image <span class="hint">(optional, max 2 MB)</span></label>
                            <input type="file" id="qr-logo-input" accept="image/png,image/jpeg,image/gif,image/svg+xml,.png,.jpg,.gif,.svg">
                            <button type="button" class="btn btn-secondary btn-sm qr-logo-clear hidden" id="qr-logo-clear">Remove logo</button>
                        </div>
                        <div class="qr-design-full">
                            <label for="qr-size">Size: <span id="qr-size-value">400</span>px</label>
                            <input type="range" id="qr-size" min="200" max="800" value="400" step="50">
                        </div>
                    </div>
                </section>

                <div class="btn-row qr-action-row">
                    <button type="button" class="btn btn-primary" id="btn-generate-qr">Create QR Code</button>
                </div>
            </div>

            <aside class="qr-studio-preview">
                <h2 class="qr-studio-heading">Preview</h2>
                <div id="qrcode" class="qr-preview-box"></div>
                <div class="btn-row">
                    <button type="button" class="btn btn-primary" id="btn-download-png" disabled>Download PNG</button>
                    <button type="button" class="btn btn-secondary" id="btn-download-svg" disabled>Download SVG</button>
                </div>
                <p id="qr-preview-hint" class="hint">Select a type, fill in details, then create your QR code.</p>
            </aside>
        </div>

        <div id="qr-error" class="alert alert-error hidden"></div>
        <div id="qr-status" class="alert alert-info hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<script>
window.QR_SHARE_CONFIG = {
    retentionDays: <?= (int) $retentionDays ?>,
    maxMb: <?= (int) $maxMb ?>
};
</script>
<?php
$extra_scripts = '<script type="module" src="/assets/js/tools/qr-code.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
