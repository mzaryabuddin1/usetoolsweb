<?php
require_once __DIR__ . '/includes/pdf-page-tool.php';

pdf_page_tool_page(
    'Extract PDF Pages',
    'Extract selected pages into a new PDF file.',
    'extract',
    'Pages to extract',
    'e.g. 1,3-5',
    'Enter page numbers or ranges to keep in the new file.'
);
