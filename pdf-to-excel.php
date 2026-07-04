<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'PDF to Excel',
    'Convert PDF to XLSX spreadsheet (LibreOffice on server).',
    'convert',
    '.pdf,application/pdf',
    'document.xlsx',
    'convert',
    '',
    'pdf-to-xlsx'
);
