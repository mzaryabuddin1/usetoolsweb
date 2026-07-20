<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Edit PDF', 'Add text to a PDF page — position, size, and color.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Edit PDF</h1>
        <p>Add text annotations to a specific page. Coordinates start from the bottom-left corner.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-edit-input">Select PDF</label>
        <input type="file" id="pdf-edit-input" accept=".pdf,application/pdf">

        <label for="pdf-edit-text">Text to add</label>
        <input type="text" id="pdf-edit-text" class="pdf-page-input" placeholder="Your text here">

        <div class="form-row">
            <div><label for="pdf-edit-page">Page</label><input type="number" id="pdf-edit-page" value="1" min="1" class="pdf-page-input"></div>
            <div><label for="pdf-edit-x">X</label><input type="number" id="pdf-edit-x" value="72" class="pdf-page-input"></div>
            <div><label for="pdf-edit-y">Y</label><input type="number" id="pdf-edit-y" value="72" class="pdf-page-input"></div>
            <div><label for="pdf-edit-size">Size</label><input type="number" id="pdf-edit-size" value="14" class="pdf-page-input"></div>
            <div><label for="pdf-edit-color">Color</label><input type="color" id="pdf-edit-color" value="#000000"></div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-edit" disabled>Add text &amp; Download</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-edit-clear">Clear</button>
        </div>

        <div id="pdf-edit-status" class="alert alert-info hidden"></div>
        <div id="pdf-edit-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-edit'); require_once __DIR__ . '/includes/footer.php'; ?>
