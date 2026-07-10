<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('Internet Speed Test', 'Free online internet speed test — measure download, upload, and ping from your browser.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Internet Speed Test</h1>
        <p>Measure download speed, upload speed, and latency using your connection to this server. Results vary by network and device.</p>
    </div>
    <div class="tool-panel dev-tool-panel speed-test-panel">
        <div class="speed-gauge-wrap">
            <div id="speed-main-value" class="speed-main-value">—</div>
            <div id="speed-main-label" class="speed-main-label">Mbps</div>
        </div>
        <div class="speed-stats-grid">
            <div class="speed-stat"><span class="speed-stat-label">Ping</span><strong id="speed-ping">—</strong></div>
            <div class="speed-stat"><span class="speed-stat-label">Download</span><strong id="speed-download">—</strong></div>
            <div class="speed-stat"><span class="speed-stat-label">Upload</span><strong id="speed-upload">—</strong></div>
            <div class="speed-stat"><span class="speed-stat-label">Jitter</span><strong id="speed-jitter">—</strong></div>
        </div>
        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-speed-start">Start test</button>
        </div>
        <p id="speed-status" class="hint speed-status">Click Start test to begin.</p>
        <div id="speed-progress" class="speed-progress hidden"><div class="speed-progress-fill" id="speed-progress-fill"></div></div>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/internet-speed-test.js"></script>';
require_once __DIR__ . '/includes/footer.php';
