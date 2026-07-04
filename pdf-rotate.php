<?php
require_once __DIR__ . '/includes/pdf-page-tool.php';

pdf_page_tool_page(
    'Rotate PDF',
    'Rotate PDF pages 90°, 180°, or 270°.',
    'rotate',
    'Pages to rotate (optional)',
    'e.g. 1,3-5',
    'Leave blank to rotate all pages.',
    true
);
