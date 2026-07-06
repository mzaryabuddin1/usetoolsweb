<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'HTML/JS Minifier',
    'Free HTML and JavaScript minifier. Remove comments and unnecessary whitespace.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>HTML / JS Minifier</h1>
        <p>Minify HTML or JavaScript by removing comments and extra whitespace.</p>
    </div>

    <div class="tool-panel">
        <div class="btn-row" style="margin-top:0;">
            <button type="button" class="btn btn-secondary code-tab active" data-tab="html">HTML</button>
            <button type="button" class="btn btn-secondary code-tab" data-tab="js">JavaScript</button>
        </div>

        <label for="code-input">Input</label>
        <textarea id="code-input" placeholder="Paste HTML or JavaScript here..."></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-code-minify">Minify</button>
            <button type="button" class="btn btn-secondary" id="btn-code-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-code-clear">Clear</button>
        </div>

        <label for="code-output" style="margin-top:1.5rem;">Minified output</label>
        <textarea id="code-output" readonly placeholder="Minified output will appear here..."></textarea>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/code-minifier.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
