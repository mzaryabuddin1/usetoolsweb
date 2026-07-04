<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'PDF to PDF/A',
    'Convert PDF to PDF/A format for long-term archiving.',
    'to-pdfa',
    '.pdf,application/pdf',
    'document-pdfa.pdf',
    'to-pdfa'
);
