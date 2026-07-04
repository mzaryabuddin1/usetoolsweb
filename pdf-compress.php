<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('Compress PDF', 'Reduce PDF file size in your browser or with server-side Ghostscript.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Compress PDF</h1>
        <p>Reduce PDF size. Browser mode re-saves the file; server mode uses Ghostscript for stronger compression.</p>
    </div>

    <div class="tool-panel">
        <label for="pdf-compress-input">Select PDF</label>
        <input type="file" id="pdf-compress-input" accept=".pdf,application/pdf">

        <label for="pdf-compress-quality">Server quality (Ghostscript)</label>
        <select id="pdf-compress-quality">
            <option value="screen">Maximum compression (screen)</option>
            <option value="ebook" selected>Ebook (balanced)</option>
            <option value="printer">High quality (printer)</option>
        </select>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-pdf-compress-client" disabled>Compress (browser)</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-compress-server" disabled>Compress (server)</button>
            <button type="button" class="btn btn-secondary" id="btn-pdf-compress-clear">Clear</button>
        </div>

        <div id="pdf-compress-status" class="alert alert-info hidden"></div>
        <div id="pdf-compress-error" class="alert alert-error hidden"></div>
    </div>
</div>

<?php $extra_scripts = pdf_tool_script('pdf-compress'); require_once __DIR__ . '/includes/footer.php'; ?>
