<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/pdf-tool-scripts.php';

function pdf_server_tool_page(
    string $title,
    string $desc,
    string $action,
    string $accept,
    string $output,
    string $requires,
    string $extraFieldsHtml = '',
    string $direction = ''
): void {
    $meta = page_meta($title, $desc);
    $extra_head = pdf_tool_head();
    require_once __DIR__ . '/header.php';
    ?>
    <div class="container tool-page">
        <div class="tool-page-header">
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($desc) ?></p>
        </div>

        <div class="tool-panel pdf-server-tool"
             data-action="<?= htmlspecialchars($action) ?>"
             data-direction="<?= htmlspecialchars($direction) ?>"
             data-output="<?= htmlspecialchars($output) ?>"
             data-requires="<?= htmlspecialchars($requires) ?>">

            <div class="alert alert-info pdf-server-note hidden"></div>
            <p class="hint">Files are processed on the server and deleted immediately after download.</p>

            <label>Select file</label>
            <input type="file" class="pdf-server-input" accept="<?= htmlspecialchars($accept) ?>">

            <?= $extraFieldsHtml ?>

            <div class="btn-row">
                <button type="button" class="btn btn-primary btn-pdf-server" disabled>Process &amp; Download</button>
                <button type="button" class="btn btn-secondary btn-pdf-server-clear">Clear</button>
            </div>

            <div class="pdf-server-status alert alert-info hidden"></div>
            <div class="pdf-server-error alert alert-error hidden"></div>
        </div>

        <div class="ad-slot" aria-hidden="true">Ad space</div>
    </div>
    <?php
    global $extra_scripts;
    $extra_scripts = pdf_tool_script('pdf-server-tool');
    require_once __DIR__ . '/footer.php';
}
