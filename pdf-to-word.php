<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'PDF to Word',
    'Convert PDF files to editable DOCX documents (LibreOffice on server).',
    'convert',
    '.pdf,application/pdf',
    'document.docx',
    'convert',
    '',
    'pdf-to-docx'
);
