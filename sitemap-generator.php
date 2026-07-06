<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Sitemap Generator',
    'Free XML sitemap generator. Create a sitemap.xml from a list of URLs.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Sitemap Generator</h1>
        <p>Enter one URL per line to generate a valid XML sitemap file.</p>
    </div>

    <div class="tool-panel">
        <label for="sitemap-urls">URLs (one per line)</label>
        <textarea id="sitemap-urls" placeholder="https://example.com/&#10;https://example.com/about&#10;https://example.com/contact"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-sitemap-generate">Generate Sitemap</button>
            <button type="button" class="btn btn-secondary" id="btn-sitemap-download" disabled>Download XML</button>
            <button type="button" class="btn btn-secondary" id="btn-sitemap-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-sitemap-clear">Clear</button>
        </div>

        <label for="sitemap-output" style="margin-top:1.5rem;">Generated sitemap</label>
        <textarea id="sitemap-output" readonly placeholder="XML sitemap will appear here..."></textarea>

        <div id="sitemap-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/sitemap-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
