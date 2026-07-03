<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'JSON Formatter',
    'Free online JSON formatter and validator. Beautify, minify, or validate JSON instantly in your browser.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>JSON Formatter</h1>
        <p>Paste JSON below to validate, beautify, or minify. All processing happens locally.</p>
    </div>

    <div class="tool-panel">
        <label for="json-input">JSON input</label>
        <textarea id="json-input" placeholder='{"example": "Paste your JSON here"}'></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-beautify">Beautify</button>
            <button type="button" class="btn btn-secondary" id="btn-minify">Minify</button>
            <button type="button" class="btn btn-secondary" id="btn-validate">Validate</button>
            <button type="button" class="btn btn-secondary" id="btn-copy-json">Copy Output</button>
            <button type="button" class="btn btn-secondary" id="btn-clear-json">Clear</button>
        </div>

        <div id="json-status" class="alert hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/json-formatter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
