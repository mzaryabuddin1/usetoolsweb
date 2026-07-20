<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta('HTML to PDF', 'Convert a webpage URL or HTML snippet to PDF.');
$extra_head = pdf_tool_head();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>HTML to PDF</h1>
        <p>Convert a public webpage URL or pasted HTML to PDF. Requires wkhtmltopdf on the server.</p>
    </div>

    <div class="tool-panel pdf-server-tool" data-action="html-to-pdf" data-output="page.pdf" data-requires="html-to-pdf">
        <div class="alert alert-info pdf-server-note hidden"></div>

        <label for="html-pdf-url">Webpage URL</label>
        <input type="url" id="html-pdf-url" data-server-field="url" class="pdf-page-input" placeholder="https://example.com">

        <p class="hint">Or paste HTML below (leave URL empty):</p>
        <textarea id="html-pdf-html" data-server-field="html" rows="8" placeholder="&lt;html&gt;&lt;body&gt;Hello&lt;/body&gt;&lt;/html&gt;"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary btn-pdf-server">Convert to PDF</button>
            <button type="button" class="btn btn-secondary btn-pdf-server-clear">Clear</button>
        </div>

        <div class="pdf-server-status alert alert-info hidden"></div>
        <div class="pdf-server-error alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = pdf_tool_script('pdf-server-tool');
require_once __DIR__ . '/includes/footer.php';
?>
