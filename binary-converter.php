<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Binary Converter',
    'Free binary converter. Convert text to 8-bit ASCII binary and decode binary back to text.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Binary Converter</h1>
        <p>Convert text to 8-bit binary or decode binary strings back to text.</p>
    </div>

    <div class="tool-panel">
        <label for="binary-input">Input</label>
        <textarea id="binary-input" placeholder="Enter text or binary (space-separated bytes)..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-binary-encode">Text → Binary</button>
            <button type="button" class="btn btn-secondary" id="btn-binary-decode">Binary → Text</button>
            <button type="button" class="btn btn-secondary" id="btn-binary-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-binary-clear">Clear</button>
        </div>

        <div id="binary-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/binary-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
