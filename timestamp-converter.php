<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Unix Timestamp Converter — Milliseconds, ISO 8601, Epochs',
    'Free Unix timestamp converter. Live milliseconds since epoch, convert to UTC & local time, ISO 8601, HTTP date, Windows ticks, NTP, GPS time, and code snippets.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Timestamp Converter</h1>
        <p>Live milliseconds since the Unix epoch, convert timestamps to UTC &amp; local time, and export common formats used in programming and APIs.</p>
    </div>

    <div class="tool-panel timestamp-tool-panel">
        <div class="timestamp-live">
            <div class="stats-row">
                <div class="stat-box stat-box-live">
                    <div class="value value-mono ts-copyable" id="live-ms" title="Click to copy" tabindex="0" role="button">—</div>
                    <div class="label">Milliseconds since epoch</div>
                </div>
                <div class="stat-box stat-box-live">
                    <div class="value value-mono ts-copyable" id="live-sec" title="Click to copy" tabindex="0" role="button">—</div>
                    <div class="label">Unix time (seconds)</div>
                </div>
                <div class="stat-box stat-box-live">
                    <div class="value value-mono value-sm ts-copyable" id="live-iso" title="Click to copy" tabindex="0" role="button">—</div>
                    <div class="label">ISO 8601 (UTC)</div>
                </div>
            </div>
            <div class="timestamp-live-row">
                <div><span class="ts-label">UTC</span> <span id="live-utc" class="value-mono ts-copyable" title="Click to copy" tabindex="0" role="button">—</span></div>
                <div><span class="ts-label">Local</span> <span id="live-local" class="value-mono ts-copyable" title="Click to copy" tabindex="0" role="button">—</span></div>
                <div><span class="ts-label">Timezone</span> <span id="live-tz" class="value-mono ts-copyable" title="Click to copy" tabindex="0" role="button">—</span></div>
            </div>
            <div class="btn-row" style="margin-top:0.75rem;">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-use-now-ms">Use current time (ms)</button>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-use-now-sec">Use current time (sec)</button>
            </div>
        </div>

        <hr class="ts-divider">

        <h2 class="ts-section-title">Convert timestamp</h2>
        <p class="text-muted ts-section-desc">Enter a Unix timestamp in seconds or milliseconds, or pick a local date &amp; time.</p>

        <div class="form-row">
            <div class="form-group">
                <label for="ts-input">Unix timestamp</label>
                <input type="text" id="ts-input" inputmode="numeric" placeholder="e.g. 1710000000 or 1710000000000" autocomplete="off">
                <p class="text-muted ts-field-hint">10 digits = seconds · 13 digits = milliseconds</p>
            </div>
            <div class="form-group">
                <label for="ts-date">Local date &amp; time</label>
                <div class="ts-datetime-wrap">
                    <input type="datetime-local" id="ts-date" class="ts-datetime-input" step="1">
                    <button type="button" class="ts-datetime-icon" id="btn-ts-date-picker" aria-label="Open calendar" title="Open calendar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-row three-col">
            <div class="form-group">
                <label for="ts-y">Year</label>
                <input type="number" id="ts-y" min="1970" max="2100" placeholder="YYYY">
            </div>
            <div class="form-group">
                <label for="ts-mo">Month</label>
                <input type="number" id="ts-mo" min="1" max="12" placeholder="MM">
            </div>
            <div class="form-group">
                <label for="ts-d">Day</label>
                <input type="number" id="ts-d" min="1" max="31" placeholder="DD">
            </div>
        </div>
        <div class="form-row three-col">
            <div class="form-group">
                <label for="ts-h">Hour</label>
                <input type="number" id="ts-h" min="0" max="23" placeholder="HH">
            </div>
            <div class="form-group">
                <label for="ts-mi">Minute</label>
                <input type="number" id="ts-mi" min="0" max="59" placeholder="MM">
            </div>
            <div class="form-group">
                <label for="ts-s">Second</label>
                <input type="number" id="ts-s" min="0" max="59" placeholder="SS">
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-ts-convert">Convert</button>
            <button type="button" class="btn btn-secondary" id="btn-ts-clear">Clear</button>
        </div>

        <div id="ts-error" class="alert alert-error hidden" role="alert"></div>
        <div id="ts-copy-tooltip" class="ts-copy-tooltip hidden" aria-live="polite">Copied</div>

        <hr class="ts-divider">

        <h2 class="ts-section-title">Converted formats</h2>
        <div id="ts-formats" class="ts-format-list"></div>

        <hr class="ts-divider">

        <details class="ts-details">
            <summary>Code snippets — get current time in milliseconds</summary>
            <div class="ts-snippet-list" id="ts-snippets"></div>
        </details>

        <details class="ts-details">
            <summary>Epochs &amp; time reference</summary>
            <div class="ts-reference">
                <p><strong>Unix epoch</strong> — January 1, 1970 00:00:00 UTC. Standard for most programming languages and APIs.</p>
                <p><strong>Milliseconds (ms)</strong> — Unix time × 1000. Used by JavaScript <code>Date.now()</code>, Java <code>System.currentTimeMillis()</code>, and PHP <code>microtime(true)</code>.</p>
                <p><strong>Windows FILETIME / LDAP</strong> — 100-nanosecond ticks since January 1, 1601 UTC. Used by .NET and Active Directory.</p>
                <p><strong>NTP epoch</strong> — Seconds or milliseconds since January 1, 1900 00:00:00 UTC.</p>
                <p><strong>.NET DateTime ticks</strong> — 100-nanosecond ticks since January 1, 0001 00:00:00 UTC.</p>
                <p><strong>GPS time</strong> — Seconds since January 6, 1980 00:00:00 UTC (approximate; GPS-UTC offset not applied).</p>
                <p><strong>Julian day</strong> — Continuous day count used in astronomy. JD 2440587.5 = Unix epoch.</p>
                <p><strong>UTC vs GMT</strong> — For software and APIs they are effectively identical. UTC is the modern atomic-time standard; GMT is the legacy solar-time name still used in HTTP headers.</p>
                <p><strong>Leap seconds</strong> — UTC occasionally adds a leap second to stay aligned with Earth's rotation (e.g. 23:59:60). Most Unix timestamps ignore leap seconds and count fixed 86400-second days.</p>
            </div>
        </details>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/timestamp-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
