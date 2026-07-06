<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Base64 Encoder Decoder',
    'Free online Base64 encoder and decoder. Encode or decode text to Base64 instantly in your browser.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Base64 Encoder / Decoder</h1>
        <p>Encode plain text to Base64 or decode Base64 back to text. All processing is local.</p>
    </div>

    <div class="tool-panel">
        <label for="base64-input">Input</label>
        <textarea id="base64-input" placeholder="Enter text to encode or Base64 string to decode"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-base64-encode">Encode</button>
            <button type="button" class="btn btn-secondary" id="btn-base64-decode">Decode</button>
            <button type="button" class="btn btn-secondary" id="btn-base64-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-base64-clear">Clear</button>
        </div>

        <div id="base64-status" class="alert hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/base64-encode-decode.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
