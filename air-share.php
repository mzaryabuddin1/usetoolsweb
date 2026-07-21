<?php
require_once __DIR__ . '/config.php';

$retentionDays = defined('AIR_SHARE_RETENTION_DAYS') ? (int) AIR_SHARE_RETENTION_DAYS : 7;
$maxFileMb = (int) (AIR_SHARE_MAX_BYTES / (1024 * 1024));
$maxTextKb = (int) (AIR_SHARE_TEXT_MAX_BYTES / 1024);

$meta = page_meta(
    'Air Share',
    'Share text and files with your team — open the same link, type, save, and everyone sees it. Works on your network or anywhere.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Air Share</h1>
        <p>Shared clipboard for your network — open Air Share on the same Wi‑Fi and everyone sees the same board automatically. No link to copy for nearby colleagues. Auto-deleted after <?= (int) $retentionDays ?> days.</p>
    </div>

    <div class="tool-panel air-share-panel">
        <div id="air-network-banner" class="air-network-banner hidden" role="status">
            <strong>Network board active</strong>
            <span id="air-network-label">Same Wi‑Fi / local network</span>
            <p class="hint">Anyone on the same network who opens Air Share will see this board. Use <strong>Save</strong> after typing so others can read your updates.</p>
        </div>

        <div class="air-desk-bar">
            <div id="air-private-link-wrap" class="hidden">
                <label for="air-desk-url">Share this private link (people outside your network)</label>
                <div class="air-share-url-row">
                    <input type="text" id="air-desk-url" readonly>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-copy-desk-url">Copy link</button>
                </div>
            </div>
            <div class="air-desk-bar-actions">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-join-network">Network board</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-new-desk">Private board</button>
            </div>
            <p id="air-desk-sync" class="hint air-desk-sync">Loading…</p>
        </div>

        <div class="air-desk-section">
            <div class="air-desk-section-head">
                <h2>Text</h2>
                <div class="air-desk-actions">
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-clear-text">Clear</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-save-text">Save</button>
                </div>
            </div>
            <textarea id="air-desk-text" rows="10" placeholder="Type notes, links, or messages here…"></textarea>
            <p id="air-save-state" class="hint air-save-state"></p>
        </div>

        <div class="air-desk-section">
            <h2>Files</h2>
            <div class="drop-zone" id="air-desk-drop">
                <p><strong>Drop files here</strong> or click to browse</p>
                <p class="hint">Up to <?= (int) $maxFileMb ?> MB each · max 20 files · PDF, images, docs, audio, video, ZIP</p>
            </div>
            <input type="file" id="air-desk-file" class="hidden" multiple>
            <ul id="air-desk-files" class="air-desk-files">
                <li class="hint air-desk-files-empty">No files yet.</li>
            </ul>
        </div>

        <div id="air-error" class="alert alert-error hidden"></div>
    </div>

    <?php ad_slot(); ?>
</div>

<script>
window.AIR_SHARE_CONFIG = {
    retentionDays: <?= (int) $retentionDays ?>,
    maxFileMb: <?= (int) $maxFileMb ?>,
    maxTextKb: <?= (int) $maxTextKb ?>,
    pollMs: 3000
};
</script>
<?php
$extra_scripts = '<script src="/assets/js/tools/air-share.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
