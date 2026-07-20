<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Sign PDF', 'Add your signature image to a PDF document.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Sign PDF</h1>
        <p>Upload your PDF and a signature image (PNG or JPG) to place on a page.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-sign-pdf">PDF file</label>
        <input type="file" id="pdf-sign-pdf" accept=".pdf,application/pdf">

        <label for="pdf-sign-img">Signature image</label>
        <input type="file" id="pdf-sign-img" accept="image/png,image/jpeg,.png,.jpg">

        <div class="form-row">
            <div><label for="pdf-sign-page">Page</label><input type="number" id="pdf-sign-page" value="1" min="1" class="pdf-page-input"></div>
            <div><label for="pdf-sign-x">X</label><input type="number" id="pdf-sign-x" value="72" class="pdf-page-input"></div>
            <div><label for="pdf-sign-y">Y</label><input type="number" id="pdf-sign-y" value="72" class="pdf-page-input"></div>
            <div><label for="pdf-sign-width">Width (px)</label><input type="number" id="pdf-sign-width" value="150" class="pdf-page-input"></div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-sign" disabled>Sign &amp; Download</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-sign-clear">Clear</button>
        </div>

        <div id="pdf-sign-status" class="alert alert-info hidden"></div>
        <div id="pdf-sign-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-sign'); require_once __DIR__ . '/includes/footer.php'; ?>
