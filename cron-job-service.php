<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Cron Job Service',
    'Schedule HTTP requests to any URL on a timer. Request and response logs saved in your browser. Free online cron job tester.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Cron Job Service</h1>
        <p>Schedule any URL to be called on a timer. Logs are saved in <strong>your browser</strong> (localStorage). Use server mode to bypass CORS — ideal for pinging APIs and cron endpoints.</p>
    </div>

    <div class="tool-panel cron-panel">
        <div class="alert alert-info cron-tip">
            <strong>Keep this tab open</strong> — jobs run in your browser while the page is active. Closing the tab stops all schedules (they restore when you return).
        </div>

        <section class="cron-form-section">
            <h2 class="cron-section-title">Add cron job</h2>
            <div class="cron-form-grid">
                <div class="cron-form-full">
                    <label for="cron-name">Label <span class="hint">(optional)</span></label>
                    <input type="text" id="cron-name" placeholder="My API health check">
                </div>
                <div class="cron-form-full">
                    <label for="cron-url">URL to call</label>
                    <input type="url" id="cron-url" placeholder="https://api.example.com/health" required>
                </div>
                <div>
                    <label for="cron-method">Method</label>
                    <select id="cron-method">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="PATCH">PATCH</option>
                        <option value="DELETE">DELETE</option>
                        <option value="HEAD">HEAD</option>
                    </select>
                </div>
                <div>
                    <label for="cron-interval">Run every</label>
                    <select id="cron-interval">
                        <option value="1">1 second</option>
                        <option value="2">2 seconds</option>
                        <option value="30">30 seconds</option>
                        <option value="60">1 minute</option>
                        <option value="300" selected>5 minutes</option>
                        <option value="900">15 minutes</option>
                        <option value="1800">30 minutes</option>
                        <option value="3600">1 hour</option>
                        <option value="21600">6 hours</option>
                        <option value="86400">24 hours</option>
                        <option value="custom">Custom (seconds)</option>
                    </select>
                </div>
                <div id="cron-custom-wrap" class="cron-form-full hidden">
                    <label for="cron-custom-sec">Custom interval (seconds)</label>
                    <input type="number" id="cron-custom-sec" min="1" max="86400" value="120">
                </div>
                <div class="cron-form-full">
                    <label for="cron-body">Request body <span class="hint">(POST / PUT / PATCH)</span></label>
                    <textarea id="cron-body" rows="3" placeholder="Optional JSON or text body"></textarea>
                </div>
                <div class="cron-form-full">
                    <label class="radio-label">
                        <input type="radio" name="cron-mode" value="server" checked> Server proxy <span class="hint">(recommended — bypasses CORS)</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="cron-mode" value="browser"> Browser fetch <span class="hint">(direct — target must allow CORS)</span>
                    </label>
                </div>
            </div>
            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-cron-add">Add cron job</button>
                <button type="button" class="btn btn-secondary" id="btn-cron-clear-form">Clear form</button>
            </div>
            <div id="cron-form-error" class="alert alert-error hidden"></div>
        </section>

        <section class="cron-jobs-section">
            <div class="cron-jobs-head">
                <h2 class="cron-section-title">Your cron jobs</h2>
                <span id="cron-job-count" class="hint">0 jobs</span>
            </div>
            <div id="cron-jobs-empty" class="cron-jobs-empty hint">No cron jobs yet. Add a URL above to get started.</div>
            <div id="cron-jobs-list" class="cron-jobs-list"></div>
        </section>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/cron-job-service.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
