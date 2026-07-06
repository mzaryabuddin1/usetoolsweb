<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Scan to PDF', 'Turn phone photos or scanned images into a PDF document.');
$extra_head = pdf_tool_head();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Scan to PDF</h1>
        <p>Upload photos from your phone or scanner and combine them into one PDF — great for receipts, notes, and documents.</p>
    </div>

    <div class="tool-panel">
        <label for="jpg-pdf-input">Select photos (JPG, PNG)</label>
        <input type="file" id="jpg-pdf-input" accept="image/jpeg,image/png,.jpg,.jpeg,.png" multiple capture="environment">

        <ul id="jpg-pdf-list" class="pdf-file-list hidden"></ul>

        <input type="hidden" name="jpg-page-size" value="a4">
        <input type="hidden" name="jpg-orientation" value="portrait">
        <input type="hidden" id="jpg-pdf-margin" value="24">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-jpg-pdf" disabled>Create PDF</button>
            <button type="button" class="btn btn-secondary" id="btn-jpg-pdf-clear">Clear</button>
        </div>

        <div id="jpg-pdf-status" class="alert alert-info hidden"></div>
        <div id="jpg-pdf-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot('Ad space'); ?>
</div>

<?php
$extra_scripts = pdf_tool_script('jpg-to-pdf');
require_once __DIR__ . '/includes/footer.php';
?>
