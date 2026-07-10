(function () {
    'use strict';

    var $ = window.jQuery;
    var stopped = false;

    function showError(msg) {
        $('#stress-error').text(msg).removeClass('hidden');
    }

    function hideError() {
        $('#stress-error').addClass('hidden');
    }

    async function oneRequest(url, method) {
        var start = performance.now();
        var res = await fetch('/api/curl-proxy.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ method: method, url: url, headers: {}, body: '' })
        });
        var data = await res.json();
        var ms = data.time_ms || Math.round(performance.now() - start);
        return {
            ok: data.ok && data.status >= 200 && data.status < 500,
            status: data.status || 0,
            ms: ms,
            error: data.error || null
        };
    }

    async function runPool(tasks, concurrency) {
        var index = 0;
        var results = [];

        async function worker() {
            while (index < tasks.length && !stopped) {
                var i = index++;
                results[i] = await tasks[i]();
            }
        }

        var workers = [];
        for (var w = 0; w < concurrency; w++) workers.push(worker());
        await Promise.all(workers);
        return results;
    }

    async function startTest() {
        hideError();
        stopped = false;
        var url = $.trim($('#stress-url').val());
        var total = Math.min(200, Math.max(1, parseInt($('#stress-requests').val(), 10) || 20));
        var concurrency = Math.min(20, Math.max(1, parseInt($('#stress-concurrency').val(), 10) || 5));
        var method = $('#stress-method').val();

        if (!url) {
            showError('Enter a target URL.');
            return;
        }

        $('#btn-stress-start').prop('disabled', true);
        $('#btn-stress-stop').prop('disabled', false);
        $('#stress-summary').removeClass('hidden');
        $('#stress-log').text('Running…');

        var tasks = [];
        for (var i = 0; i < total; i++) {
            tasks.push(function () { return oneRequest(url, method); });
        }

        var results = await runPool(tasks, concurrency);
        if (stopped) {
            $('#stress-log').text('Stopped.');
        }

        var ok = 0, fail = 0, times = [], log = [];
        results.forEach(function (r, idx) {
            if (!r) return;
            if (r.ok) ok++; else fail++;
            times.push(r.ms);
            log.push('#' + (idx + 1) + ' ' + (r.ok ? 'OK' : 'FAIL') + ' ' + r.status + ' — ' + r.ms + ' ms' + (r.error ? ' — ' + r.error : ''));
        });

        var avg = times.length ? Math.round(times.reduce(function (a, b) { return a + b; }, 0) / times.length) : 0;
        var max = times.length ? Math.max.apply(null, times) : 0;

        $('#stress-ok').text(ok);
        $('#stress-fail').text(fail);
        $('#stress-avg').text(avg + ' ms');
        $('#stress-max').text(max + ' ms');
        $('#stress-log').text(log.join('\n'));

        $('#btn-stress-start').prop('disabled', false);
        $('#btn-stress-stop').prop('disabled', true);
    }

    $(function () {
        $('#btn-stress-start').on('click', startTest);
        $('#btn-stress-stop').on('click', function () {
            stopped = true;
            $('#btn-stress-stop').prop('disabled', true);
        });
    });
})();
