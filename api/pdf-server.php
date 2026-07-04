<?php
/**
 * POST /api/pdf-server.php — server-side PDF processing (compress, convert, encrypt, etc.)
 * GET  — returns available backends (JSON).
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/pdf-helper.php';

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['ok' => true], pdf_server_status()));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    pdf_json_error(405, 'Method not allowed.');
}

$action = $_POST['action'] ?? '';
$workDir = pdf_ensure_tmp_dir() . '/' . bin2hex(random_bytes(8));
@mkdir($workDir, 0755, true);

try {
    switch ($action) {
        case 'compress':
            handle_compress($workDir);
            break;
        case 'repair':
            handle_repair($workDir);
            break;
        case 'unlock':
            handle_unlock($workDir);
            break;
        case 'protect':
            handle_protect($workDir);
            break;
        case 'to-pdfa':
            handle_to_pdfa($workDir);
            break;
        case 'convert':
            handle_convert($workDir);
            break;
        case 'html-to-pdf':
            handle_html_to_pdf($workDir);
            break;
        case 'ocr':
            handle_ocr($workDir);
            break;
        default:
            pdf_json_error(400, 'Unknown action.');
    }
} finally {
    pdf_cleanup_dir($workDir);
}

function handle_upload_to(string $workDir, string $field = 'file'): array
{
    if (!isset($_FILES[$field])) {
        pdf_json_error(400, 'No file uploaded.');
    }
    $ext = pdf_validate_upload($_FILES[$field]);
    $dest = $workDir . '/input.' . $ext;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
        pdf_json_error(500, 'Could not save uploaded file.');
    }
    return ['path' => $dest, 'ext' => $ext];
}

function send_file_download(string $path, string $downloadName, string $mime): void
{
    if (!is_file($path)) {
        pdf_json_error(500, 'Output file was not created.');
    }
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $downloadName) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

function handle_compress(string $workDir): void
{
    if (!ghostscript_binary()) {
        pdf_json_error(503, 'Ghostscript is not installed on this server.');
    }

    $upload = handle_upload_to($workDir);
    if ($upload['ext'] !== 'pdf') {
        pdf_json_error(400, 'Upload a PDF file.');
    }

    $quality = in_array($_POST['quality'] ?? '', ['screen', 'ebook', 'printer'], true)
        ? $_POST['quality']
        : 'ebook';

    $output = $workDir . '/compressed.pdf';
    if (!pdf_compress_file($upload['path'], $output, $quality)) {
        pdf_json_error(500, 'Compression failed. The PDF may be corrupted or encrypted.');
    }

    send_file_download($output, 'compressed.pdf', 'application/pdf');
}

function handle_repair(string $workDir): void
{
    if (!qpdf_binary()) {
        pdf_json_error(503, 'qpdf is not installed on this server.');
    }

    $upload = handle_upload_to($workDir);
    $output = $workDir . '/repaired.pdf';
    if (!pdf_repair_file($upload['path'], $output)) {
        pdf_json_error(500, 'Could not repair this PDF.');
    }

    send_file_download($output, 'repaired.pdf', 'application/pdf');
}

function handle_unlock(string $workDir): void
{
    if (!qpdf_binary()) {
        pdf_json_error(503, 'qpdf is not installed on this server.');
    }

    $upload = handle_upload_to($workDir);
    $password = $_POST['password'] ?? '';
    if ($password === '') {
        pdf_json_error(400, 'Enter the PDF password.');
    }

    $output = $workDir . '/unlocked.pdf';
    if (!pdf_unlock_file($upload['path'], $output, $password)) {
        pdf_json_error(400, 'Could not unlock PDF. Wrong password or unsupported encryption.');
    }

    send_file_download($output, 'unlocked.pdf', 'application/pdf');
}

function handle_protect(string $workDir): void
{
    if (!qpdf_binary()) {
        pdf_json_error(503, 'qpdf is not installed on this server.');
    }

    $upload = handle_upload_to($workDir);
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 1) {
        pdf_json_error(400, 'Enter a password.');
    }

    $output = $workDir . '/protected.pdf';
    if (!pdf_protect_file($upload['path'], $output, $password)) {
        pdf_json_error(500, 'Could not encrypt PDF.');
    }

    send_file_download($output, 'protected.pdf', 'application/pdf');
}

function handle_to_pdfa(string $workDir): void
{
    if (!ghostscript_binary()) {
        pdf_json_error(503, 'Ghostscript is not installed on this server.');
    }

    $upload = handle_upload_to($workDir);
    $output = $workDir . '/output.pdf';
    if (!pdf_to_pdfa_file($upload['path'], $output)) {
        pdf_json_error(500, 'PDF/A conversion failed.');
    }

    send_file_download($output, 'document-pdfa.pdf', 'application/pdf');
}

function handle_convert(string $workDir): void
{
    if (!libreoffice_binary()) {
        pdf_json_error(503, 'LibreOffice is not installed on this server.');
    }

    $upload = handle_upload_to($workDir);
    $direction = $_POST['direction'] ?? '';

    $map = [
        'pdf-to-docx'  => ['from' => 'pdf',  'format' => 'docx', 'out' => 'document.docx', 'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'pdf-to-xlsx'  => ['from' => 'pdf',  'format' => 'xlsx', 'out' => 'document.xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'pdf-to-pptx'  => ['from' => 'pdf',  'format' => 'pptx', 'out' => 'document.pptx', 'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'docx-to-pdf'  => ['from' => 'docx', 'format' => 'pdf',  'out' => 'document.pdf',  'mime' => 'application/pdf'],
        'doc-to-pdf'   => ['from' => 'doc',  'format' => 'pdf',  'out' => 'document.pdf',  'mime' => 'application/pdf'],
        'xlsx-to-pdf'  => ['from' => 'xlsx', 'format' => 'pdf',  'out' => 'document.pdf',  'mime' => 'application/pdf'],
        'xls-to-pdf'   => ['from' => 'xls',  'format' => 'pdf',  'out' => 'document.pdf',  'mime' => 'application/pdf'],
        'pptx-to-pdf'  => ['from' => 'pptx', 'format' => 'pdf',  'out' => 'document.pdf',  'mime' => 'application/pdf'],
        'ppt-to-pdf'   => ['from' => 'ppt',  'format' => 'pdf',  'out' => 'document.pdf',  'mime' => 'application/pdf'],
    ];

    if (!isset($map[$direction])) {
        pdf_json_error(400, 'Invalid conversion direction.');
    }

    $cfg = $map[$direction];
    if ($upload['ext'] !== $cfg['from'] && !($cfg['from'] === 'docx' && $upload['ext'] === 'doc')) {
        // allow doc for docx-to-pdf etc via ext check loosely
        $validFrom = [$cfg['from']];
        if ($cfg['from'] === 'docx') $validFrom[] = 'doc';
        if ($cfg['from'] === 'xlsx') $validFrom[] = 'xls';
        if ($cfg['from'] === 'pptx') $validFrom[] = 'ppt';
        if (!in_array($upload['ext'], $validFrom, true)) {
            pdf_json_error(400, 'Wrong file type for this conversion.');
        }
    }

    $result = pdf_convert_with_libreoffice($upload['path'], $workDir, $cfg['format']);
    if (!$result) {
        pdf_json_error(500, 'Conversion failed. The file may be corrupted or unsupported.');
    }

    send_file_download($result, $cfg['out'], $cfg['mime']);
}

function handle_html_to_pdf(string $workDir): void
{
    $url = trim($_POST['url'] ?? '');
    $html = $_POST['html'] ?? '';

    if ($url === '' && $html === '') {
        pdf_json_error(400, 'Enter a URL or paste HTML content.');
    }

    $output = $workDir . '/page.pdf';

    // Try wkhtmltopdf
    $wk = pdf_detect_binary('wkhtmltopdf');
    if ($wk) {
        if ($url !== '') {
            $args = [$wk, '--quiet', $url, $output];
        } else {
            $htmlFile = $workDir . '/input.html';
            file_put_contents($htmlFile, $html);
            $args = [$wk, '--quiet', $htmlFile, $output];
        }
        if (pdf_run_command($args) !== null && is_file($output)) {
            send_file_download($output, 'page.pdf', 'application/pdf');
        }
    }

    // Fallback: Ghostscript cannot convert HTML; return helpful error
    pdf_json_error(503, 'HTML to PDF requires wkhtmltopdf on the server. Contact your host or use Word to PDF instead.');
}

function handle_ocr(string $workDir): void
{
    $tesseract = pdf_detect_binary('tesseract');
    $gs = ghostscript_binary();

    if (!$tesseract || !$gs) {
        pdf_json_error(503, 'OCR requires Tesseract and Ghostscript on the server.');
    }

    $upload = handle_upload_to($workDir);
    if ($upload['ext'] !== 'pdf') {
        pdf_json_error(400, 'Upload a PDF file.');
    }

    $lang = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['lang'] ?? 'eng') ?: 'eng';
    $pngPrefix = $workDir . '/page';
    $gsArgs = [
        $gs,
        '-sDEVICE=pnggray',
        '-r300',
        '-o',
        $pngPrefix . '-%d.png',
        $upload['path'],
    ];

    if (pdf_run_command($gsArgs) === null) {
        pdf_json_error(500, 'Could not rasterize PDF for OCR.');
    }

    $images = glob($pngPrefix . '-*.png') ?: [];
    if (count($images) === 0) {
        pdf_json_error(500, 'No pages found in PDF.');
    }

    sort($images, SORT_NATURAL);
    $textParts = [];
    foreach ($images as $img) {
        $txtBase = $workDir . '/' . pathinfo($img, PATHINFO_FILENAME);
        $args = [$tesseract, $img, $txtBase, '-l', $lang];
        pdf_run_command($args);
        $txtFile = $txtBase . '.txt';
        if (is_file($txtFile)) {
            $textParts[] = trim(file_get_contents($txtFile));
        }
    }

    // Build searchable PDF via tesseract pdf output on first page merged — simplified: return text PDF via gs
    $combinedText = $workDir . '/ocr.txt';
    file_put_contents($combinedText, implode("\n\n--- Page break ---\n\n", $textParts));

    $ocrPdf = $workDir . '/ocr.pdf';
    $gsTextArgs = [
        $gs,
        '-sDEVICE=pdfwrite',
        '-dNOPAUSE',
        '-dBATCH',
        '-sOutputFile=' . $ocrPdf,
        '-f',
        $combinedText,
    ];

    if (!pdf_run_command($gsTextArgs) || !is_file($ocrPdf)) {
        // Return original with note — at minimum provide text extract as downloadable txt in JSON
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'    => true,
            'text'  => implode("\n\n", $textParts),
            'note'  => 'OCR text extracted. Full searchable PDF requires additional server setup.',
        ]);
        exit;
    }

    send_file_download($ocrPdf, 'ocr.pdf', 'application/pdf');
}
