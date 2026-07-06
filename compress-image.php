<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Image Compressor Online',
    'Free online image compressor. Reduce JPG and PNG file size in your browser — no upload to any server.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Image Compressor</h1>
        <p>Compress JPG and PNG images locally in your browser. Nothing is uploaded to our servers.</p>
    </div>

    <div class="tool-panel">
        <div class="drop-zone" id="drop-zone">
            <p><strong>Drop an image here</strong> or click to browse</p>
            <p>JPG, PNG, WebP supported</p>
        </div>
        <input type="file" id="file-input" accept="image/jpeg,image/png,image/webp" class="hidden">

        <div id="compress-controls" class="hidden">
            <label for="quality">Quality: <span id="quality-value">80</span>%</label>
            <input type="range" id="quality" min="10" max="100" value="80">

            <div class="preview-area">
                <img id="preview" alt="Preview">
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="value" id="stat-original">—</div>
                    <div class="label">Original</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="stat-compressed">—</div>
                    <div class="label">Compressed</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="stat-saved">—</div>
                    <div class="label">Saved</div>
                </div>
            </div>

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-download" disabled>Download Compressed Image</button>
                <button type="button" class="btn btn-secondary" id="btn-reset">Choose Another</button>
            </div>
        </div>

        <div id="compress-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/compress-image.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
