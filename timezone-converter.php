<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Timezone Converter',
    'Free timezone converter. Convert date and time between common timezones.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Timezone Converter</h1>
        <p>Convert a date and time from one timezone to another.</p>
    </div>

    <div class="tool-panel">
        <label for="tz-datetime">Date &amp; time</label>
        <input type="datetime-local" id="tz-datetime" step="60">

        <div class="form-row" style="margin-top:1rem;">
            <div>
                <label for="tz-from">From timezone</label>
                <select id="tz-from"></select>
            </div>
            <div>
                <label for="tz-to">To timezone</label>
                <select id="tz-to"></select>
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-tz-convert">Convert</button>
            <button type="button" class="btn btn-secondary" id="btn-tz-now">Use now</button>
            <button type="button" class="btn btn-secondary" id="btn-tz-clear">Clear</button>
        </div>

        <div id="tz-result" class="stat-box hidden" style="margin-top:1.5rem;">
            <div class="value" id="tz-output">—</div>
            <div class="label">Converted time</div>
        </div>

        <div id="tz-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/timezone-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
