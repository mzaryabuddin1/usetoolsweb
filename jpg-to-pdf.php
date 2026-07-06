<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('JPG to PDF', 'Convert JPG and PNG images to a single PDF document in your browser.');
$extra_head = pdf_tool_head();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>JPG to PDF</h1>
        <p>Convert JPG or PNG images into a PDF. Adjust page size, orientation, and margins.</p>
    </div>

    <div class="tool-panel">
        <label for="jpg-pdf-input">Select images (JPG, PNG)</label>
        <input type="file" id="jpg-pdf-input" accept="image/jpeg,image/png,.jpg,.jpeg,.png" multiple>

        <ul id="jpg-pdf-list" class="pdf-file-list hidden"></ul>

        <div class="form-row">
            <fieldset class="pdf-fieldset">
                <legend>Page size</legend>
                <label class="radio-label"><input type="radio" name="jpg-page-size" value="fit" checked> Fit to image</label>
                <label class="radio-label"><input type="radio" name="jpg-page-size" value="a4"> A4</label>
                <label class="radio-label"><input type="radio" name="jpg-page-size" value="letter"> Letter</label>
            </fieldset>
            <fieldset class="pdf-fieldset">
                <legend>Orientation</legend>
                <label class="radio-label"><input type="radio" name="jpg-orientation" value="portrait" checked> Portrait</label>
                <label class="radio-label"><input type="radio" name="jpg-orientation" value="landscape"> Landscape</label>
            </fieldset>
        </div>

        <label for="jpg-pdf-margin">Margin (points)</label>
        <input type="number" id="jpg-pdf-margin" value="36" min="0" max="200" class="pdf-page-input">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-jpg-pdf" disabled>Create PDF</button>
            <button type="button" class="btn btn-secondary" id="btn-jpg-pdf-clear">Clear</button>
        </div>

        <div id="jpg-pdf-status" class="alert alert-info hidden"></div>
        <div id="jpg-pdf-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot('Ad space'); ?>
</div>

<?php
$extra_scripts = pdf_tool_script('jpg-to-pdf');
require_once __DIR__ . '/includes/footer.php';
?>
