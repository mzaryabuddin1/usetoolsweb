<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('VAPT Report Generator', 'Basic security audit report — HTTP headers, HTTPS, CSP, cookies, and surface-level VAPT checks.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header"><h1>VAPT Report</h1><p>Automated vulnerability surface scan for any public URL. This is not a full penetration test — use for quick security header review.</p></div>
    <div class="tool-panel dev-tool-panel" data-audit-type="vapt">
        <label for="audit-url">Website URL</label>
        <input type="url" id="audit-url" placeholder="https://example.com">
        <div class="btn-row"><button type="button" class="btn btn-primary" id="btn-audit-run">Generate VAPT report</button><button type="button" class="btn btn-secondary" id="btn-audit-download">Download report</button></div>
        <div id="audit-error" class="alert alert-error hidden"></div>
        <div id="audit-result" class="audit-result hidden"></div>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/site-audit-report.js"></script>';
require_once __DIR__ . '/includes/footer.php';
