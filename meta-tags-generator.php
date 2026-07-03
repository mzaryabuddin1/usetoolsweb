<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Meta Tags Generator',
    'Free meta tags generator for SEO and social sharing. Generate HTML meta tags from title, description, URL, and image.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Meta Tags Generator</h1>
        <p>Fill in the fields below to generate HTML meta tags for SEO and Open Graph social sharing.</p>
    </div>

    <div class="tool-panel">
        <label for="meta-title">Page title</label>
        <input type="text" id="meta-title" placeholder="My Awesome Page">

        <label for="meta-description" style="margin-top:1rem;">Description</label>
        <textarea id="meta-description" placeholder="A brief description of your page..." style="min-height:80px;"></textarea>

        <label for="meta-url" style="margin-top:1rem;">URL</label>
        <input type="text" id="meta-url" placeholder="https://example.com/page">

        <label for="meta-image" style="margin-top:1rem;">Image URL</label>
        <input type="text" id="meta-image" placeholder="https://example.com/image.jpg">

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-meta-generate">Generate</button>
            <button type="button" class="btn btn-secondary" id="btn-meta-copy">Copy HTML</button>
            <button type="button" class="btn btn-secondary" id="btn-meta-clear">Clear</button>
        </div>

        <label for="meta-output" style="margin-top:1.5rem;">Generated meta tags</label>
        <textarea id="meta-output" readonly placeholder="Meta tags will appear here..."></textarea>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/meta-tags-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
