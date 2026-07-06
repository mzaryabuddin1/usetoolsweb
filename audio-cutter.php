<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Audio Cutter Online — Trim MP3, WAV, OGG Free',
    'Free online audio cutter. Trim, rearrange clips, insert silence, remove blank gaps, and export MP3 or WAV — all in your browser.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Audio Cutter</h1>
        <p>Select parts on the waveform, build a timeline, drag clips to reorder, add silence gaps, or auto-remove blank areas — then export.</p>
    </div>

    <div class="tool-panel media-tool-panel">
        <div class="tool-steps" aria-label="How to use">
            <span class="tool-step active" data-step="1"><strong>1</strong> Upload</span>
            <span class="tool-step" data-step="2"><strong>2</strong> Edit clips</span>
            <span class="tool-step" data-step="3"><strong>3</strong> Export</span>
        </div>

        <div class="drop-zone" id="drop-zone">
            <p><strong>Drop an audio file here</strong> or click to browse</p>
            <p>MP3, WAV, OGG, M4A, FLAC, WebM audio supported</p>
        </div>
        <input type="file" id="file-input" accept="audio/*,.mp3,.wav,.ogg,.m4a,.flac,.aac,.webm" class="hidden">

        <div id="audio-controls" class="hidden">
            <div class="media-toolbar">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-play" title="Play / Pause">▶ Play</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-play-selection">▶ Selection</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-play-timeline">▶ Timeline</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-stop">⏹ Stop</button>
                <label class="checkbox-inline">
                    <input type="checkbox" id="loop-selection"> Loop selection
                </label>
                <div class="media-toolbar-spacer"></div>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-zoom-in" title="Zoom in">+</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-zoom-out" title="Zoom out">−</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-fit">Fit</button>
            </div>

            <div class="waveform-wrap" id="waveform-wrap">
                <div id="waveform"></div>
            </div>
            <p class="text-muted waveform-hint">Drag on the waveform to select a part. Use the timeline below to combine clips, add gaps, or remove silence.</p>

            <div class="time-range-panel">
                <div class="form-group">
                    <label for="start-time">Selection start</label>
                    <input type="text" id="start-time" inputmode="decimal" placeholder="0:00.0" aria-label="Start time">
                </div>
                <div class="form-group">
                    <label for="end-time">Selection end</label>
                    <input type="text" id="end-time" inputmode="decimal" placeholder="0:00.0" aria-label="End time">
                </div>
                <div class="form-group">
                    <label for="duration-display">Selection length</label>
                    <input type="text" id="duration-display" readonly tabindex="-1" aria-readonly="true">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-set-from-playhead">Set start/end from playhead</button>
                </div>
            </div>

            <div class="timeline-editor">
                <div class="timeline-editor-header">
                    <h3>Output timeline</h3>
                    <p class="text-muted">Drag clips to reorder. Silence appears as gaps in the final audio.</p>
                </div>
                <div class="timeline-toolbar">
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-add-clip">+ Add selection to timeline</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-add-silence">+ Insert silence</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-remove-silence">Remove blank areas</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-clear-timeline">Clear timeline</button>
                </div>
                <div class="form-row three-col timeline-options">
                    <div class="form-group">
                        <label for="silence-duration">Silence gap (seconds)</label>
                        <input type="number" id="silence-duration" min="0.1" max="60" step="0.1" value="1">
                    </div>
                    <div class="form-group">
                        <label for="silence-threshold">Blank detection sensitivity</label>
                        <select id="silence-threshold">
                            <option value="0.008">High (more cuts)</option>
                            <option value="0.015" selected>Medium</option>
                            <option value="0.03">Low (less cuts)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="min-silence">Min blank length (sec)</label>
                        <input type="number" id="min-silence" min="0.1" max="5" step="0.1" value="0.3">
                    </div>
                </div>
                <ul id="timeline-list" class="timeline-list" aria-label="Output timeline clips"></ul>
                <p id="timeline-empty" class="text-muted timeline-empty">No clips yet — select a range and click “Add selection to timeline”, or use “Remove blank areas”.</p>
            </div>

            <div class="form-row three-col">
                <div class="form-group">
                    <label for="fade-in">Fade in (seconds)</label>
                    <input type="number" id="fade-in" min="0" max="30" step="0.1" value="0">
                </div>
                <div class="form-group">
                    <label for="fade-out">Fade out (seconds)</label>
                    <input type="number" id="fade-out" min="0" max="30" step="0.1" value="0">
                </div>
                <div class="form-group">
                    <label for="volume-gain">Volume boost</label>
                    <select id="volume-gain">
                        <option value="1">Normal (100%)</option>
                        <option value="1.25">125%</option>
                        <option value="1.5">150%</option>
                        <option value="2">200%</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="export-format">Export format</label>
                    <select id="export-format">
                        <option value="wav">WAV (lossless, larger)</option>
                        <option value="mp3">MP3 (smaller file)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mp3-bitrate">MP3 quality</label>
                    <select id="mp3-bitrate">
                        <option value="320">320 kbps (best)</option>
                        <option value="256">256 kbps</option>
                        <option value="192" selected>192 kbps</option>
                        <option value="128">128 kbps</option>
                    </select>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="value" id="stat-original">—</div>
                    <div class="label">Original size</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="stat-duration">—</div>
                    <div class="label">Source duration</div>
                </div>
                <div class="stat-box">
                    <div class="value" id="stat-output">—</div>
                    <div class="label">Output length</div>
                </div>
            </div>

            <div id="export-progress" class="progress-wrap hidden">
                <div class="progress-bar"><div class="progress-fill" id="export-progress-fill"></div></div>
                <p class="text-muted" id="export-progress-text">Processing…</p>
            </div>

            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-export">Download edited audio</button>
                <button type="button" class="btn btn-secondary" id="btn-select-all">Select all</button>
                <button type="button" class="btn btn-secondary" id="btn-reset">New file</button>
            </div>
        </div>

        <div id="audio-error" class="alert alert-error hidden" role="alert"></div>
        <div id="audio-info" class="alert alert-info hidden" role="status"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '
<script src="https://cdn.jsdelivr.net/npm/wavesurfer.js@6.6.4/dist/wavesurfer.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/wavesurfer.js@6.6.4/dist/plugin/wavesurfer.regions.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script src="/assets/js/tools/audio-cutter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
