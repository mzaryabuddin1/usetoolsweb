<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Word Counter',
    'Free word counter tool. Count words, characters, sentences, paragraphs, and estimated reading time.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Word Counter</h1>
        <p>Paste or type your text below. Counts update automatically as you type.</p>
    </div>

    <div class="tool-panel">
        <label for="word-text">Your text</label>
        <textarea id="word-text" placeholder="Start typing or paste your text here..."></textarea>

        <div class="stats-row">
            <div class="stat-box">
                <div class="value" id="stat-words">0</div>
                <div class="label">Words</div>
            </div>
            <div class="stat-box">
                <div class="value" id="stat-chars">0</div>
                <div class="label">Characters</div>
            </div>
            <div class="stat-box">
                <div class="value" id="stat-chars-no-space">0</div>
                <div class="label">Chars (no spaces)</div>
            </div>
            <div class="stat-box">
                <div class="value" id="stat-sentences">0</div>
                <div class="label">Sentences</div>
            </div>
            <div class="stat-box">
                <div class="value" id="stat-paragraphs">0</div>
                <div class="label">Paragraphs</div>
            </div>
            <div class="stat-box">
                <div class="value" id="stat-reading">0 min</div>
                <div class="label">Reading time</div>
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-secondary" id="btn-clear-text">Clear</button>
            <button type="button" class="btn btn-secondary" id="btn-copy-text">Copy Text</button>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/word-counter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
