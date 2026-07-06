<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'CSS Minifier',
    'Free CSS minifier. Remove comments and unnecessary whitespace from CSS code.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>CSS Minifier</h1>
        <p>Paste CSS below to minify by removing comments and extra whitespace.</p>
    </div>

    <div class="tool-panel">
        <label for="css-input">CSS input</label>
        <textarea id="css-input" placeholder=".class { color: red; }"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-css-minify">Minify</button>
            <button type="button" class="btn btn-secondary" id="btn-css-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-css-clear">Clear</button>
        </div>

        <label for="css-output" style="margin-top:1.5rem;">Minified CSS</label>
        <textarea id="css-output" readonly placeholder="Minified output will appear here..."></textarea>

        <div id="css-stats" class="stats-row hidden">
            <div class="stat-box">
                <div class="value" id="css-before">0</div>
                <div class="label">Bytes before</div>
            </div>
            <div class="stat-box">
                <div class="value" id="css-after">0</div>
                <div class="label">Bytes after</div>
            </div>
            <div class="stat-box">
                <div class="value" id="css-saved">0%</div>
                <div class="label">Saved</div>
            </div>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/css-minifier.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
