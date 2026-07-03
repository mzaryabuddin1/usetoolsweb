<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'BMI Calculator',
    'Free BMI calculator. Calculate Body Mass Index from height in cm and weight in kg with category.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>BMI Calculator</h1>
        <p>Enter your height and weight to calculate your Body Mass Index and category.</p>
    </div>

    <div class="tool-panel">
        <div class="form-row">
            <div>
                <label for="bmi-height">Height (cm)</label>
                <input type="number" id="bmi-height" placeholder="e.g. 175" min="1">
            </div>
            <div>
                <label for="bmi-weight">Weight (kg)</label>
                <input type="number" id="bmi-weight" placeholder="e.g. 70" min="1">
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-bmi-calc">Calculate BMI</button>
            <button type="button" class="btn btn-secondary" id="btn-bmi-clear">Clear</button>
        </div>

        <div id="bmi-result" class="stats-row hidden">
            <div class="stat-box">
                <div class="value" id="bmi-value">—</div>
                <div class="label">BMI</div>
            </div>
            <div class="stat-box">
                <div class="value" id="bmi-category">—</div>
                <div class="label">Category</div>
            </div>
        </div>

        <div id="bmi-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/bmi-calculator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
