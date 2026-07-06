<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Robots.txt Generator',
    'Free robots.txt generator. Create robots.txt with allow/disallow rules and sitemap URL.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Robots.txt Generator</h1>
        <p>Configure allow/disallow paths and sitemap URL to generate a robots.txt file.</p>
    </div>

    <div class="tool-panel">
        <label for="robots-user-agent">User-agent</label>
        <input type="text" id="robots-user-agent" value="*" placeholder="*">

        <label for="robots-disallow" style="margin-top:1rem;">Disallow paths (one per line)</label>
        <textarea id="robots-disallow" placeholder="/admin/&#10;/private/" style="min-height:80px;"></textarea>

        <label for="robots-allow" style="margin-top:1rem;">Allow paths (one per line, optional)</label>
        <textarea id="robots-allow" placeholder="/public/" style="min-height:80px;"></textarea>

        <label for="robots-sitemap" style="margin-top:1rem;">Sitemap URL</label>
        <input type="text" id="robots-sitemap" placeholder="https://example.com/sitemap.xml">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-robots-generate">Generate</button>
            <button type="button" class="btn btn-secondary" id="btn-robots-download" disabled>Download</button>
            <button type="button" class="btn btn-secondary" id="btn-robots-copy">Copy</button>
            <button type="button" class="btn btn-secondary" id="btn-robots-clear">Clear</button>
        </div>

        <label for="robots-output" style="margin-top:1.5rem;">Generated robots.txt</label>
        <textarea id="robots-output" readonly placeholder="robots.txt will appear here..."></textarea>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/robots-txt-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
