<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Color Converter',
    'Free online color converter. Convert between HEX, RGB, and HSL with a live color preview.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Color Converter</h1>
        <p>Convert colors between HEX, RGB, and HSL. Pick a color or type values manually.</p>
    </div>

    <div class="tool-panel">
        <div class="color-preview-row">
            <div id="color-preview" class="color-preview"></div>
            <input type="color" id="color-picker" value="#2563eb">
        </div>

        <div class="form-row three-col">
            <div>
                <label for="color-hex">HEX</label>
                <input type="text" id="color-hex" placeholder="#2563eb" maxlength="7">
            </div>
            <div>
                <label for="color-rgb">RGB</label>
                <input type="text" id="color-rgb" placeholder="rgb(37, 99, 235)">
            </div>
            <div>
                <label for="color-hsl">HSL</label>
                <input type="text" id="color-hsl" placeholder="hsl(221, 83%, 53%)">
            </div>
        </div>

        <div id="color-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/color-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
