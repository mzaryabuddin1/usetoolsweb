<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Percentage Calculator',
    'Free percentage calculator. Find X% of Y, what percentage X is of Y, and increase or decrease by percentage.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Percentage Calculator</h1>
        <p>Calculate percentages, find what percent one number is of another, or apply increase/decrease.</p>
    </div>

    <div class="tool-panel">
        <div class="btn-row" style="margin-top:0;">
            <button type="button" class="btn btn-secondary pct-mode active" data-mode="of">X% of Y</button>
            <button type="button" class="btn btn-secondary pct-mode" data-mode="is">What % is X of Y</button>
            <button type="button" class="btn btn-secondary pct-mode" data-mode="change">Increase / Decrease</button>
        </div>

        <div id="pct-panel-of" class="pct-panel">
            <div class="form-row">
                <div>
                    <label for="pct-of-percent">Percentage (X)</label>
                    <input type="number" id="pct-of-percent" placeholder="e.g. 15">
                </div>
                <div>
                    <label for="pct-of-value">Of value (Y)</label>
                    <input type="number" id="pct-of-value" placeholder="e.g. 200">
                </div>
            </div>
        </div>

        <div id="pct-panel-is" class="pct-panel hidden">
            <div class="form-row">
                <div>
                    <label for="pct-is-x">Value (X)</label>
                    <input type="number" id="pct-is-x" placeholder="e.g. 25">
                </div>
                <div>
                    <label for="pct-is-y">Of total (Y)</label>
                    <input type="number" id="pct-is-y" placeholder="e.g. 200">
                </div>
            </div>
        </div>

        <div id="pct-panel-change" class="pct-panel hidden">
            <div class="form-row three-col">
                <div>
                    <label for="pct-change-value">Original value</label>
                    <input type="number" id="pct-change-value" placeholder="e.g. 100">
                </div>
                <div>
                    <label for="pct-change-percent">Percentage</label>
                    <input type="number" id="pct-change-percent" placeholder="e.g. 10">
                </div>
                <div>
                    <label for="pct-change-type">Type</label>
                    <select id="pct-change-type">
                        <option value="increase">Increase by %</option>
                        <option value="decrease">Decrease by %</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pct-calc">Calculate</button>
            <button type="button" class="btn btn-secondary" id="btn-pct-clear">Clear</button>
        </div>

        <div id="pct-result" class="stats-row hidden">
            <div class="stat-box">
                <div class="value" id="pct-result-value">—</div>
                <div class="label" id="pct-result-label">Result</div>
            </div>
        </div>

        <div id="pct-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/percentage-calculator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
