<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Reverse Text',
    'Free reverse text tool. Reverse characters, words, or lines in your text.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Reverse Text</h1>
        <p>Reverse your text by characters, words, or lines.</p>
    </div>

    <div class="tool-panel">
        <label for="reverse-input">Your text</label>
        <textarea id="reverse-input" placeholder="Enter text to reverse..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" data-reverse="chars">Reverse Characters</button>
            <button type="button" class="btn btn-secondary" data-reverse="words">Reverse Words</button>
            <button type="button" class="btn btn-secondary" data-reverse="lines">Reverse Lines</button>
            <button type="button" class="btn btn-secondary" id="btn-reverse-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-reverse-clear">Clear</button>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/reverse-text.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
