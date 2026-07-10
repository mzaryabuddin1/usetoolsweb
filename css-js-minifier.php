<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('CSS & JS Minifier', 'Minify CSS and JavaScript online — reduce file size by removing whitespace and comments.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header"><h1>CSS &amp; JS Minifier</h1><p>Minify CSS and JavaScript in your browser. No data is sent to the server.</p></div>
    <div class="tool-panel dev-tool-panel">
        <div class="dev-tabs">
            <button type="button" class="dev-tab active" data-tab="css">CSS</button>
            <button type="button" class="dev-tab" data-tab="js">JavaScript</button>
        </div>
        <label for="minify-input">Input</label>
        <textarea id="minify-input" rows="12" placeholder="Paste CSS or JavaScript…"></textarea>
        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-minify-run">Minify</button>
            <button type="button" class="btn btn-secondary" id="btn-minify-copy">Copy output</button>
            <button type="button" class="btn btn-secondary" id="btn-minify-clear">Clear</button>
        </div>
        <label for="minify-output">Output</label>
        <textarea id="minify-output" rows="10" readonly></textarea>
        <p id="minify-stats" class="hint hidden"></p>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/css-js-minifier.js"></script>';
require_once __DIR__ . '/includes/footer.php';
