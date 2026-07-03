<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Unix Timestamp Converter',
    'Free Unix timestamp converter. Convert epoch timestamps to readable dates and back.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Timestamp Converter</h1>
        <p>Convert Unix timestamps to human-readable dates and vice versa.</p>
    </div>

    <div class="tool-panel">
        <div class="stat-box" style="margin-bottom:1.5rem;">
            <div class="value" id="current-timestamp">—</div>
            <div class="label">Current Unix timestamp</div>
            <button type="button" class="btn btn-secondary btn-sm" id="btn-use-now" style="margin-top:0.5rem;">Use current time</button>
        </div>

        <label for="ts-unix">Unix timestamp (seconds)</label>
        <input type="text" id="ts-unix" placeholder="e.g. 1710000000">

        <label for="ts-date" style="margin-top:1rem;">Date &amp; time (local)</label>
        <input type="datetime-local" id="ts-date" step="1">

        <label for="ts-utc" style="margin-top:1rem;">UTC string</label>
        <input type="text" id="ts-utc" readonly placeholder="UTC representation">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-ts-convert">Convert</button>
            <button type="button" class="btn btn-secondary" id="btn-ts-clear">Clear</button>
        </div>

        <div id="ts-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/timestamp-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
