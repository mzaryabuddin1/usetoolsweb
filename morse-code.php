<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Morse Code Translator',
    'Free Morse code translator. Convert text to Morse code and decode Morse back to text.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Morse Code Translator</h1>
        <p>Convert text to Morse code or decode Morse code to plain text.</p>
    </div>

    <div class="tool-panel">
        <label for="morse-input">Input</label>
        <textarea id="morse-input" placeholder="Enter text or Morse code (use . - and spaces)..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-morse-encode">Text → Morse</button>
            <button type="button" class="btn btn-secondary" id="btn-morse-decode">Morse → Text</button>
            <button type="button" class="btn btn-secondary" id="btn-morse-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-morse-clear">Clear</button>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/morse-code.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
