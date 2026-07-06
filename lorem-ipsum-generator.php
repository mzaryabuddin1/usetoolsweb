<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Lorem Ipsum Generator',
    'Free Lorem Ipsum generator. Create placeholder text for designs, mockups, and prototypes.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Lorem Ipsum Generator</h1>
        <p>Generate placeholder Lorem Ipsum text by paragraphs, sentences, or words.</p>
    </div>

    <div class="tool-panel">
        <div class="form-row">
            <div>
                <label for="lorem-type">Generate by</label>
                <select id="lorem-type">
                    <option value="paragraphs">Paragraphs</option>
                    <option value="sentences">Sentences</option>
                    <option value="words">Words</option>
                </select>
            </div>
            <div>
                <label for="lorem-count">Count</label>
                <input type="number" id="lorem-count" min="1" max="50" value="3">
            </div>
        </div>

        <label class="checkbox-inline" style="margin-top:1rem;">
            <input type="checkbox" id="lorem-start" checked> Start with "Lorem ipsum dolor sit amet..."
        </label>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-generate-lorem">Generate</button>
            <button type="button" class="btn btn-secondary" id="btn-copy-lorem">Copy</button>
        </div>

        <label for="lorem-output" style="margin-top:1.5rem;">Output</label>
        <textarea id="lorem-output" readonly placeholder="Generated text will appear here..."></textarea>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/lorem-ipsum-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
