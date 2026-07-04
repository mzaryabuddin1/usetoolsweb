<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Split PDF', 'Split a PDF into separate files by every page or custom page ranges. Free and private.');
$extra_head = pdf_tool_head(false, true);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Split PDF</h1>
        <p>Separate one page or a whole set into independent PDF files.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-split-input">Select PDF file</label>
        <input type="file" id="pdf-split-input" accept=".pdf,application/pdf">

        <div id="pdf-split-info" class="pdf-meta hidden">Pages in document: <strong id="pdf-split-page-count">0</strong></div>

        <fieldset class="pdf-fieldset">
            <legend>Split mode</legend>
            <label class="radio-label"><input type="radio" name="split-mode" value="each" checked> Split into individual pages (ZIP)</label>
            <label class="radio-label"><input type="radio" name="split-mode" value="ranges"> Split by ranges</label>
        </fieldset>

        <label for="pdf-split-ranges">Page ranges (when using ranges)</label>
        <input type="text" id="pdf-split-ranges" placeholder="e.g. 1-3;4-6;7" class="pdf-page-input">
        <p class="hint">Use commas within a range group and semicolons between groups. Example: <code>1-3;4-6;7</code></p>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-split" disabled>Split &amp; Download</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-split-clear">Clear</button>
        </div>

        <div id="pdf-split-status" class="alert alert-info hidden"></div>
        <div id="pdf-split-error" class="alert alert-error hidden"></div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space</div>
</div>

<?php
$extra_scripts = pdf_tool_script('pdf-split');
require_once __DIR__ . '/includes/footer.php';
?>
