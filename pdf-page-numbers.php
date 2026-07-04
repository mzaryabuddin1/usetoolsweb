<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Add Page Numbers to PDF', 'Add page numbers to PDF documents — choose position and starting number.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Add Page Numbers</h1>
        <p>Insert page numbers into your PDF with customizable position and size.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-pn-input">Select PDF</label>
        <input type="file" id="pdf-pn-input" accept=".pdf,application/pdf">

        <div class="form-row">
            <div>
                <label for="pdf-pn-position">Position</label>
                <select id="pdf-pn-position">
                    <option value="bottom-center">Bottom center</option>
                    <option value="bottom-left">Bottom left</option>
                    <option value="bottom-right">Bottom right</option>
                    <option value="top-center">Top center</option>
                    <option value="top-left">Top left</option>
                    <option value="top-right">Top right</option>
                </select>
            </div>
            <div>
                <label for="pdf-pn-start">Start at</label>
                <input type="number" id="pdf-pn-start" value="1" min="1" class="pdf-page-input">
            </div>
            <div>
                <label for="pdf-pn-size">Font size</label>
                <input type="number" id="pdf-pn-size" value="12" min="8" max="72" class="pdf-page-input">
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-pn" disabled>Add numbers &amp; Download</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-pn-clear">Clear</button>
        </div>

        <div id="pdf-pn-status" class="alert alert-info hidden"></div>
        <div id="pdf-pn-error" class="alert alert-error hidden"></div>
    </div>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-page-numbers'); require_once __DIR__ . '/includes/footer.php'; ?>
