<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'HTML Encoder/Decoder',
    'Free HTML entity encoder and decoder. Escape or unescape HTML special characters in text.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>HTML Encoder / Decoder</h1>
        <p>Encode text to HTML entities or decode HTML entities back to plain text.</p>
    </div>

    <div class="tool-panel">
        <label for="html-input">Input</label>
        <textarea id="html-input" placeholder="Enter text to encode or decode..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-html-encode">Encode</button>
            <button type="button" class="btn btn-secondary" id="btn-html-decode">Decode</button>
            <button type="button" class="btn btn-secondary" id="btn-html-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-html-clear">Clear</button>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/html-encode-decode.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
