<?php
require_once __DIR__ . '/includes/pdf-page-tool.php';

pdf_page_tool_page(
    'Remove PDF Pages',
    'Delete unwanted pages from a PDF document.',
    'remove',
    'Pages to remove',
    'e.g. 2,5-7',
    'Enter page numbers or ranges to delete.'
);
