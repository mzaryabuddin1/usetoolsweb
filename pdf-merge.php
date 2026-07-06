<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'PDF Merge',
    'Free PDF merge tool. Combine multiple PDF files into one document in your browser.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>PDF Merge</h1>
        <p>Upload multiple PDF files and merge them into a single document. All processing happens locally.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-merge-input">Select PDF files</label>
        <input type="file" id="pdf-merge-input" accept=".pdf,application/pdf" multiple>

        <ul id="pdf-file-list" class="pdf-file-list hidden"></ul>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-merge" disabled>Merge &amp; Download</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-clear">Clear</button>
        </div>

        <div id="pdf-merge-status" class="alert alert-info hidden"></div>
        <div id="pdf-merge-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/pdf-merge.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
