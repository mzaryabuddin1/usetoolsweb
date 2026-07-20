<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Compare PDF', 'Compare two PDF files side by side.');
$extra_head = pdf_tool_head(true);
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Compare PDF</h1>
        <p>Upload two PDFs to preview page 1 side by side and compare page counts.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-compare-a">PDF file A</label>
        <input type="file" id="pdf-compare-a" accept=".pdf,application/pdf">

        <label for="pdf-compare-b">PDF file B</label>
        <input type="file" id="pdf-compare-b" accept=".pdf,application/pdf">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-compare" disabled>Compare</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-compare-clear">Clear</button>
        </div>

        <div id="pdf-compare-status" class="alert alert-info hidden"></div>
        <div id="pdf-compare-error" class="alert alert-error hidden"></div>

        <div id="pdf-compare-result" class="pdf-compare-result hidden">
            <p id="pdf-compare-summary" class="hint"></p>
            <div class="pdf-compare-canvas-wrap">
                <div><p>File A</p><canvas id="pdf-compare-canvas-a"></canvas></div>
                <div><p>File B</p><canvas id="pdf-compare-canvas-b"></canvas></div>
            </div>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-compare'); require_once __DIR__ . '/includes/footer.php'; ?>
