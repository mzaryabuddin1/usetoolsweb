<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('Lighthouse Report', 'Simplified Lighthouse-style audit — performance, accessibility, SEO, and best practices scores for any URL.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header"><h1>Lighthouse Report</h1><p>Simplified performance and quality audit inspired by Google Lighthouse. Scores are estimated from page fetch and HTML analysis.</p></div>
    <div class="tool-panel dev-tool-panel" data-audit-type="lighthouse">
        <label for="audit-url">Website URL</label>
        <input type="url" id="audit-url" placeholder="https://example.com">
        <div class="btn-row"><button type="button" class="btn btn-primary" id="btn-audit-run">Run audit</button><button type="button" class="btn btn-secondary" id="btn-audit-download">Download report</button></div>
        <div id="audit-error" class="alert alert-error hidden"></div>
        <div id="audit-result" class="audit-result hidden"></div>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/site-audit-report.js"></script>';
require_once __DIR__ . '/includes/footer.php';
