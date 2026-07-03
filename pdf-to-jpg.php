<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'PDF to JPG',
    'Free PDF to JPG converter. Convert PDF pages to JPG images in your browser.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>PDF to JPG</h1>
        <p>Upload a PDF file and convert each page to a JPG image. Processing happens locally in your browser.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-jpg-input">Select PDF file</label>
        <input type="file" id="pdf-jpg-input" accept=".pdf,application/pdf">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-jpg-convert" disabled>Convert to JPG</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-jpg-clear">Clear</button>
        </div>

        <div id="pdf-jpg-status" class="alert alert-info hidden"></div>
        <div id="pdf-jpg-preview" class="preview-area hidden"></div>
        <div id="pdf-jpg-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/pdf-to-jpg.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
