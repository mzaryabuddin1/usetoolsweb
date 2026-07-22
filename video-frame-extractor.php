<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Video Frame Extractor',
    'Extract a single frame from any video and download it as PNG or JPG. Upload MP4, WebM, or MOV — scrub the timeline, pick the perfect shot, all in your browser.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Video Frame Extractor</h1>
        <p>Upload a video, scrub to any moment, and download that frame as a picture. Processing stays in your browser — nothing is uploaded to our servers.</p>
    </div>

    <div class="tool-panel vfe-panel">
        <div class="drop-zone" id="vfe-drop">
            <p><strong>Drop a video here</strong> or click to browse</p>
            <p class="hint">MP4, WebM, MOV, MKV, AVI supported</p>
        </div>
        <input type="file" id="vfe-file" accept="video/*,.mp4,.webm,.mov,.mkv,.avi,.m4v" class="hidden">

        <div id="vfe-workspace" class="hidden">
            <div class="vfe-layout">
                <div class="vfe-video-col">
                    <label class="vfe-label">Video preview</label>
                    <div class="video-preview-wrap">
                        <video id="vfe-video" playsinline muted></video>
                    </div>
                </div>
                <div class="vfe-frame-col">
                    <label class="vfe-label">Selected frame</label>
                    <div class="vfe-frame-preview">
                        <canvas id="vfe-canvas"></canvas>
                    </div>
                    <p id="vfe-frame-meta" class="hint vfe-frame-meta"></p>
                </div>
            </div>

            <div class="vfe-scrubber-wrap">
                <div class="vfe-scrubber-head">
                    <span id="vfe-time-current">0:00.00</span>
                    <span id="vfe-time-duration">0:00.00</span>
                </div>
                <input type="range" id="vfe-scrubber" min="0" max="1000" value="0" step="1" aria-label="Seek video frame">
            </div>

            <div class="vfe-toolbar media-toolbar">
                <button type="button" class="btn btn-secondary btn-sm" id="vfe-prev-frame" title="Previous frame">− Frame</button>
                <button type="button" class="btn btn-secondary btn-sm" id="vfe-play-toggle" title="Play / pause">Play</button>
                <button type="button" class="btn btn-secondary btn-sm" id="vfe-next-frame" title="Next frame">+ Frame</button>
                <span class="media-toolbar-spacer"></span>
                <label for="vfe-format" class="sr-only">Output format</label>
                <select id="vfe-format" class="vfe-format-select">
                    <option value="image/png">PNG</option>
                    <option value="image/jpeg">JPG</option>
                    <option value="image/webp">WebP</option>
                </select>
                <button type="button" class="btn btn-primary" id="vfe-download">Download picture</button>
                <button type="button" class="btn btn-secondary" id="vfe-reset">New video</button>
            </div>

            <div class="vfe-filmstrip-wrap">
                <p class="vfe-label">Frame strip — click a thumbnail to jump</p>
                <div id="vfe-filmstrip" class="vfe-filmstrip" role="listbox" aria-label="Video frames"></div>
                <p id="vfe-filmstrip-status" class="hint">Generating thumbnails…</p>
            </div>
        </div>

        <div id="vfe-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/video-frame-extractor.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
