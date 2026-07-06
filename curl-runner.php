<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'cURL Runner',
    'Paste a cURL command copied from Postman, Insomnia, or terminal. Parse it and send the HTTP request in your browser — no Postman needed.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>cURL Runner</h1>
        <p>Paste any <code>curl</code> command, hit <strong>Run Request</strong>, and see the response. Works with Postman’s “Copy as cURL”, Swagger, and terminal exports.</p>
    </div>

    <div class="tool-panel curl-runner-panel">
        <label for="curl-input">cURL command</label>
        <textarea id="curl-input" class="curl-input" rows="10" placeholder="curl --location 'https://api.example.com/users' \
--header 'Authorization: Bearer token' \
--header 'Content-Type: application/json' \
--data '{&quot;name&quot;: &quot;John&quot;}'"></textarea>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-curl-run">Run Request</button>
            <button type="button" class="btn btn-secondary" id="btn-curl-parse">Parse only</button>
            <button type="button" class="btn btn-secondary" id="btn-curl-clear">Clear</button>
        </div>

        <label class="radio-label curl-mode-label">
            <input type="radio" name="curl-mode" value="server" checked> Send via server <span class="hint">(bypasses CORS — recommended)</span>
        </label>
        <label class="radio-label curl-mode-label">
            <input type="radio" name="curl-mode" value="browser"> Send from browser <span class="hint">(direct — API must allow CORS)</span>
        </label>

        <p class="hint curl-cors-note">Server mode runs the request on this site’s server (like Postman). Your auth headers are not stored — only forwarded for the request.</p>

        <div id="curl-error" class="alert alert-error hidden"></div>
        <div id="curl-parse-status" class="alert alert-info hidden"></div>

        <section id="curl-parsed-section" class="curl-section hidden">
            <h2 class="curl-section-title">Parsed request <span class="hint">(edit before sending)</span></h2>

            <label for="curl-url" class="curl-field-label">Request URL</label>
            <div class="curl-url-bar">
                <select id="curl-method" class="curl-method-select" aria-label="HTTP method">
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                    <option>PATCH</option>
                    <option>DELETE</option>
                    <option>HEAD</option>
                    <option>OPTIONS</option>
                </select>
                <input type="text" id="curl-url" class="curl-url-input" placeholder="https://api.example.com/v1/resource?id=1" spellcheck="false" autocomplete="off">
            </div>

            <label for="curl-headers" class="curl-field-label">Headers <span class="hint">(one per line: Name: Value)</span></label>
            <textarea id="curl-headers" class="curl-headers" rows="5" placeholder="Content-Type: application/json&#10;Authorization: Bearer token"></textarea>
            <label for="curl-body" class="curl-field-label">Body</label>
            <textarea id="curl-body" class="curl-body" rows="6" placeholder="Request body (JSON, form data, etc.)"></textarea>
        </section>

        <section id="curl-response-section" class="curl-section hidden">
            <h2 class="curl-section-title">Response</h2>
            <div class="curl-response-meta">
                <span id="curl-res-status" class="curl-res-badge">—</span>
                <span id="curl-res-time" class="curl-res-time"></span>
                <span id="curl-res-size" class="curl-res-size"></span>
            </div>

            <div class="btn-row curl-response-actions">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-curl-copy-body">Copy body</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-curl-format-json">Format JSON</button>
            </div>

            <details class="curl-details" open>
                <summary>Response headers</summary>
                <pre id="curl-res-headers" class="curl-pre"></pre>
            </details>

            <label for="curl-res-body">Response body</label>
            <textarea id="curl-res-body" class="curl-res-body" rows="14" readonly></textarea>
        </section>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/curl-runner.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
