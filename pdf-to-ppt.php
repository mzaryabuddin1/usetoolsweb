<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'PDF to PowerPoint',
    'Convert PDF to editable PPTX presentation (LibreOffice on server).',
    'convert',
    '.pdf,application/pdf',
    'document.pptx',
    'convert',
    '',
    'pdf-to-pptx'
);
