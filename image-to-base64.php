<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Image to Base64',
    'Free image to Base64 converter. Convert images to Base64 data URIs or preview Base64 strings.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Image to Base64</h1>
        <p>Upload an image to get its Base64 string, or paste a Base64 data URI to preview the image.</p>
    </div>

    <div class="tool-panel">
        <div class="btn-row" style="margin-top:0;">
            <button type="button" class="btn btn-secondary img-tab active" data-tab="encode">Image → Base64</button>
            <button type="button" class="btn btn-secondary img-tab" data-tab="decode">Base64 → Image</button>
        </div>

        <div id="img-encode-panel">
            <label for="img-upload">Upload image</label>
            <input type="file" id="img-upload" accept="image/*">

            <label for="img-base64-out" style="margin-top:1rem;">Base64 output</label>
            <textarea id="img-base64-out" readonly placeholder="Base64 string will appear here..."></textarea>

            <div class="btn-row">
                <button type="button" class="btn btn-secondary" id="btn-img-copy" disabled>Copy Base64</button>
            </div>
        </div>

        <div id="img-decode-panel" class="hidden">
            <label for="img-base64-in">Paste Base64 or data URI</label>
            <textarea id="img-base64-in" placeholder="data:image/png;base64,... or raw base64"></textarea>

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-img-preview">Preview</button>
            </div>

            <div class="preview-area">
                <img id="img-preview" alt="Preview" class="hidden">
            </div>
        </div>

        <div id="img-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/image-to-base64.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
