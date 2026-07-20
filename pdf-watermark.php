<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Add Watermark to PDF', 'Stamp a text watermark on every page of your PDF.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Add Watermark</h1>
        <p>Stamp diagonal text over your PDF pages. Adjust size and transparency.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-wm-input">Select PDF</label>
        <input type="file" id="pdf-wm-input" accept=".pdf,application/pdf">

        <label for="pdf-wm-text">Watermark text</label>
        <input type="text" id="pdf-wm-text" placeholder="CONFIDENTIAL" class="pdf-page-input">

        <div class="form-row">
            <div>
                <label for="pdf-wm-size">Size</label>
                <input type="number" id="pdf-wm-size" value="48" min="12" max="120" class="pdf-page-input">
            </div>
            <div>
                <label for="pdf-wm-opacity">Opacity (0–1)</label>
                <input type="number" id="pdf-wm-opacity" value="0.3" min="0.1" max="1" step="0.1" class="pdf-page-input">
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-wm" disabled>Add watermark</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-wm-clear">Clear</button>
        </div>

        <div id="pdf-wm-status" class="alert alert-info hidden"></div>
        <div id="pdf-wm-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-watermark'); require_once __DIR__ . '/includes/footer.php'; ?>
