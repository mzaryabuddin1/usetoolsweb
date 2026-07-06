<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Favicon Generator',
    'Free favicon generator. Create 16x16, 32x32, and 48x48 PNG favicons from any image.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Favicon Generator</h1>
        <p>Upload an image to generate favicon PNGs in 16×16, 32×32, and 48×48 sizes.</p>
    </div>

    <div class="tool-panel">
        <label for="favicon-upload">Upload image</label>
        <input type="file" id="favicon-upload" accept="image/*">

        <div id="favicon-preview" class="preview-area hidden">
            <div class="stats-row">
                <div class="stat-box">
                    <canvas id="fav-16" width="16" height="16"></canvas>
                    <div class="label">16×16</div>
                    <button type="button" class="btn btn-secondary btn-sm fav-download" data-size="16">Download</button>
                </div>
                <div class="stat-box">
                    <canvas id="fav-32" width="32" height="32"></canvas>
                    <div class="label">32×32</div>
                    <button type="button" class="btn btn-secondary btn-sm fav-download" data-size="32">Download</button>
                </div>
                <div class="stat-box">
                    <canvas id="fav-48" width="48" height="48"></canvas>
                    <div class="label">48×48</div>
                    <button type="button" class="btn btn-secondary btn-sm fav-download" data-size="48">Download</button>
                </div>
            </div>
            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-fav-download-all">Download All</button>
            </div>
        </div>

        <div id="favicon-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/favicon-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
