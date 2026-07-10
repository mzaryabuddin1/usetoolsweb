<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('SEO Report Generator', 'Free SEO audit report — analyze any URL for title, meta tags, headings, links, and on-page SEO signals.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header"><h1>SEO Report</h1><p>Enter a public URL to generate an on-page SEO audit report. We fetch the page once and do not store results.</p></div>
    <div class="tool-panel dev-tool-panel" data-audit-type="seo">
        <label for="audit-url">Website URL</label>
        <input type="url" id="audit-url" placeholder="https://example.com">
        <div class="btn-row"><button type="button" class="btn btn-primary" id="btn-audit-run">Generate SEO report</button><button type="button" class="btn btn-secondary" id="btn-audit-download">Download report</button></div>
        <div id="audit-error" class="alert alert-error hidden"></div>
        <div id="audit-result" class="audit-result hidden"></div>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/site-audit-report.js"></script>';
require_once __DIR__ . '/includes/footer.php';
