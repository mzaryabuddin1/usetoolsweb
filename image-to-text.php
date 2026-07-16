<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Image to Text',
    'Free OCR image to text converter. Upload a photo or paste from clipboard to extract text instantly in your browser.'
);

$extra_head = '<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Image to Text</h1>
        <p>Extract text from photos, screenshots, and scans using OCR. Upload an image or paste from your clipboard — all processing happens locally in your browser.</p>
    </div>

    <div class="tool-panel img-ocr-panel">
        <label for="ocr-lang">Language</label>
        <select id="ocr-lang">
            <option value="eng" selected>English</option>
            <option value="spa">Spanish</option>
            <option value="fra">French</option>
            <option value="deu">German</option>
            <option value="por">Portuguese</option>
            <option value="ita">Italian</option>
            <option value="rus">Russian</option>
            <option value="ara">Arabic</option>
            <option value="chi_sim">Chinese (Simplified)</option>
            <option value="jpn">Japanese</option>
            <option value="kor">Korean</option>
            <option value="hin">Hindi</option>
            <option value="urd">Urdu</option>
        </select>

        <div class="drop-zone" id="ocr-drop-zone" style="margin-top:1rem;">
            <p><strong>Drop an image here</strong> or click to browse</p>
            <p>JPG, PNG, WebP, GIF, BMP — or press <kbd>Ctrl</kbd>+<kbd>V</kbd> / <kbd>⌘</kbd>+<kbd>V</kbd> to paste</p>
        </div>
        <input type="file" id="ocr-file-input" accept="image/png,image/jpeg,image/webp,image/gif,image/bmp,.png,.jpg,.jpeg,.webp,.gif,.bmp" class="hidden">

        <div id="ocr-progress" class="bg-remover-progress hidden">
            <p id="ocr-progress-label">Preparing OCR…</p>
            <div class="bg-progress-bar"><div id="ocr-progress-fill" class="bg-progress-fill"></div></div>
            <p class="hint">First run downloads the language model — this may take a moment.</p>
        </div>

        <div id="ocr-results" class="hidden">
            <div class="img-ocr-preview-wrap">
                <p class="img-ocr-preview-label">Source image</p>
                <div class="preview-area">
                    <img id="ocr-preview" alt="Uploaded image preview">
                </div>
            </div>

            <label for="ocr-output" style="margin-top:1.25rem;">Extracted text</label>
            <textarea id="ocr-output" readonly placeholder="Recognized text will appear here…"></textarea>

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-ocr-copy" disabled>Copy text</button>
                <button type="button" class="btn btn-secondary" id="btn-ocr-download" disabled>Download .txt</button>
                <button type="button" class="btn btn-secondary" id="btn-ocr-reset">Scan another</button>
            </div>
        </div>

        <div id="ocr-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/image-to-text.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
