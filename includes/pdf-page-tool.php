<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/pdf-tool-scripts.php';

function pdf_page_tool_page(string $title, string $desc, string $mode, string $specLabel, string $specPlaceholder, string $specHint, bool $showRotate = false): void
{
    $meta = page_meta($title, $desc);
    $extra_head = pdf_tool_head();
    require_once __DIR__ . '/header.php';
    ?>
    <div class="container tool-page">
        <div class="tool-page-header">
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($desc) ?></p>
        </div>

        <div class="tool-panel pdf-page-tool" data-mode="<?= htmlspecialchars($mode) ?>">
            <label>Select PDF file</label>
            <input type="file" class="pdf-file-input" accept=".pdf,application/pdf">

            <div class="pdf-meta hidden">Pages: <strong class="pdf-page-count">0</strong></div>

            <?php if ($showRotate): ?>
                <label for="pdf-rotate-angle">Rotation</label>
                <select id="pdf-rotate-angle" class="pdf-rotate-angle">
                    <option value="90">90° clockwise</option>
                    <option value="180">180°</option>
                    <option value="270">90° counter-clockwise</option>
                </select>
                <p class="hint">Leave pages blank to rotate all pages.</p>
            <?php endif; ?>

            <label><?= htmlspecialchars($specLabel) ?></label>
            <input type="text" class="pdf-page-spec pdf-page-input" placeholder="<?= htmlspecialchars($specPlaceholder) ?>" <?= $mode === 'reorder' ? '' : '' ?>>
            <p class="hint"><?= htmlspecialchars($specHint) ?></p>

            <div class="btn-row">
                <button type="button" class="btn btn-primary btn-process" disabled>Download PDF</button>
                <button type="button" class="btn btn-secondary btn-clear">Clear</button>
            </div>

            <div class="pdf-status alert alert-info hidden"></div>
            <div class="pdf-error alert alert-error hidden"></div>
        </div>

        <div class="ad-slot" aria-hidden="true">Ad space</div>
    </div>
    <?php
    global $extra_scripts;
    $extra_scripts = pdf_tool_script('pdf-page-tool');
    require_once __DIR__ . '/footer.php';
}
