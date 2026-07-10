<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('Postman to Swagger Converter', 'Convert Postman Collection JSON to OpenAPI 3.0 Swagger spec online.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header"><h1>Postman to Swagger</h1><p>Upload or paste a Postman Collection v2.0/v2.1 JSON file and convert it to OpenAPI 3.0. All conversion runs locally.</p></div>
    <div class="tool-panel dev-tool-panel">
        <label for="postman-input">Postman collection JSON</label>
        <textarea id="postman-input" rows="12" placeholder='Paste exported Postman collection JSON…'></textarea>
        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-postman-convert">Convert to OpenAPI</button>
            <button type="button" class="btn btn-secondary" id="btn-postman-copy">Copy OpenAPI</button>
            <button type="button" class="btn btn-secondary" id="btn-postman-download">Download JSON</button>
            <button type="button" class="btn btn-secondary" id="btn-postman-clear">Clear</button>
        </div>
        <div id="postman-error" class="alert alert-error hidden"></div>
        <label for="postman-output">OpenAPI 3.0 output</label>
        <textarea id="postman-output" rows="14" readonly></textarea>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/postman-to-swagger.js"></script>';
require_once __DIR__ . '/includes/footer.php';
