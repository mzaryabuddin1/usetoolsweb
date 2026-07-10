<?php
require_once __DIR__ . '/config.php';
$meta = page_meta('Stress Test Tool', 'HTTP load and stress testing — send concurrent requests to any URL and view latency stats.');
require_once __DIR__ . '/includes/header.php';
?>
<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Stress Test</h1>
        <p>Send multiple concurrent HTTP requests to test endpoint stability. Use responsibly — only test URLs you own or have permission to load test.</p>
    </div>
    <div class="tool-panel dev-tool-panel">
        <div class="dev-form-grid">
            <div class="dev-form-full"><label for="stress-url">Target URL</label><input type="url" id="stress-url" placeholder="https://your-api.example.com/health"></div>
            <div><label for="stress-method">Method</label><select id="stress-method"><option>GET</option><option>POST</option><option>HEAD</option></select></div>
            <div><label for="stress-requests">Total requests</label><input type="number" id="stress-requests" min="1" max="200" value="20"></div>
            <div><label for="stress-concurrency">Concurrency</label><input type="number" id="stress-concurrency" min="1" max="20" value="5"></div>
        </div>
        <div class="btn-row"><button type="button" class="btn btn-primary" id="btn-stress-start">Start stress test</button><button type="button" class="btn btn-secondary" id="btn-stress-stop" disabled>Stop</button></div>
        <div id="stress-error" class="alert alert-error hidden"></div>
        <div id="stress-summary" class="stress-summary hidden">
            <div class="speed-stats-grid">
                <div class="speed-stat"><span class="speed-stat-label">Success</span><strong id="stress-ok">0</strong></div>
                <div class="speed-stat"><span class="speed-stat-label">Failed</span><strong id="stress-fail">0</strong></div>
                <div class="speed-stat"><span class="speed-stat-label">Avg time</span><strong id="stress-avg">—</strong></div>
                <div class="speed-stat"><span class="speed-stat-label">Max time</span><strong id="stress-max">—</strong></div>
            </div>
            <pre id="stress-log" class="audit-log"></pre>
        </div>
    </div>
    <?php ad_slot(); ?>
</div>
<?php
$extra_scripts = '<script src="/assets/js/tools/stress-test.js"></script>';
require_once __DIR__ . '/includes/footer.php';
