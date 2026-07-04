<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'Word to PDF',
    'Convert DOC or DOCX files to PDF.',
    'convert',
    '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'document.pdf',
    'convert',
    '',
    'docx-to-pdf'
);
