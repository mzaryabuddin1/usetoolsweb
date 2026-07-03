<?php
/**
 * POST /api/video-cut.php — trim video using server-side FFmpeg.
 * GET  — returns availability status (JSON).
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/ffmpeg-helper.php';

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'available' => ffmpeg_available(),
        'max_mb'    => (int) (VIDEO_CUT_MAX_BYTES / (1024 * 1024)),
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    video_cut_json_error(405, 'Method not allowed.');
}

if (!ffmpeg_available()) {
    video_cut_json_error(503, 'FFmpeg is not installed on this server. Ask your host to install FFmpeg or set FFMPEG_BINARY in config.php.');
}

if (!isset($_FILES['video']) || !is_uploaded_file($_FILES['video']['tmp_name'])) {
    video_cut_json_error(400, 'No video file uploaded.');
}

$file = $_FILES['video'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $messages = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit (upload_max_filesize).',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
        UPLOAD_ERR_PARTIAL    => 'Upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
    ];
    video_cut_json_error(400, $messages[$file['error']] ?? 'Upload failed (error ' . $file['error'] . ').');
}

if ($file['size'] > VIDEO_CUT_MAX_BYTES) {
    $maxMb = (int) (VIDEO_CUT_MAX_BYTES / (1024 * 1024));
    video_cut_json_error(413, 'File is too large. Maximum size is ' . $maxMb . ' MB.');
}

$ext = video_cut_allowed_ext($file['name']);
if (!$ext) {
    video_cut_json_error(400, 'Unsupported video format. Use MP4, WebM, MOV, MKV, or AVI.');
}

$start = isset($_POST['start']) ? (float) $_POST['start'] : 0;
$end   = isset($_POST['end']) ? (float) $_POST['end'] : 0;

if ($start < 0) {
    $start = 0;
}

$duration = $end - $start;
if ($duration < 0.1) {
    video_cut_json_error(400, 'Selection must be at least 0.1 seconds.');
}

if ($duration > 86400) {
    video_cut_json_error(400, 'Selection is too long.');
}

$mode    = ($_POST['mode'] ?? 'fast') === 'precise' ? 'precise' : 'fast';
$mute    = !empty($_POST['mute']) && $_POST['mute'] !== '0';
$quality = in_array($_POST['quality'] ?? '', ['high', 'medium', 'low'], true)
    ? $_POST['quality']
    : 'medium';

if ($mode === 'fast') {
    $outExt = in_array($ext, ['webm'], true) ? 'webm' : 'mp4';
} else {
    $outExt = ($_POST['format'] ?? 'mp4') === 'webm' ? 'webm' : 'mp4';
}

$jobId    = bin2hex(random_bytes(16));
$tmpDir   = video_cut_tmp_dir();
$inputPath  = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '-in.' . $ext;
$outputPath = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '-out.' . $outExt;

try {
    if (!move_uploaded_file($file['tmp_name'], $inputPath)) {
        video_cut_json_error(500, 'Could not save uploaded file.');
    }

    $args = ffmpeg_cut_args($inputPath, $outputPath, $start, $duration, [
        'mode'    => $mode,
        'mute'    => $mute,
        'format'  => $outExt,
        'quality' => $quality,
    ]);

    $ffmpegOut = '';
    $exitCode  = 0;
    $ok = ffmpeg_run($args, $ffmpegOut, $exitCode);

    if (!$ok || !is_file($outputPath) || filesize($outputPath) === 0) {
        $hint = $mode === 'fast'
            ? ' Try switching to precise cut mode.'
            : '';
        video_cut_json_error(500, 'FFmpeg failed to cut this video.' . $hint);
    }

    $baseName = preg_replace('/\.[^.]+$/', '', basename($file['name']));
    $baseName = preg_replace('/[^\w\-]+/', '-', $baseName) ?: 'video';
    $downloadName = $baseName . '-trimmed.' . $outExt;

    $mime = $outExt === 'webm' ? 'video/webm' : 'video/mp4';
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $downloadName . '"');
    header('Content-Length: ' . filesize($outputPath));
    header('Cache-Control: no-store');

    readfile($outputPath);
} finally {
    video_cut_safe_unlink($inputPath ?? null);
    video_cut_safe_unlink($outputPath ?? null);
}

exit;
