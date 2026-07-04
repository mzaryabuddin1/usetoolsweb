<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'PowerPoint to PDF',
    'Convert PPT or PPTX presentations to PDF.',
    'convert',
    '.ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'document.pdf',
    'convert',
    '',
    'pptx-to-pdf'
);
