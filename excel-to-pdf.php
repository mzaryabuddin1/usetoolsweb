<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'Excel to PDF',
    'Convert XLS or XLSX spreadsheets to PDF.',
    'convert',
    '.xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'document.pdf',
    'convert',
    '',
    'xlsx-to-pdf'
);
