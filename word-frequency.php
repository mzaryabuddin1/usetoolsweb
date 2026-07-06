<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Word Frequency Counter',
    'Free word frequency counter. Count how often each word appears in your text.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Word Frequency Counter</h1>
        <p>Paste text below to see a sorted table of word frequencies.</p>
    </div>

    <div class="tool-panel">
        <label for="freq-input">Your text</label>
        <textarea id="freq-input" placeholder="Paste or type text here..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-freq-analyze">Analyze</button>
            <button type="button" class="btn btn-secondary" id="btn-freq-clear">Clear</button>
        </div>

        <div id="freq-table-wrap" class="hidden" style="margin-top:1.5rem;">
            <table class="freq-table">
                <thead>
                    <tr><th>Word</th><th>Count</th></tr>
                </thead>
                <tbody id="freq-table-body"></tbody>
            </table>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/word-frequency.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
