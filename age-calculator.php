<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Age Calculator',
    'Free age calculator. Calculate exact age in years, months, and days from your date of birth.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Age Calculator</h1>
        <p>Enter your date of birth to calculate your exact age in years, months, and days.</p>
    </div>

    <div class="tool-panel">
        <label for="age-dob">Date of birth</label>
        <input type="date" id="age-dob">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-age-calc">Calculate Age</button>
            <button type="button" class="btn btn-secondary" id="btn-age-clear">Clear</button>
        </div>

        <div id="age-result" class="stats-row hidden">
            <div class="stat-box">
                <div class="value" id="age-years">0</div>
                <div class="label">Years</div>
            </div>
            <div class="stat-box">
                <div class="value" id="age-months">0</div>
                <div class="label">Months</div>
            </div>
            <div class="stat-box">
                <div class="value" id="age-days">0</div>
                <div class="label">Days</div>
            </div>
            <div class="stat-box">
                <div class="value" id="age-total-days">0</div>
                <div class="label">Total days lived</div>
            </div>
        </div>

        <div id="age-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/age-calculator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
