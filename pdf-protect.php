<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'Protect PDF',
    'Encrypt a PDF with a password to prevent unauthorized access.',
    'protect',
    '.pdf,application/pdf',
    'protected.pdf',
    'protect',
    '<label for="pdf-protect-pass">Password</label><input type="password" id="pdf-protect-pass" data-server-field="password" class="pdf-page-input" autocomplete="new-password">'
);
