<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'URL Encoder Decoder',
    'Free online URL encoder and decoder. Encode or decode URLs and query strings safely.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>URL Encoder / Decoder</h1>
        <p>Encode special characters for URLs or decode percent-encoded strings.</p>
    </div>

    <div class="tool-panel">
        <label for="url-input">Input</label>
        <textarea id="url-input" placeholder="Enter URL or text to encode/decode"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-url-encode">Encode</button>
            <button type="button" class="btn btn-secondary" id="btn-url-decode">Decode</button>
            <button type="button" class="btn btn-secondary" id="btn-url-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-url-clear">Clear</button>
        </div>

        <div id="url-status" class="alert hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/url-encode-decode.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
