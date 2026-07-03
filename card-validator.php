<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Credit Card Validator',
    'Free credit card validator. Validate card numbers with the Luhn algorithm and detect card type.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Credit Card Validator</h1>
        <p>Enter a credit card number to validate with the Luhn algorithm and detect the card type.</p>
    </div>

    <div class="tool-panel">
        <label for="card-number">Card number</label>
        <input type="text" id="card-number" placeholder="e.g. 4111 1111 1111 1111" maxlength="23">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-card-validate">Validate</button>
            <button type="button" class="btn btn-secondary" id="btn-card-clear">Clear</button>
        </div>

        <div id="card-result" class="hidden">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="value" id="card-type">—</div>
                    <div class="label">Card type</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="card-valid">—</div>
                    <div class="label">Luhn check</div>
                </div>
            </div>
        </div>

        <div id="card-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/card-validator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
