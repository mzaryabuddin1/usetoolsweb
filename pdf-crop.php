<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Crop PDF', 'Crop PDF margins by setting trim values in points.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Crop PDF</h1>
        <p>Trim margins from all pages. Values are in PDF points (72 pts = 1 inch).</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-crop-input">Select PDF</label>
        <input type="file" id="pdf-crop-input" accept=".pdf,application/pdf">

        <div class="form-row pdf-crop-grid">
            <div><label for="pdf-crop-top">Top</label><input type="number" id="pdf-crop-top" value="0" min="0" class="pdf-page-input"></div>
            <div><label for="pdf-crop-right">Right</label><input type="number" id="pdf-crop-right" value="0" min="0" class="pdf-page-input"></div>
            <div><label for="pdf-crop-bottom">Bottom</label><input type="number" id="pdf-crop-bottom" value="0" min="0" class="pdf-page-input"></div>
            <div><label for="pdf-crop-left">Left</label><input type="number" id="pdf-crop-left" value="0" min="0" class="pdf-page-input"></div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-crop" disabled>Crop &amp; Download</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-crop-clear">Clear</button>
        </div>

        <div id="pdf-crop-status" class="alert alert-info hidden"></div>
        <div id="pdf-crop-error" class="alert alert-error hidden"></div>
    </div>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-crop'); require_once __DIR__ . '/includes/footer.php'; ?>
