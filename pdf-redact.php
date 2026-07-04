<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Redact PDF', 'Permanently black out sensitive areas in a PDF.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Redact PDF</h1>
        <p>Cover sensitive content with black boxes. One area per line: <code>page,x,y,width,height</code> (points, origin bottom-left).</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-redact-input">Select PDF</label>
        <input type="file" id="pdf-redact-input" accept=".pdf,application/pdf">

        <label for="pdf-redact-areas">Redaction areas</label>
        <textarea id="pdf-redact-areas" rows="5" placeholder="1,100,200,150,24&#10;2,50,400,200,30"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-redact" disabled>Redact &amp; Download</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-redact-clear">Clear</button>
        </div>

        <div id="pdf-redact-status" class="alert alert-info hidden"></div>
        <div id="pdf-redact-error" class="alert alert-error hidden"></div>
    </div>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-redact'); require_once __DIR__ . '/includes/footer.php'; ?>
