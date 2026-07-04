<?php
/**
 * FFmpeg helper — detect binary and run safe cut commands.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

/**
 * Resolve FFmpeg executable path, or null if unavailable.
 */
function ffmpeg_binary(): ?string
{
    static $cached = null;
    static $resolved = false;

    if ($resolved) {
        return $cached;
    }
    $resolved = true;

    $candidates = [];

    if (defined('FFMPEG_BINARY') && FFMPEG_BINARY !== '') {
        $candidates[] = FFMPEG_BINARY;
    }

    $candidates[] = 'ffmpeg';

    if (PHP_OS_FAMILY === 'Windows') {
        $candidates[] = 'C:\\ffmpeg\\ffmpeg.exe';
        $where = @shell_exec('where ffmpeg 2>NUL');
        if ($where) {
            foreach (preg_split('/\R/', trim($where)) as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $candidates[] = $line;
                }
            }
        }
    } else {
        $which = @shell_exec('command -v ffmpeg 2>/dev/null') ?: @shell_exec('which ffmpeg 2>/dev/null');
        if ($which) {
            $candidates[] = trim($which);
        }
        $candidates[] = '/usr/bin/ffmpeg';
        $candidates[] = '/usr/local/bin/ffmpeg';
    }

    $seen = [];
    foreach ($candidates as $bin) {
        if (isset($seen[$bin])) {
            continue;
        }
        $seen[$bin] = true;

        if ($bin !== 'ffmpeg' && !is_file($bin)) {
            continue;
        }

        $cmd = escapeshellarg($bin) . ' -version 2>&1';
        $out = @shell_exec($cmd);
        if ($out && stripos($out, 'ffmpeg version') !== false) {
            $cached = $bin;
            return $cached;
        }
    }

    $cached = null;
    return null;
}

function ffmpeg_available(): bool
{
    return ffmpeg_binary() !== null;
}

/**
 * Build ffmpeg argument list for trimming a video clip.
 *
 * @return string[]
 */
function ffmpeg_cut_args(string $input, string $output, float $start, float $duration, array $options): array
{
    $mode    = ($options['mode'] ?? 'fast') === 'precise' ? 'precise' : 'fast';
    $mute    = !empty($options['mute']);
    $format  = ($options['format'] ?? 'mp4') === 'webm' ? 'webm' : 'mp4';
    $quality = $options['quality'] ?? 'medium';

    if ($quality === 'high') {
        $crf = '18';
    } elseif ($quality === 'low') {
        $crf = '28';
    } else {
        $crf = '23';
    }

    $args = [
        '-y',
        '-ss', sprintf('%.3F', $start),
        '-i', $input,
        '-t', sprintf('%.3F', $duration),
        '-avoid_negative_ts', '1',
    ];

    if ($mode === 'fast') {
        if ($mute) {
            $args[] = '-c:v';
            $args[] = 'copy';
            $args[] = '-an';
        } else {
            $args[] = '-c';
            $args[] = 'copy';
        }
    } else {
        if ($format === 'webm') {
            $args[] = '-c:v';
            $args[] = 'libvpx-vp9';
            $args[] = '-crf';
            $args[] = $crf;
            $args[] = '-b:v';
            $args[] = '0';
            if (!$mute) {
                $args[] = '-c:a';
                $args[] = 'libopus';
            }
        } else {
            $args[] = '-c:v';
            $args[] = 'libx264';
            $args[] = '-preset';
            $args[] = 'fast';
            $args[] = '-crf';
            $args[] = $crf;
            if (!$mute) {
                $args[] = '-c:a';
                $args[] = 'aac';
                $args[] = '-b:a';
                $args[] = '128k';
            }
        }
        if ($mute) {
            $args[] = '-an';
        }
    }

    $args[] = $output;
    return $args;
}

/**
 * Run ffmpeg with argument list. Returns true on success (exit code 0).
 */
function ffmpeg_run(array $args, ?string &$output = null, ?int &$exitCode = null): bool
{
    $bin = ffmpeg_binary();
    if (!$bin) {
        $output = 'FFmpeg is not installed or not accessible.';
        $exitCode = 127;
        return false;
    }

    $parts = [escapeshellarg($bin)];
    foreach ($args as $arg) {
        $parts[] = escapeshellarg($arg);
    }

    $cmd = implode(' ', $parts) . ' 2>&1';
    $lines = [];
    exec($cmd, $lines, $exitCode);
    $output = implode("\n", $lines);

    return $exitCode === 0;
}

function video_cut_tmp_dir(): string
{
    $dir = VIDEO_CUT_TMP_DIR;
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    return $dir;
}

function video_cut_safe_unlink(?string $path): void
{
    if ($path && is_file($path)) {
        @unlink($path);
    }
}

function video_cut_allowed_ext(string $filename): ?string
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = ['mp4', 'webm', 'mov', 'mkv', 'avi', 'm4v', 'mpeg', 'mpg', '3gp'];
    return in_array($ext, $allowed, true) ? $ext : null;
}

function video_cut_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $message]);
    exit;
}
