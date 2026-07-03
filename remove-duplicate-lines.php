<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Remove Duplicate Lines',
    'Free tool to remove duplicate lines from text while preserving original order.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Remove Duplicate Lines</h1>
        <p>Paste your text below and remove duplicate lines while keeping the first occurrence of each line.</p>
    </div>

    <div class="tool-panel">
        <label for="dedupe-input">Your text</label>
        <textarea id="dedupe-input" placeholder="Paste lines here, one per line..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-dedupe">Remove Duplicates</button>
            <button type="button" class="btn btn-secondary" id="btn-dedupe-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-dedupe-clear">Clear</button>
        </div>

        <div id="dedupe-stats" class="stats-row hidden">
            <div class="stat-box">
                <div class="value" id="dedupe-before">0</div>
                <div class="label">Lines before</div>
            </div>
            <div class="stat-box">
                <div class="value" id="dedupe-after">0</div>
                <div class="label">Lines after</div>
            </div>
            <div class="stat-box">
                <div class="value" id="dedupe-removed">0</div>
                <div class="label">Removed</div>
            </div>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/remove-duplicate-lines.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
