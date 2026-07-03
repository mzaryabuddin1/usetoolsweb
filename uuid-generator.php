<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'UUID Generator',
    'Free online UUID generator. Create random UUID v4 identifiers instantly.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>UUID Generator</h1>
        <p>Generate random UUID v4 identifiers for databases, APIs, and applications.</p>
    </div>

    <div class="tool-panel">
        <label for="uuid-count">How many UUIDs?</label>
        <input type="number" id="uuid-count" min="1" max="50" value="1">

        <label for="uuid-uppercase" class="checkbox-inline" style="margin-top:1rem;">
            <input type="checkbox" id="uuid-uppercase"> Uppercase
        </label>

        <div class="uuid-output" id="uuid-output"></div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-generate-uuid">Generate</button>
            <button type="button" class="btn btn-secondary" id="btn-copy-uuid">Copy All</button>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/uuid-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
