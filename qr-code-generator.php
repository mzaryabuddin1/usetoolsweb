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
                        <div class="qr-design-full qr-fg-section">
                            <label>Foreground</label>
                            <div class="qr-fg-mode-toggle" role="tablist" aria-label="Foreground style">
                                <button type="button" class="qr-fg-mode-btn active" data-mode="solid" role="tab" aria-selected="true">Solid</button>
                                <button type="button" class="qr-fg-mode-btn" data-mode="gradient" role="tab" aria-selected="false">Gradient</button>
                            </div>
                            <input type="hidden" id="qr-fg-mode" value="solid">

                            <div id="qr-fg-solid-panel" class="qr-fg-panel">
                                <label for="qr-fg-color">Color</label>
                                <input type="color" id="qr-fg-color" value="#0a2558" title="Foreground color">
                            </div>

                            <div id="qr-fg-gradient-panel" class="qr-fg-panel hidden">
                                <div class="qr-fg-colors-row">
                                    <div>
                                        <label for="qr-fg-gradient-start">Start color</label>
                                        <input type="color" id="qr-fg-gradient-start" value="#0a2558" title="Gradient start">
                                    </div>
                                    <div>
                                        <label for="qr-fg-gradient-end">End color</label>
                                        <input type="color" id="qr-fg-gradient-end" value="#3b82f6" title="Gradient end">
                                    </div>
                                </div>
                                <div class="qr-gradient-type-toggle" role="tablist" aria-label="Gradient type">
                                    <button type="button" class="qr-gradient-type-btn active" data-type="linear" role="tab" aria-selected="true">Linear</button>
                                    <button type="button" class="qr-gradient-type-btn" data-type="radial" role="tab" aria-selected="false">Radial</button>
                                </div>
                                <input type="hidden" id="qr-gradient-type" value="linear">
                                <div id="qr-gradient-rotation-wrap">
                                    <label for="qr-gradient-rotation">Angle: <span id="qr-gradient-rotation-value">45</span>°</label>
                                    <input type="range" id="qr-gradient-rotation" min="0" max="360" value="45" step="5">
                                </div>
                                <div class="qr-gradient-css-wrap">
                                    <label for="qr-gradient-css">Or paste CSS gradient</label>
                                    <textarea id="qr-gradient-css" rows="3" placeholder="linear-gradient(45deg, rgba(13, 83, 189, 1) 0%, rgba(19, 176, 168, 1) 30%, rgba(21, 176, 60, 1) 50%, rgba(93, 156, 17, 1) 70%, rgba(242, 213, 0, 1) 100%)"></textarea>
                                    <div id="qr-gradient-css-preview" class="qr-gradient-css-preview hidden" aria-hidden="true"></div>
                                    <p id="qr-gradient-css-hint" class="hint">Supports <code>linear-gradient(...)</code> and <code>radial-gradient(...)</code> with multiple color stops.</p>
                                    <p id="qr-gradient-css-error" class="qr-gradient-css-error hidden"></p>
                                </div>
                            </div>
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
                            <label>Logo image <span class="hint">(optional, max 2 MB)</span></label>
                            <div class="qr-logo-picker" id="qr-logo-picker">
                                <label for="qr-logo-input" class="qr-logo-picker-btn">Choose image</label>
                                <input type="file" id="qr-logo-input" class="hidden" accept="image/png,image/jpeg,image/gif,image/svg+xml,.png,.jpg,.gif,.svg">
                                <span class="qr-logo-filename" id="qr-logo-filename">PNG, JPG, GIF, or SVG</span>
                                <img class="qr-logo-thumb hidden" id="qr-logo-thumb" alt="" width="36" height="36">
                            </div>
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
