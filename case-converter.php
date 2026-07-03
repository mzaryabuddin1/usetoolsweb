<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Case Converter',
    'Free online case converter. Convert text to uppercase, lowercase, title case, sentence case, and more.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Case Converter</h1>
        <p>Paste your text and convert it to different letter cases instantly.</p>
    </div>

    <div class="tool-panel">
        <label for="case-input">Your text</label>
        <textarea id="case-input" placeholder="Paste or type text here..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-secondary" data-case="upper">UPPERCASE</button>
            <button type="button" class="btn btn-secondary" data-case="lower">lowercase</button>
            <button type="button" class="btn btn-secondary" data-case="title">Title Case</button>
            <button type="button" class="btn btn-secondary" data-case="sentence">Sentence case</button>
            <button type="button" class="btn btn-secondary" data-case="camel">camelCase</button>
            <button type="button" class="btn btn-secondary" data-case="snake">snake_case</button>
            <button type="button" class="btn btn-secondary" data-case="kebab">kebab-case</button>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-secondary" id="btn-case-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-case-clear">Clear</button>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/case-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
