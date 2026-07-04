<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'Repair PDF',
    'Fix corrupted or damaged PDF files using qpdf on the server.',
    'repair',
    '.pdf,application/pdf',
    'repaired.pdf',
    'repair'
);
