<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/ffmpeg-helper.php';

$meta = page_meta(
    'Video Cutter Online — Trim MP4, WebM Free',
    'Free online video cutter. Trim MP4, WebM, MOV, and more. Preview in your browser, export with server-side FFmpeg — files deleted after processing.'
);

$ffmpeg_ready = ffmpeg_available();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Video Cutter</h1>
        <p>Preview and select your clip in the browser. When you export, the video is processed with FFmpeg on the server and deleted immediately afterward.</p>
    </div>

    <?php if (!$ffmpeg_ready): ?>
    <div class="alert alert-error" role="alert">
        FFmpeg is not detected on this server. Install FFmpeg and add it to PATH, or set <code>FFMPEG_BINARY</code> in config.php (e.g. <code>C:\ffmpeg\ffmpeg.exe</code> or <code>/usr/bin/ffmpeg</code>).
    </div>
    <?php endif; ?>

    <div class="tool-panel media-tool-panel">
        <div class="tool-steps" aria-label="How to use">
            <span class="tool-step active" data-step="1"><strong>1</strong> Upload</span>
            <span class="tool-step" data-step="2"><strong>2</strong> Select range</span>
            <span class="tool-step" data-step="3"><strong>3</strong> Export</span>
        </div>

        <div class="drop-zone" id="drop-zone">
            <p><strong>Drop a video here</strong> or click to browse</p>
            <p>MP4, WebM, MOV, MKV, AVI supported (max <?= (int) (VIDEO_CUT_MAX_BYTES / (1024 * 1024)) ?> MB)</p>
        </div>
        <input type="file" id="file-input" accept="video/*,.mp4,.webm,.mov,.mkv,.avi,.m4v" class="hidden">

        <div id="video-controls" class="hidden">
            <div class="video-preview-wrap">
                <video id="video-preview" controls playsinline></video>
            </div>

            <div class="timeline-panel">
                <div class="timeline-labels">
                    <span id="timeline-start-label">0:00</span>
                    <span id="timeline-end-label">0:00</span>
                </div>
                <div class="dual-range-track" id="timeline-track">
                    <div class="dual-range-fill" id="timeline-fill"></div>
                    <input type="range" id="range-start" min="0" max="1000" value="0" step="1" aria-label="Start time">
                    <input type="range" id="range-end" min="0" max="1000" value="1000" step="1" aria-label="End time">
                </div>
                <div class="timeline-playhead-wrap">
                    <div class="timeline-playhead" id="timeline-playhead"></div>
                </div>
            </div>

            <div class="media-toolbar">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-play-selection">▶ Play selection</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-pause">⏸ Pause</button>
                <label class="checkbox-inline">
                    <input type="checkbox" id="loop-selection"> Loop selection
                </label>
                <div class="media-toolbar-spacer"></div>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-set-start">Set start = current</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-set-end">Set end = current</button>
            </div>

            <div class="time-range-panel">
                <div class="form-group">
                    <label for="start-time">Start</label>
                    <input type="text" id="start-time" inputmode="decimal" placeholder="0:00.0" aria-label="Start time">
                </div>
                <div class="form-group">
                    <label for="end-time">End</label>
                    <input type="text" id="end-time" inputmode="decimal" placeholder="0:00.0" aria-label="End time">
                </div>
                <div class="form-group">
                    <label for="duration-display">Selection length</label>
                    <input type="text" id="duration-display" readonly tabindex="-1" aria-readonly="true">
                </div>
            </div>

            <div class="form-row three-col">
                <div class="form-group">
                    <label for="cut-mode">Cut mode</label>
                    <select id="cut-mode">
                        <option value="fast">Fast cut (keeps quality, instant)</option>
                        <option value="precise">Precise cut (re-encodes, frame-accurate)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="output-format">Output format</label>
                    <select id="output-format">
                        <option value="mp4">MP4</option>
                        <option value="webm">WebM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="video-quality">Quality (precise mode)</label>
                    <select id="video-quality">
                        <option value="high">High</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low (smaller file)</option>
                    </select>
                </div>
            </div>

            <div class="checkbox-row">
                <label class="checkbox-inline">
                    <input type="checkbox" id="mute-audio"> Remove audio track
                </label>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="value" id="stat-original">—</div>
                    <div class="label">Original size</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="stat-duration">—</div>
                    <div class="label">Full duration</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="stat-selection">—</div>
                    <div class="label">Selected length</div>
                </div>
            </div>

            <div id="export-progress" class="progress-wrap hidden">
                <div class="progress-bar"><div class="progress-fill" id="export-progress-fill"></div></div>
                <p class="text-muted" id="export-progress-text">Processing…</p>
            </div>

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-export"<?= $ffmpeg_ready ? '' : ' disabled' ?>>Download trimmed video</button>
                <button type="button" class="btn btn-secondary" id="btn-select-all">Select all</button>
                <button type="button" class="btn btn-secondary" id="btn-reset">New file</button>
            </div>
        </div>

        <div id="video-error" class="alert alert-error hidden" role="alert"></div>
        <div id="video-info" class="alert alert-info hidden" role="status"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/video-cutter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
