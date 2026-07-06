<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Background Remover',
    'Remove image backgrounds online for free. AI-powered background removal using Python rembg on the server.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Background Remover</h1>
        <p>Remove the background from any photo. Powered by <strong>Python rembg</strong> on the server — works reliably without browser model downloads.</p>
    </div>

    <div class="tool-panel bg-remover-panel">
        <div class="drop-zone" id="bg-drop-zone">
            <p><strong>Drop an image here</strong> or click to browse</p>
            <p>JPG, PNG, WebP — people, products, and objects</p>
        </div>
        <input type="file" id="bg-file-input" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="hidden">

        <div id="bg-remover-progress" class="bg-remover-progress hidden">
            <p id="bg-progress-label">Uploading image…</p>
            <div class="bg-progress-bar"><div id="bg-progress-fill" class="bg-progress-fill"></div></div>
            <p class="hint">First request may take longer while the AI model loads on the server.</p>
        </div>

        <div id="bg-remover-results" class="hidden">
            <div class="bg-compare-grid">
                <div>
                    <p class="bg-compare-label">Original</p>
                    <div class="preview-area bg-checker">
                        <img id="bg-preview-original" alt="Original">
                    </div>
                </div>
                <div>
                    <p class="bg-compare-label">Background removed</p>
                    <div class="preview-area bg-checker">
                        <img id="bg-preview-result" alt="Background removed">
                    </div>
                </div>
            </div>

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="bg-btn-download">Download PNG</button>
                <button type="button" class="btn btn-secondary" id="bg-btn-reset">Choose Another</button>
            </div>
        </div>

        <div id="bg-remover-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/background-remover.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
