<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'CSV to JSON',
    'Free CSV to JSON converter. Paste CSV data and convert to JSON format instantly.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>CSV to JSON</h1>
        <p>Paste CSV data below. First row is treated as column headers.</p>
    </div>

    <div class="tool-panel">
        <label for="csv-input">CSV input</label>
        <textarea id="csv-input" placeholder="name,email&#10;Alice,alice@example.com&#10;Bob,bob@example.com"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-csv-convert">Convert to JSON</button>
            <button type="button" class="btn btn-secondary" id="btn-csv-copy">Copy JSON</button>
            <button type="button" class="btn btn-secondary" id="btn-csv-clear">Clear</button>
        </div>

        <label for="csv-output" style="margin-top:1.5rem;">JSON output</label>
        <textarea id="csv-output" readonly placeholder="JSON will appear here..."></textarea>

        <div id="csv-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/csv-to-json.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
