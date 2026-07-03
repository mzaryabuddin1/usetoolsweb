<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Hash Generator',
    'Free online hash generator. Create MD5, SHA-1, SHA-256, and SHA-512 hashes from text instantly.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Hash Generator</h1>
        <p>Generate cryptographic hashes from any text. All hashing runs locally in your browser.</p>
    </div>

    <div class="tool-panel">
        <label for="hash-input">Input text</label>
        <textarea id="hash-input" placeholder="Enter text to hash..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-hash-generate">Generate Hashes</button>
            <button type="button" class="btn btn-secondary" id="btn-hash-clear">Clear</button>
        </div>

        <div id="hash-results" class="hash-results hidden">
            <div class="hash-row">
                <label>MD5</label>
                <div class="hash-value-wrap">
                    <code id="hash-md5"></code>
                    <button type="button" class="btn btn-secondary btn-sm btn-copy-hash" data-target="hash-md5">Copy</button>
                </div>
            </div>
            <div class="hash-row">
                <label>SHA-1</label>
                <div class="hash-value-wrap">
                    <code id="hash-sha1"></code>
                    <button type="button" class="btn btn-secondary btn-sm btn-copy-hash" data-target="hash-sha1">Copy</button>
                </div>
            </div>
            <div class="hash-row">
                <label>SHA-256</label>
                <div class="hash-value-wrap">
                    <code id="hash-sha256"></code>
                    <button type="button" class="btn btn-secondary btn-sm btn-copy-hash" data-target="hash-sha256">Copy</button>
                </div>
            </div>
            <div class="hash-row">
                <label>SHA-512</label>
                <div class="hash-value-wrap">
                    <code id="hash-sha512"></code>
                    <button type="button" class="btn btn-secondary btn-sm btn-copy-hash" data-target="hash-sha512">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/hash-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
