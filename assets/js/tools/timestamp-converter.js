$(function () {
    'use strict';

    var $input = $('#ts-input');
    var $date = $('#ts-date');
    var $error = $('#ts-error');
    var $formats = $('#ts-formats');
    var syncing = false;

    var UNIX_EPOCH_MS = 0;
    var MS_PER_DAY = 86400000;
    var UNIX_TO_FILETIME = 116444736000000000; // 100-ns ticks from 1601 to 1970
    var UNIX_TO_DOTNET_TICKS = 621355968000000000;
    var UNIX_TO_NTP_MS = 2208988800000;
    var GPS_EPOCH_UNIX_SEC = 315964800; // 1980-01-06 00:00:00 UTC
    var JULIAN_UNIX = 2440587.5;

    var SNIPPETS = [
        { lang: 'JavaScript', code: 'Date.now() // or: new Date().getTime()' },
        { lang: 'PHP', code: '(int) (microtime(true) * 1000)' },
        { lang: 'Python', code: 'round(time.time() * 1000)' },
        { lang: 'Java / Kotlin', code: 'System.currentTimeMillis()' },
        { lang: 'C# / .NET', code: 'DateTimeOffset.UtcNow.ToUnixTimeMilliseconds()' },
        { lang: 'Go', code: 'time.Now().UnixMilli()' },
        { lang: 'Rust', code: 'std::time::SystemTime::now()\n  .duration_since(UNIX_EPOCH)\n  .unwrap()\n  .as_millis() as i64' },
        { lang: 'Ruby', code: '(Time.now.to_f * 1000).floor' },
        { lang: 'Swift', code: 'Int(Date().timeIntervalSince1970 * 1000)' },
        { lang: 'PostgreSQL', code: "extract(epoch FROM now()) * 1000" },
        { lang: 'MySQL', code: 'UNIX_TIMESTAMP(NOW(3)) * 1000' },
        { lang: 'SQL Server', code: "DATEDIFF_BIG(MILLISECOND,'1970-01-01', SYSUTCDATETIME())" },
        { lang: 'Bash', code: "date +%s%3N  # GNU date; or: $(($(date +%s)*1000))" },
        { lang: 'PowerShell', code: '[DateTimeOffset]::UtcNow.ToUnixTimeMilliseconds()' }
    ];

    function pad(n, len) {
        len = len || 2;
        return String(n).padStart(len, '0');
    }

    function toDatetimeLocal(date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) +
            'T' + pad(date.getHours()) + ':' + pad(date.getMinutes()) + ':' + pad(date.getSeconds());
    }

    function formatLocalLong(date) {
        return date.toLocaleString(undefined, {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }

    function formatIsoLocal(date) {
        var off = -date.getTimezoneOffset();
        var sign = off >= 0 ? '+' : '-';
        var abs = Math.abs(off);
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) +
            'T' + pad(date.getHours()) + ':' + pad(date.getMinutes()) + ':' + pad(date.getSeconds()) +
            sign + pad(Math.floor(abs / 60)) + ':' + pad(abs % 60);
    }

    function httpDate(date) {
        return date.toUTCString();
    }

    function julianDay(unixMs) {
        return (unixMs / MS_PER_DAY) + JULIAN_UNIX;
    }

    function parseTimestampInput(raw) {
        raw = String(raw).trim().replace(/[,_\s]/g, '');
        if (!raw || !/^-?\d+$/.test(raw)) {
            throw new Error('Enter a valid numeric Unix timestamp.');
        }
        var n = parseInt(raw, 10);
        if (raw.length >= 12 || (raw.length >= 10 && n > 9999999999)) {
            return n; // milliseconds
        }
        return n * 1000; // seconds
    }

    function msToDate(ms) {
        var date = new Date(ms);
        if (isNaN(date.getTime())) throw new Error('Timestamp is out of range.');
        return date;
    }

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
    }

    function hideError() {
        $error.addClass('hidden');
    }

    function getTimezoneLabel() {
        try {
            return Intl.DateTimeFormat().resolvedOptions().timeZone +
                ' (UTC' + formatOffset(new Date()) + ')';
        } catch (e) {
            return 'UTC' + formatOffset(new Date());
        }
    }

    function formatOffset(date) {
        var off = -date.getTimezoneOffset();
        var sign = off >= 0 ? '+' : '-';
        var abs = Math.abs(off);
        return sign + pad(Math.floor(abs / 60)) + ':' + pad(abs % 60);
    }

    function buildFormats(ms) {
        var date = msToDate(ms);
        var sec = Math.floor(ms / 1000);
        var filetime = Math.round(ms * 10000 + UNIX_TO_FILETIME);
        var dotnetTicks = Math.round(ms * 10000 + UNIX_TO_DOTNET_TICKS);
        var ntpMs = ms + UNIX_TO_NTP_MS;
        var gpsSec = sec - GPS_EPOCH_UNIX_SEC;

        return [
            { label: 'Unix seconds', value: String(sec), desc: '10-digit Unix timestamp' },
            { label: 'Unix milliseconds', value: String(ms), desc: '13-digit timestamp (JavaScript, Java)' },
            { label: 'ISO 8601 (UTC)', value: date.toISOString(), desc: 'Standard API format' },
            { label: 'ISO 8601 (local)', value: formatIsoLocal(date), desc: 'With your timezone offset' },
            { label: 'UTC string', value: date.toUTCString(), desc: 'Human-readable UTC' },
            { label: 'Local string', value: formatLocalLong(date), desc: 'Your device timezone' },
            { label: 'HTTP / RFC 7231', value: httpDate(date), desc: 'Used in HTTP headers' },
            { label: 'Windows FILETIME', value: String(filetime), desc: '100-ns ticks since 1601-01-01' },
            { label: '.NET DateTime ticks', value: String(dotnetTicks), desc: '100-ns ticks since 0001-01-01' },
            { label: 'NTP (ms since 1900)', value: String(ntpMs), desc: 'Network Time Protocol epoch' },
            { label: 'GPS seconds (since 1980)', value: String(gpsSec), desc: 'Approximate GPS time' },
            { label: 'Julian day', value: julianDay(ms).toFixed(5), desc: 'JD 2440587.5 = Unix epoch' },
            { label: 'Day of week (UTC)', value: date.toLocaleDateString('en-US', { weekday: 'long', timeZone: 'UTC' }), desc: '' },
            { label: 'Relative', value: relativeTime(ms), desc: 'Compared to now' }
        ];
    }

    function relativeTime(ms) {
        var diff = ms - Date.now();
        var abs = Math.abs(diff);
        var units = [
            { n: 86400000, s: 'day' },
            { n: 3600000, s: 'hour' },
            { n: 60000, s: 'minute' },
            { n: 1000, s: 'second' }
        ];
        for (var i = 0; i < units.length; i++) {
            if (abs >= units[i].n || units[i].s === 'second') {
                var v = Math.round(abs / units[i].n);
                var word = v === 1 ? units[i].s : units[i].s + 's';
                return diff >= 0 ? 'in ' + v + ' ' + word : v + ' ' + word + ' ago';
            }
        }
        return 'now';
    }

    function renderFormats(ms) {
        var formats = buildFormats(ms);
        $formats.empty();
        formats.forEach(function (f) {
            var $row = $('<div class="ts-format-row"></div>');
            $row.append(
                '<div class="ts-format-meta">' +
                '<strong>' + f.label + '</strong>' +
                (f.desc ? '<span>' + f.desc + '</span>' : '') +
                '</div>'
            );
            var $valWrap = $('<div class="ts-format-value-wrap"></div>');
            $valWrap.append('<code class="ts-format-value">' + escapeHtml(f.value) + '</code>');
            $valWrap.append('<button type="button" class="btn btn-secondary btn-sm btn-ts-copy" title="Copy">Copy</button>');
            $row.append($valWrap);
            $row.find('.btn-ts-copy').on('click', function () {
                copyText(f.value, $(this));
            });
            $formats.append($row);
        });
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function copyText(text, $btn) {
        var done = function () {
            if ($btn) flashCopy($btn);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text).then(done);
        }
        var $ta = $('<textarea>').val(text).appendTo('body').select();
        document.execCommand('copy');
        $ta.remove();
        done();
        return $.Deferred().resolve().promise();
    }

    function flashCopy($btn) {
        var orig = $btn.text();
        $btn.text('Copied!');
        setTimeout(function () { $btn.text(orig); }, 1200);
    }

    var $copyTooltip = $('#ts-copy-tooltip');
    var tooltipTimer = null;

    function showCopyTooltip($el) {
        if (!$copyTooltip.length || !$el || !$el.length) return;
        var rect = $el[0].getBoundingClientRect();
        $copyTooltip.removeClass('hidden').css({
            top: Math.max(8, rect.top - 36) + 'px',
            left: rect.left + rect.width / 2 + 'px'
        });
        clearTimeout(tooltipTimer);
        tooltipTimer = setTimeout(function () {
            $copyTooltip.addClass('hidden');
        }, 1500);
    }

    function copyWithTooltip(text, $el) {
        if (!text || text === '—') return;
        copyText(String(text)).then(function () {
            showCopyTooltip($el);
        });
    }

    function syncPartsFromDate(date) {
        syncing = true;
        $('#ts-y').val(date.getFullYear());
        $('#ts-mo').val(date.getMonth() + 1);
        $('#ts-d').val(date.getDate());
        $('#ts-h').val(date.getHours());
        $('#ts-mi').val(date.getMinutes());
        $('#ts-s').val(date.getSeconds());
        $date.val(toDatetimeLocal(date));
        syncing = false;
    }

    function applyFromMs(ms) {
        var date = msToDate(ms);
        syncing = true;
        $input.val(String(ms));
        syncPartsFromDate(date);
        syncing = false;
        renderFormats(ms);
        hideError();
    }

    function applyFromParts() {
        var y = parseInt($('#ts-y').val(), 10);
        var mo = parseInt($('#ts-mo').val(), 10);
        var d = parseInt($('#ts-d').val(), 10);
        var h = parseInt($('#ts-h').val(), 10) || 0;
        var mi = parseInt($('#ts-mi').val(), 10) || 0;
        var s = parseInt($('#ts-s').val(), 10) || 0;
        if (isNaN(y) || isNaN(mo) || isNaN(d)) {
            throw new Error('Enter year, month, and day.');
        }
        var date = new Date(y, mo - 1, d, h, mi, s);
        if (isNaN(date.getTime())) throw new Error('Invalid date.');
        applyFromMs(date.getTime());
    }

    function convert() {
        hideError();
        try {
            var raw = $.trim($input.val());
            if (raw) {
                applyFromMs(parseTimestampInput(raw));
                return;
            }
            if ($date.val()) {
                applyFromMs(new Date($date.val()).getTime());
                return;
            }
            if ($('#ts-y').val()) {
                applyFromParts();
                return;
            }
            throw new Error('Enter a timestamp or date/time.');
        } catch (e) {
            showError(e.message || 'Conversion failed.');
        }
    }

    function openDatePicker() {
        var el = $date[0];
        if (!el) return;
        if (typeof el.showPicker === 'function') {
            try { el.showPicker(); return; } catch (err) { /* fallback */ }
        }
        el.focus();
        el.click();
    }

    function updateLiveClock() {
        var now = Date.now();
        var date = new Date(now);
        $('#live-ms').text(now);
        $('#live-sec').text(Math.floor(now / 1000));
        $('#live-iso').text(date.toISOString());
        $('#live-utc').text(date.toUTCString());
        $('#live-local').text(formatLocalLong(date));
        $('#live-tz').text(getTimezoneLabel());
    }

    function renderSnippets() {
        var $list = $('#ts-snippets');
        SNIPPETS.forEach(function (s) {
            var $item = $('<div class="ts-snippet"></div>');
            $item.append('<div class="ts-snippet-lang">' + s.lang + '</div>');
            var $pre = $('<pre><code></code></pre>');
            $pre.find('code').text(s.code);
            $item.append($pre);
            $item.append('<button type="button" class="btn btn-secondary btn-sm btn-ts-copy">Copy</button>');
            $item.find('.btn-ts-copy').on('click', function () {
                copyText(s.code, $(this));
            });
            $list.append($item);
        });
    }

    function useNow(asMs) {
        var now = Date.now();
        if (asMs) {
            applyFromMs(now);
        } else {
            var sec = Math.floor(now / 1000);
            syncing = true;
            $input.val(String(sec));
            syncing = false;
            applyFromMs(sec * 1000);
        }
    }

    function clearAll() {
        $input.val('');
        $date.val('');
        $('#ts-y, #ts-mo, #ts-d, #ts-h, #ts-mi, #ts-s').val('');
        $formats.empty();
        hideError();
    }

    // URL param: /timestamp-converter?ts=1710000000 or ?ts=1710000000000
    function loadFromUrl() {
        var params = new URLSearchParams(window.location.search);
        var ts = params.get('ts') || params.get('millis') || params.get('now');
        if (ts === 'now' || ts === null) return;
        try {
            applyFromMs(parseTimestampInput(ts));
        } catch (e) { /* ignore bad URL param */ }
    }

    updateLiveClock();
    setInterval(updateLiveClock, 37);
    renderSnippets();
    loadFromUrl();

    $input.on('input', function () {
        if (syncing || !$.trim($(this).val())) return;
        try {
            applyFromMs(parseTimestampInput($(this).val()));
        } catch (e) { /* wait for full input */ }
    });

    $date.on('change', function () {
        if (syncing || !$(this).val()) return;
        try {
            applyFromMs(new Date($(this).val()).getTime());
        } catch (e) { /* ignore */ }
    });

    $('#ts-y, #ts-mo, #ts-d, #ts-h, #ts-mi, #ts-s').on('change blur', function () {
        if (syncing || !$('#ts-y').val()) return;
        try { applyFromParts(); } catch (e) { /* incomplete */ }
    });

    $('.ts-copyable').on('click keydown', function (e) {
        if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;
        if (e.type === 'keydown') e.preventDefault();
        copyWithTooltip($.trim($(this).text()), $(this));
    });

    $('#btn-ts-date-picker').on('click', openDatePicker);

    $('#btn-ts-convert').on('click', convert);
    $('#btn-use-now-ms').on('click', function () { useNow(true); });
    $('#btn-use-now-sec').on('click', function () { useNow(false); });
    $('#btn-ts-clear').on('click', clearAll);

    // Initial convert to show formats for current time
    useNow(true);
});
