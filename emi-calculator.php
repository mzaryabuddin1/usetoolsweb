<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'EMI Calculator',
    'Free EMI calculator. Calculate monthly loan EMI, total interest, and total payment.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>EMI Calculator</h1>
        <p>Calculate your monthly Equated Monthly Installment (EMI) for a loan.</p>
    </div>

    <div class="tool-panel">
        <label for="emi-amount">Loan amount</label>
        <input type="number" id="emi-amount" placeholder="e.g. 100000" min="1">

        <div class="form-row" style="margin-top:1rem;">
            <div>
                <label for="emi-rate">Annual interest rate (%)</label>
                <input type="number" id="emi-rate" placeholder="e.g. 8.5" min="0" step="0.01">
            </div>
            <div>
                <label for="emi-tenure">Tenure</label>
                <input type="number" id="emi-tenure" placeholder="e.g. 12" min="1">
            </div>
        </div>

        <label for="emi-tenure-type" style="margin-top:1rem;">Tenure unit</label>
        <select id="emi-tenure-type">
            <option value="months">Months</option>
            <option value="years">Years</option>
        </select>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-emi-calc">Calculate EMI</button>
            <button type="button" class="btn btn-secondary" id="btn-emi-clear">Clear</button>
        </div>

        <div id="emi-result" class="stats-row hidden">
            <div class="stat-box">
                <div class="value" id="emi-monthly">—</div>
                <div class="label">Monthly EMI</div>
            </div>
            <div class="stat-box">
                <div class="value" id="emi-interest">—</div>
                <div class="label">Total interest</div>
            </div>
            <div class="stat-box">
                <div class="value" id="emi-total">—</div>
                <div class="label">Total payment</div>
            </div>
        </div>

        <div id="emi-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/emi-calculator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
