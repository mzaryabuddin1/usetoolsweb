<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Slug Generator',
    'Free URL slug generator. Convert titles to SEO-friendly URL slugs instantly.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Slug Generator</h1>
        <p>Enter a title to generate a clean, URL-friendly slug.</p>
    </div>

    <div class="tool-panel">
        <label for="slug-title">Title</label>
        <input type="text" id="slug-title" placeholder="My Blog Post Title!">

        <label for="slug-output" style="margin-top:1rem;">Generated slug</label>
        <input type="text" id="slug-output" readonly placeholder="my-blog-post-title">

        <div class="btn-row">
            <button type="button" class="btn btn-secondary" id="btn-slug-copy">Copy Slug</button>
            <button type="button" class="btn btn-secondary" id="btn-slug-clear">Clear</button>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/slug-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
