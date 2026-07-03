<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'IBAN Validator',
    'Free IBAN validator. Validate International Bank Account Numbers using mod-97 check.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>IBAN Validator</h1>
        <p>Enter an IBAN to validate its format and mod-97 checksum.</p>
    </div>

    <div class="tool-panel">
        <label for="iban-input">IBAN</label>
        <input type="text" id="iban-input" placeholder="e.g. GB82 WEST 1234 5698 7654 32">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-iban-validate">Validate</button>
            <button type="button" class="btn btn-secondary" id="btn-iban-clear">Clear</button>
        </div>

        <div id="iban-result" class="hidden">
            <div id="iban-status" class="alert"></div>
            <div class="stats-row">
                <div class="stat-box">
                    <div class="value" id="iban-country">—</div>
                    <div class="label">Country code</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="iban-length">—</div>
                    <div class="label">Length</div>
                </div>
            </div>
        </div>

        <div id="iban-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/iban-validator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
