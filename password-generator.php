<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Password Generator',
    'Free online password generator. Create strong, secure random passwords with custom length and character options.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Password Generator</h1>
        <p>Generate strong random passwords. Adjust length and character types below.</p>
    </div>

    <div class="tool-panel">
        <div class="password-output-wrap">
            <input type="text" id="password-output" readonly placeholder="Click Generate to create a password">
            <button type="button" class="btn btn-secondary btn-icon" id="btn-copy-password" title="Copy">Copy</button>
        </div>

        <label for="pw-length">Length: <span id="pw-length-value">16</span></label>
        <input type="range" id="pw-length" min="4" max="64" value="16">

        <div class="checkbox-row">
            <label><input type="checkbox" id="pw-upper" checked> Uppercase (A-Z)</label>
            <label><input type="checkbox" id="pw-lower" checked> Lowercase (a-z)</label>
            <label><input type="checkbox" id="pw-numbers" checked> Numbers (0-9)</label>
            <label><input type="checkbox" id="pw-symbols" checked> Symbols (!@#$…)</label>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-generate-password">Generate Password</button>
        </div>

        <div id="pw-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/password-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
