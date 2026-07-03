<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Random Number Generator',
    'Free random number generator. Generate random numbers within a custom min/max range.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Random Number Generator</h1>
        <p>Generate one or more random numbers within a specified range.</p>
    </div>

    <div class="tool-panel">
        <div class="form-row three-col">
            <div>
                <label for="rng-min">Minimum</label>
                <input type="number" id="rng-min" value="1">
            </div>
            <div>
                <label for="rng-max">Maximum</label>
                <input type="number" id="rng-max" value="100">
            </div>
            <div>
                <label for="rng-count">Count</label>
                <input type="number" id="rng-count" value="1" min="1" max="1000">
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-rng-generate">Generate</button>
            <button type="button" class="btn btn-secondary" id="btn-rng-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-rng-clear">Clear</button>
        </div>

        <label for="rng-output" style="margin-top:1.5rem;">Results</label>
        <textarea id="rng-output" readonly placeholder="Random numbers will appear here..."></textarea>

        <div id="rng-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/random-number-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
