(function () {
    'use strict';

    var $ = window.jQuery;
    var running = false;

    function fmtMbps(bytes, ms) {
        if (!ms || ms <= 0) return '—';
        var bits = bytes * 8;
        var mbps = bits / (ms / 1000) / 1000000;
        return mbps.toFixed(2) + ' Mbps';
    }

    function setProgress(pct, text) {
        $('#speed-progress').removeClass('hidden');
        $('#speed-progress-fill').css('width', pct + '%');
        $('#speed-status').text(text);
    }

    async function measurePing(samples) {
        var times = [];
        for (var i = 0; i < samples; i++) {
            var start = performance.now();
            await fetch('/api/speed-test.php?size=65536&r=' + Date.now(), { cache: 'no-store' });
            times.push(performance.now() - start);
        }
        times.sort(function (a, b) { return a - b; });
        var avg = times.reduce(function (a, b) { return a + b; }, 0) / times.length;
        var jitter = times.length > 1 ? Math.abs(times[times.length - 1] - times[0]) : 0;
        return { ping: Math.round(avg), jitter: Math.round(jitter) };
    }

    async function measureDownload() {
        var size = 1048576;
        var start = performance.now();
        var res = await fetch('/api/speed-test.php?size=' + size + '&r=' + Date.now(), { cache: 'no-store' });
        var blob = await res.blob();
        var ms = performance.now() - start;
        return { bytes: blob.size, ms: ms };
    }

    async function measureUpload() {
        var payload = new Uint8Array(524288);
        var start = performance.now();
        await fetch('/api/speed-test.php', {
            method: 'POST',
            body: payload,
            cache: 'no-store'
        });
        var ms = performance.now() - start;
        return { bytes: payload.length, ms: ms };
    }

    async function runTest() {
        if (running) return;
        running = true;
        $('#btn-speed-start').prop('disabled', true);
        $('#speed-main-value').text('…');

        try {
            setProgress(10, 'Measuring ping…');
            var ping = await measurePing(5);
            $('#speed-ping').text(ping.ping + ' ms');
            $('#speed-jitter').text(ping.jitter + ' ms');

            setProgress(40, 'Testing download…');
            var down = await measureDownload();
            var downStr = fmtMbps(down.bytes, down.ms);
            $('#speed-download').text(downStr);
            $('#speed-main-value').text(downStr.replace(' Mbps', ''));
            $('#speed-main-label').text('Mbps download');

            setProgress(75, 'Testing upload…');
            var up = await measureUpload();
            var upStr = fmtMbps(up.bytes, up.ms);
            $('#speed-upload').text(upStr);

            setProgress(100, 'Test complete.');
        } catch (e) {
            $('#speed-status').text('Test failed: ' + (e.message || 'unknown error'));
        } finally {
            running = false;
            $('#btn-speed-start').prop('disabled', false);
        }
    }

    $(function () {
        $('#btn-speed-start').on('click', runTest);
    });
})();
