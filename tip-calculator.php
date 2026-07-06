<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Tip Calculator',
    'Free tip calculator. Calculate tip amount and split the bill between multiple people.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Tip Calculator</h1>
        <p>Enter bill amount, tip percentage, and number of people to split the total.</p>
    </div>

    <div class="tool-panel">
        <div class="form-row three-col">
            <div>
                <label for="tip-bill">Bill amount</label>
                <input type="number" id="tip-bill" placeholder="e.g. 50.00" min="0" step="0.01">
            </div>
            <div>
                <label for="tip-percent">Tip (%)</label>
                <input type="number" id="tip-percent" placeholder="e.g. 15" min="0" value="15">
            </div>
            <div>
                <label for="tip-people">Split between</label>
                <input type="number" id="tip-people" placeholder="e.g. 2" min="1" value="1">
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-tip-calc">Calculate</button>
            <button type="button" class="btn btn-secondary" id="btn-tip-clear">Clear</button>
        </div>

        <div id="tip-result" class="stats-row hidden">
            <div class="stat-box">
                <div class="value" id="tip-amount">$0</div>
                <div class="label">Tip amount</div>
            </div>
            <div class="stat-box">
                <div class="value" id="tip-total">$0</div>
                <div class="label">Total with tip</div>
            </div>
            <div class="stat-box">
                <div class="value" id="tip-per-person">$0</div>
                <div class="label">Per person</div>
            </div>
        </div>

        <div id="tip-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/tip-calculator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
