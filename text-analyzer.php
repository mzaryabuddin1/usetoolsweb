<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Text Analyzer',
    'Free online text analyzer. Count lines, bytes, unique words, average word length, and more.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Text Analyzer</h1>
        <p>Deep analysis of your text — lines, bytes, unique words, and reading stats.</p>
    </div>

    <div class="tool-panel">
        <label for="analyzer-text">Your text</label>
        <textarea id="analyzer-text" placeholder="Paste or type text here..."></textarea>

        <div class="stats-row">
            <div class="stat-box">
                <div class="value" id="an-words">0</div>
                <div class="label">Words</div>
            </div>
            <div class="stat-box">
                <div class="value" id="an-chars">0</div>
                <div class="label">Characters</div>
            </div>
            <div class="stat-box">
                <div class="value" id="an-lines">0</div>
                <div class="label">Lines</div>
            </div>
            <div class="stat-box">
                <div class="value" id="an-bytes">0</div>
                <div class="label">Bytes (UTF-8)</div>
            </div>
            <div class="stat-box">
                <div class="value" id="an-unique">0</div>
                <div class="label">Unique words</div>
            </div>
            <div class="stat-box">
                <div class="value" id="an-avg-len">0</div>
                <div class="label">Avg word length</div>
            </div>
            <div class="stat-box">
                <div class="value" id="an-longest">—</div>
                <div class="label">Longest word</div>
            </div>
            <div class="stat-box">
                <div class="value" id="an-reading">0 min</div>
                <div class="label">Reading time</div>
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-secondary" id="btn-an-clear">Clear</button>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/text-analyzer.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
