<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Markdown Preview',
    'Free Markdown preview tool. Write Markdown and see rendered HTML live in your browser.'
);

$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Markdown Preview</h1>
        <p>Write Markdown on the left and preview rendered HTML on the right. Updates as you type.</p>
    </div>

    <div class="tool-panel">
        <div class="diff-columns">
            <div>
                <label for="md-input">Markdown</label>
                <textarea id="md-input" placeholder="# Hello&#10;&#10;Write **Markdown** here..."></textarea>
            </div>
            <div>
                <label>Preview</label>
                <div id="md-preview" class="md-preview"></div>
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-secondary" id="btn-md-copy-html">Copy HTML</button>
            <button type="button" class="btn btn-secondary" id="btn-md-clear">Clear</button>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/markdown-preview.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
