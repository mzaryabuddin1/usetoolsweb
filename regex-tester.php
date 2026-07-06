<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Regex Tester',
    'Free regular expression tester. Test regex patterns with flags against sample text and see matches.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Regex Tester</h1>
        <p>Enter a regular expression pattern, flags, and test string to see all matches.</p>
    </div>

    <div class="tool-panel">
        <div class="form-row">
            <div>
                <label for="regex-pattern">Pattern</label>
                <input type="text" id="regex-pattern" placeholder="e.g. \d+">
            </div>
            <div>
                <label for="regex-flags">Flags</label>
                <input type="text" id="regex-flags" placeholder="e.g. gi" value="g">
            </div>
        </div>

        <label for="regex-test" style="margin-top:1rem;">Test string</label>
        <textarea id="regex-test" placeholder="Enter text to test against..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-regex-test">Test</button>
            <button type="button" class="btn btn-secondary" id="btn-regex-clear">Clear</button>
        </div>

        <div id="regex-results" class="hidden">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="value" id="regex-count">0</div>
                    <div class="label">Matches</div>
                </div>
            </div>
            <label style="margin-top:1rem;">Match details</label>
            <textarea id="regex-output" readonly></textarea>
        </div>

        <div id="regex-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/regex-tester.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
