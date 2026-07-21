<?php
require_once __DIR__ . '/config.php';

$retentionDays = defined('AIR_SHARE_RETENTION_DAYS') ? (int) AIR_SHARE_RETENTION_DAYS : 7;
$maxFileMb = (int) (AIR_SHARE_MAX_BYTES / (1024 * 1024));
$maxTextKb = (int) (AIR_SHARE_TEXT_MAX_BYTES / 1024);

$meta = page_meta(
    'Air Share',
    'Shared clipboard for your team — same Wi‑Fi board with no link, or create a URL to share outside your network.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Air Share</h1>
        <p>Share text and files with your team on the <strong>same Wi‑Fi</strong> — just open Air Share, no link required. Need someone outside your network? Switch to <strong>Share link</strong> and send them a URL.</p>
    </div>

    <div class="tool-panel air-share-panel">
        <div class="air-mode-bar" role="tablist" aria-label="Sharing mode">
            <button type="button" class="air-mode-btn active" data-mode="lan" role="tab" aria-selected="true" id="air-mode-lan">
                Same network
            </button>
            <button type="button" class="air-mode-btn" data-mode="link" role="tab" aria-selected="false" id="air-mode-link">
                Share link
            </button>
        </div>

        <div class="air-desk-bar air-desk-bar-lan" id="air-desk-bar-lan">
            <p class="air-desk-lan-title"><strong>Network board</strong> — centralized sharing for your Wi‑Fi</p>
            <p id="air-desk-lan-hint" class="hint">Open Air Share on any device on your network — everyone sees the same text and files. No link to copy.</p>
            <p id="air-desk-sync" class="hint air-desk-sync">Loading…</p>
        </div>

        <div class="air-desk-bar air-desk-bar-link hidden" id="air-desk-bar-link">
            <label for="air-desk-url">Share this link outside your network</label>
            <div class="air-share-url-row">
                <input type="text" id="air-desk-url" readonly>
                <button type="button" class="btn btn-primary btn-sm" id="btn-copy-desk-url">Copy link</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-new-desk">New link board</button>
            </div>
            <p id="air-desk-link-sync" class="hint air-desk-sync"></p>
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
