<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Image Resizer',
    'Free online image resizer. Resize JPG, PNG, and WebP images to custom dimensions in your browser.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Image Resizer</h1>
        <p>Resize images to exact dimensions or by percentage. Files stay on your device.</p>
    </div>

    <div class="tool-panel">
        <div class="drop-zone" id="resize-drop-zone">
            <p><strong>Drop an image here</strong> or click to browse</p>
            <p>JPG, PNG, WebP supported</p>
        </div>
        <input type="file" id="resize-file-input" accept="image/jpeg,image/png,image/webp" class="hidden">

        <div id="resize-controls" class="hidden">
            <div class="preview-area">
                <img id="resize-preview" alt="Preview">
                <p class="text-muted" id="resize-original-size"></p>
            </div>

            <label class="checkbox-inline">
                <input type="checkbox" id="resize-lock-ratio" checked> Lock aspect ratio
            </label>

            <div class="form-row">
                <div>
                    <label for="resize-width">Width (px)</label>
                    <input type="number" id="resize-width" min="1" max="10000">
                </div>
                <div>
                    <label for="resize-height">Height (px)</label>
                    <input type="number" id="resize-height" min="1" max="10000">
                </div>
            </div>

            <label for="resize-percent">Or resize by: <span id="resize-percent-value">100</span>%</label>
            <input type="range" id="resize-percent" min="5" max="200" value="100">

            <label for="resize-format">Output format</label>
            <select id="resize-format">
                <option value="image/jpeg">JPG</option>
                <option value="image/png">PNG</option>
                <option value="image/webp">WebP</option>
            </select>

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-resize-download">Download Resized Image</button>
                <button type="button" class="btn btn-secondary" id="btn-resize-reset">Choose Another</button>
            </div>
        </div>

        <div id="resize-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/resize-image.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
