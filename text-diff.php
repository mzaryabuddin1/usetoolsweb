<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Text Diff',
    'Free text diff tool. Compare two texts side by side and highlight added and removed lines.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Text Diff</h1>
        <p>Compare two texts and see line-by-line differences with added and removed highlighting.</p>
    </div>

    <div class="tool-panel">
        <div class="diff-columns">
            <div>
                <label for="diff-original">Original text</label>
                <textarea id="diff-original" placeholder="Paste original text..."></textarea>
            </div>
            <div>
                <label for="diff-modified">Modified text</label>
                <textarea id="diff-modified" placeholder="Paste modified text..."></textarea>
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-diff">Compare</button>
            <button type="button" class="btn btn-secondary" id="btn-diff-clear">Clear</button>
        </div>

        <div id="diff-output-wrap" class="hidden" style="margin-top:1.5rem;">
            <label>Diff result</label>
            <div id="diff-output" class="diff-output"></div>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/text-diff.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
