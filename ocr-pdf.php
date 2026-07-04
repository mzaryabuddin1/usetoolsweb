<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'OCR PDF',
    'Extract text from scanned PDFs using Tesseract OCR (server).',
    'ocr',
    '.pdf,application/pdf',
    'ocr.pdf',
    'ocr',
    '<label for="pdf-ocr-lang">Language code</label><input type="text" id="pdf-ocr-lang" data-server-field="lang" value="eng" class="pdf-page-input" placeholder="eng">'
);
