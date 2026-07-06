<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Image Converter',
    'Free online image converter. Convert PNG, JPG, and WebP images to another format in your browser.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Image Converter</h1>
        <p>Convert images between JPG, PNG, and WebP formats. No upload to any server.</p>
    </div>

    <div class="tool-panel">
        <div class="drop-zone" id="convert-drop-zone">
            <p><strong>Drop an image here</strong> or click to browse</p>
            <p>JPG, PNG, WebP supported</p>
        </div>
        <input type="file" id="convert-file-input" accept="image/jpeg,image/png,image/webp" class="hidden">

        <div id="convert-controls" class="hidden">
            <div class="preview-area">
                <img id="convert-preview" alt="Preview">
                <p class="text-muted" id="convert-original-info"></p>
            </div>

            <label for="convert-format">Convert to</label>
            <select id="convert-format">
                <option value="image/jpeg">JPG</option>
                <option value="image/png">PNG</option>
                <option value="image/webp">WebP</option>
            </select>

            <label for="convert-quality" id="convert-quality-wrap">Quality: <span id="convert-quality-value">90</span>%</label>
            <input type="range" id="convert-quality" min="10" max="100" value="90">

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-convert-download">Download Converted Image</button>
                <button type="button" class="btn btn-secondary" id="btn-convert-reset">Choose Another</button>
            </div>
        </div>

        <div id="convert-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/image-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
