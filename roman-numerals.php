<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Roman Numerals Converter',
    'Free Roman numerals converter. Convert numbers to Roman numerals and back (1–3999).'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Roman Numerals Converter</h1>
        <p>Convert numbers (1–3999) to Roman numerals or decode Roman numerals to numbers.</p>
    </div>

    <div class="tool-panel">
        <label for="roman-input">Input</label>
        <input type="text" id="roman-input" placeholder="e.g. 2024 or MMXXIV">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-roman-to">Number → Roman</button>
            <button type="button" class="btn btn-secondary" id="btn-roman-from">Roman → Number</button>
            <button type="button" class="btn btn-secondary" id="btn-roman-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-roman-clear">Clear</button>
        </div>

        <div id="roman-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/roman-numerals.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
