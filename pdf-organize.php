<?php
require_once __DIR__ . '/includes/pdf-page-tool.php';

pdf_page_tool_page(
    'Organize PDF',
    'Reorder PDF pages by entering the new page order.',
    'reorder',
    'New page order',
    'e.g. 3,1,2,4',
    'List every page number in the order you want. Example: 3,1,2,4 reorders a 4-page PDF.'
);
