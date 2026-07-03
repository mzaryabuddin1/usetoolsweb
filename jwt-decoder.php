<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'JWT Decoder',
    'Free JWT decoder. Decode JSON Web Token headers and payloads without verification.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>JWT Decoder</h1>
        <p>Paste a JWT token to decode its header and payload. Signature is not verified.</p>
    </div>

    <div class="tool-panel">
        <label for="jwt-input">JWT token</label>
        <textarea id="jwt-input" placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-jwt-decode">Decode</button>
            <button type="button" class="btn btn-secondary" id="btn-jwt-clear">Clear</button>
        </div>

        <div id="jwt-results" class="hidden">
            <label style="margin-top:1.5rem;">Header</label>
            <textarea id="jwt-header" readonly></textarea>
            <label style="margin-top:1rem;">Payload</label>
            <textarea id="jwt-payload" readonly></textarea>
        </div>

        <div id="jwt-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/jwt-decoder.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
