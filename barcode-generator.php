<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Barcode Generator',
    'Free barcode generator. Create CODE128 barcodes from text and download as PNG.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Barcode Generator</h1>
        <p>Enter text to generate a CODE128 barcode. Download as PNG when ready.</p>
    </div>

    <div class="tool-panel">
        <label for="barcode-text">Text / value</label>
        <input type="text" id="barcode-text" placeholder="e.g. 1234567890">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-barcode-generate">Generate</button>
            <button type="button" class="btn btn-secondary" id="btn-barcode-download" disabled>Download PNG</button>
            <button type="button" class="btn btn-secondary" id="btn-barcode-clear">Clear</button>
        </div>

        <div class="preview-area">
            <svg id="barcode-canvas"></svg>
        </div>

        <div id="barcode-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/barcode-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
