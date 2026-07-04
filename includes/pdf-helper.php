<?php
/**
 * PDF server-side helper — Ghostscript, qpdf, LibreOffice detection and processing.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

function pdf_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function pdf_ensure_tmp_dir(): string
{
    $dir = defined('PDF_TMP_DIR') ? PDF_TMP_DIR : (dirname(__DIR__) . '/tmp/pdf');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function pdf_detect_binary(string $name, array $extraCandidates = []): ?string
{
    static $cache = [];
    if (isset($cache[$name])) {
        return $cache[$name];
    }

    $candidates = $extraCandidates;

    if ($name === 'gs' && defined('GHOSTSCRIPT_BINARY') && GHOSTSCRIPT_BINARY !== '') {
        $candidates[] = GHOSTSCRIPT_BINARY;
    }
    if ($name === 'qpdf' && defined('QPDF_BINARY') && QPDF_BINARY !== '') {
        $candidates[] = QPDF_BINARY;
    }
    if ($name === 'libreoffice' && defined('LIBREOFFICE_BINARY') && LIBREOFFICE_BINARY !== '') {
        $candidates[] = LIBREOFFICE_BINARY;
    }

    $candidates[] = $name;

    if (PHP_OS_FAMILY === 'Windows') {
        if ($name === 'gs') {
            $candidates[] = 'C:\\Program Files\\gs\\gs10.03.1\\bin\\gswin64c.exe';
            $candidates[] = 'C:\\gs\\gswin64c.exe';
        }
        if ($name === 'libreoffice') {
            $candidates[] = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';
        }
        $where = @shell_exec('where ' . escapeshellarg($name) . ' 2>NUL');
        if ($where) {
            foreach (preg_split('/\R/', trim($where)) as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $candidates[] = $line;
                }
            }
        }
    } else {
        $which = @shell_exec('command -v ' . escapeshellarg($name) . ' 2>/dev/null')
            ?: @shell_exec('which ' . escapeshellarg($name) . ' 2>/dev/null');
        if ($which) {
            $candidates[] = trim($which);
        }
        if ($name === 'gs') {
            $candidates[] = '/usr/bin/gs';
            $candidates[] = '/usr/local/bin/gs';
        }
        if ($name === 'qpdf') {
            $candidates[] = '/usr/bin/qpdf';
        }
        if ($name === 'libreoffice') {
            $candidates[] = '/usr/bin/libreoffice';
            $candidates[] = '/usr/bin/soffice';
        }
    }

    $seen = [];
    foreach ($candidates as $bin) {
        if (isset($seen[$bin])) {
            continue;
        }
        $seen[$bin] = true;

        if ($bin !== $name && !is_file($bin)) {
            continue;
        }

        $cmd = escapeshellarg($bin) . ' -version 2>&1';
        $out = @shell_exec($cmd);
        if (!$out || strlen(trim($out)) === 0) {
            $cmd = escapeshellarg($bin) . ' --version 2>&1';
            $out = @shell_exec($cmd);
        }
        if ($out && strlen(trim($out)) > 0) {
            $cache[$name] = $bin;
            return $bin;
        }
    }

    $cache[$name] = null;
    return null;
}

function ghostscript_binary(): ?string
{
    return pdf_detect_binary('gs');
}

function qpdf_binary(): ?string
{
    return pdf_detect_binary('qpdf');
}

function libreoffice_binary(): ?string
{
    return pdf_detect_binary('libreoffice') ?: pdf_detect_binary('soffice');
}

function pdf_server_status(): array
{
    return [
        'ghostscript'  => ghostscript_binary() !== null,
        'qpdf'         => qpdf_binary() !== null,
        'libreoffice'  => libreoffice_binary() !== null,
        'max_mb'       => (int) (PDF_MAX_BYTES / (1024 * 1024)),
    ];
}

function pdf_run_command(array $args, ?string &$stderr = null): ?string
{
    $cmd = implode(' ', array_map('escapeshellarg', $args));
    $stderr = '';
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $proc = proc_open($cmd, $descriptors, $pipes);
    if (!is_resource($proc)) {
        return null;
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($proc);
    if ($code !== 0) {
        return null;
    }
    return $stdout;
}

function pdf_compress_file(string $input, string $output, string $quality = 'ebook'): bool
{
    $gs = ghostscript_binary();
    if (!$gs) {
        return false;
    }

    $settings = [
        'screen'  => '/screen',
        'ebook'   => '/ebook',
        'printer' => '/printer',
        'prepress'=> '/prepress',
    ];
    $setting = $settings[$quality] ?? '/ebook';

    $args = [
        $gs,
        '-sDEVICE=pdfwrite',
        '-dCompatibilityLevel=1.4',
        '-dPDFSETTINGS=' . $setting,
        '-dNOPAUSE',
        '-dQUIET',
        '-dBATCH',
        '-sOutputFile=' . $output,
        $input,
    ];

    return pdf_run_command($args) !== null && is_file($output);
}

function pdf_repair_file(string $input, string $output): bool
{
    $qpdf = qpdf_binary();
    if (!$qpdf) {
        return false;
    }

    $args = [$qpdf, '--linearize', $input, $output];
    if (pdf_run_command($args) !== null && is_file($output)) {
        return true;
    }

    $args = [$qpdf, $input, $output];
    return pdf_run_command($args) !== null && is_file($output);
}

function pdf_unlock_file(string $input, string $output, string $password): bool
{
    $qpdf = qpdf_binary();
    if (!$qpdf) {
        return false;
    }

    $args = [$qpdf, '--password=' . $password, '--decrypt', $input, $output];
    return pdf_run_command($args) !== null && is_file($output);
}

function pdf_protect_file(string $input, string $output, string $userPassword, string $ownerPassword = ''): bool
{
    $qpdf = qpdf_binary();
    if (!$qpdf) {
        return false;
    }

    if ($ownerPassword === '') {
        $ownerPassword = $userPassword;
    }

    $args = [
        $qpdf,
        '--encrypt',
        $userPassword,
        $ownerPassword,
        '256',
        '--',
        $input,
        $output,
    ];

    return pdf_run_command($args) !== null && is_file($output);
}

function pdf_to_pdfa_file(string $input, string $output): bool
{
    $gs = ghostscript_binary();
    if (!$gs) {
        return false;
    }

    $args = [
        $gs,
        '-dPDFA=2',
        '-dBATCH',
        '-dNOPAUSE',
        '-dNOOUTERSAVE',
        '-sProcessColorModel=DeviceRGB',
        '-sDEVICE=pdfwrite',
        '-dPDFACompatibilityPolicy=1',
        '-sOutputFile=' . $output,
        $input,
    ];

    return pdf_run_command($args) !== null && is_file($output);
}

function pdf_convert_with_libreoffice(string $input, string $outDir, string $format): ?string
{
    $lo = libreoffice_binary();
    if (!$lo) {
        return null;
    }

    $args = [
        $lo,
        '--headless',
        '--nologo',
        '--nofirststartwizard',
        '--convert-to',
        $format,
        '--outdir',
        $outDir,
        $input,
    ];

    if (pdf_run_command($args) === null) {
        return null;
    }

    $base = pathinfo($input, PATHINFO_FILENAME);
    $ext  = $format === 'pdf' ? 'pdf' : explode(':', $format)[0];
    $out  = rtrim($outDir, '/\\') . DIRECTORY_SEPARATOR . $base . '.' . $ext;

    return is_file($out) ? $out : null;
}

function pdf_allowed_upload_ext(string $filename): ?string
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'html', 'htm'];
    return in_array($ext, $allowed, true) ? $ext : null;
}

function pdf_cleanup_dir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    foreach (glob($dir . '/*') ?: [] as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
    @rmdir($dir);
}

function pdf_validate_upload(array $file): string
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        pdf_json_error(400, 'No file uploaded.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        pdf_json_error(400, 'Upload failed (error ' . $file['error'] . ').');
    }

    if ($file['size'] > PDF_MAX_BYTES) {
        $maxMb = (int) (PDF_MAX_BYTES / (1024 * 1024));
        pdf_json_error(413, 'File is too large. Maximum size is ' . $maxMb . ' MB.');
    }

    $ext = pdf_allowed_upload_ext($file['name']);
    if (!$ext) {
        pdf_json_error(400, 'Unsupported file type.');
    }

    return $ext;
}
