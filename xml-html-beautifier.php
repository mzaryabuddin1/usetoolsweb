<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('XML & HTML Beautifier', 'Beautify or minify XML and HTML online. Format messy markup instantly in your browser.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header">
        <h1>XML &amp; HTML Beautifier</h1>
        <p>Format or minify XML and HTML markup. All processing happens locally in your browser.</p>
    </div>
    <div class="tool-panel dev-tool-panel">
        <div class="dev-tabs">
            <button type="button" class="dev-tab active" data-tab="html">HTML</button>
            <button type="button" class="dev-tab" data-tab="xml">XML</button>
        </div>
        <label for="markup-input">Input</label>
        <textarea id="markup-input" rows="12" placeholder="Paste HTML or XML here…"></textarea>
        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-markup-beautify">Beautify</button>
            <button type="button" class="btn btn-secondary" id="btn-markup-minify">Minify</button>
            <button type="button" class="btn btn-secondary" id="btn-markup-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-markup-clear">Clear</button>
        </div>
        <div id="markup-status" class="alert hidden"></div>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/xml-html-beautifier.js"></script>';
require_once __DIR__ . '/includes/footer.php';
