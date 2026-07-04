<?php
require_once __DIR__ . '/includes/pdf-server-tool.php';

pdf_server_tool_page(
    'Unlock PDF',
    'Remove password protection from a PDF if you know the password.',
    'unlock',
    '.pdf,application/pdf',
    'unlocked.pdf',
    'unlock',
    '<label for="pdf-unlock-pass">PDF password</label><input type="password" id="pdf-unlock-pass" data-server-field="password" class="pdf-page-input" autocomplete="off">'
);
