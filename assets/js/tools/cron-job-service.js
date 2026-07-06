(function () {
    'use strict';

    var STORAGE_KEY = 'utw_cron_jobs_v1';
    var MAX_LOGS = 40;
    var MAX_PREVIEW = 3000;
    var timers = {};

    var $ = window.jQuery;

    function uid() {
        return 'cj_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 9);
    }

    function loadJobs() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return [];
            var data = JSON.parse(raw);
            return Array.isArray(data) ? data : [];
        } catch (e) {
            return [];
        }
    }

    function saveJobs(jobs) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(jobs));
    }

    function getJob(id) {
        return loadJobs().find(function (j) { return j.id === id; });
    }

    function updateJob(id, patch) {
        var jobs = loadJobs();
        var idx = jobs.findIndex(function (j) { return j.id === id; });
        if (idx === -1) return;
        Object.assign(jobs[idx], patch);
        saveJobs(jobs);
    }

    function removeJob(id) {
        stopTimer(id);
        var jobs = loadJobs().filter(function (j) { return j.id !== id; });
        saveJobs(jobs);
        renderJobs();
    }

    function truncate(str, max) {
        if (!str) return '';
        return str.length > max ? str.slice(0, max) + '…' : str;
    }

    function formatTime(iso) {
        if (!iso) return '—';
        try {
            return new Date(iso).toLocaleString();
        } catch (e) {
            return iso;
        }
    }

    function intervalSeconds() {
        var sel = $('#cron-interval').val();
        if (sel === 'custom') {
            return Math.max(1, Math.min(86400, parseInt($('#cron-custom-sec').val(), 10) || 60));
        }
        return parseInt(sel, 10) || 300;
    }

    function showFormError(msg) {
        $('#cron-form-error').text(msg).removeClass('hidden');
    }

    function hideFormError() {
        $('#cron-form-error').addClass('hidden');
    }

    async function executeRequest(job) {
        var start = performance.now();
        var log = {
            at: new Date().toISOString(),
            status: 0,
            ok: false,
            durationMs: 0,
            responsePreview: '',
            error: null
        };

        try {
            if (job.mode === 'server') {
                var res = await fetch('/api/curl-proxy.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        method: job.method,
                        url: job.url,
                        headers: {},
                        body: job.body || ''
                    })
                });
                var data = await res.json();
                log.durationMs = data.time_ms || Math.round(performance.now() - start);

                if (!data.ok) {
                    log.error = data.error || 'Proxy error';
                    log.responsePreview = log.error;
                } else {
                    log.status = data.status;
                    log.ok = data.status >= 200 && data.status < 300;
                    log.responsePreview = truncate(String(data.body || ''), MAX_PREVIEW);
                }
            } else {
                var opts = { method: job.method, headers: {} };
                if (['POST', 'PUT', 'PATCH', 'DELETE'].indexOf(job.method) !== -1 && job.body) {
                    opts.body = job.body;
                    opts.headers['Content-Type'] = 'application/json';
                }
                var res2 = await fetch(job.url, opts);
                log.durationMs = Math.round(performance.now() - start);
                log.status = res2.status;
                log.ok = res2.ok;
                var text = await res2.text();
                log.responsePreview = truncate(text, MAX_PREVIEW);
            }
        } catch (err) {
            log.durationMs = Math.round(performance.now() - start);
            log.error = err.message || 'Request failed';
            log.responsePreview = log.error;
        }

        return log;
    }

    async function runJob(id, manual) {
        var job = getJob(id);
        if (!job || job.running) return;

        updateJob(id, { running: true, lastRun: new Date().toISOString() });
        renderJobs();

        var log = await executeRequest(job);

        var jobs = loadJobs();
        var idx = jobs.findIndex(function (j) { return j.id === id; });
        if (idx === -1) return;

        jobs[idx].running = false;
        jobs[idx].lastRun = log.at;
        jobs[idx].lastStatus = log.ok ? 'success' : 'error';
        jobs[idx].logs = jobs[idx].logs || [];
        jobs[idx].logs.unshift(log);
        if (jobs[idx].logs.length > MAX_LOGS) {
            jobs[idx].logs = jobs[idx].logs.slice(0, MAX_LOGS);
        }
        saveJobs(jobs);
        renderJobs();

        if (!manual && jobs[idx].enabled) {
            scheduleJob(jobs[idx]);
        }
    }

    function stopTimer(id) {
        if (timers[id]) {
            clearTimeout(timers[id]);
            delete timers[id];
        }
    }

    function scheduleJob(job) {
        stopTimer(job.id);
        if (!job.enabled) return;

        timers[job.id] = setTimeout(function () {
            runJob(job.id, false);
        }, (job.intervalSec || 300) * 1000);
    }

    function startAllTimers() {
        loadJobs().forEach(function (job) {
            if (job.enabled) scheduleJob(job);
        });
    }

    function escapeHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderLogRow(log) {
        var statusClass = log.ok ? 'cron-log-ok' : 'cron-log-fail';
        var statusLabel = log.error ? 'Error' : (log.status || '—');
        var preview = log.responsePreview || log.error || '';
        return '<tr class="' + statusClass + '">' +
            '<td>' + escapeHtml(formatTime(log.at)) + '</td>' +
            '<td>' + escapeHtml(String(statusLabel)) + '</td>' +
            '<td>' + log.durationMs + ' ms</td>' +
            '<td><pre class="cron-log-preview">' + escapeHtml(preview) + '</pre></td>' +
            '</tr>';
    }

    function renderJobCard(job) {
        var name = job.name || job.url;
        var statusBadge = job.running
            ? '<span class="cron-badge cron-badge-run">Running…</span>'
            : (job.enabled
                ? '<span class="cron-badge cron-badge-on">Active</span>'
                : '<span class="cron-badge cron-badge-off">Paused</span>');
        var lastStatus = job.lastStatus === 'success'
            ? '<span class="cron-last-ok">Last: OK</span>'
            : (job.lastStatus === 'error' ? '<span class="cron-last-fail">Last: Failed</span>' : '');

        var logs = job.logs || [];
        var logsHtml = logs.length
            ? '<table class="cron-log-table"><thead><tr><th>Time</th><th>Status</th><th>Duration</th><th>Response</th></tr></thead><tbody>' +
              logs.map(renderLogRow).join('') + '</tbody></table>'
            : '<p class="hint">No runs yet.</p>';

        return '<article class="cron-job-card" data-id="' + escapeHtml(job.id) + '">' +
            '<div class="cron-job-head">' +
            '<div class="cron-job-title">' +
            '<strong>' + escapeHtml(name) + '</strong> ' + statusBadge +
            '<div class="cron-job-meta">' +
            '<code>' + escapeHtml(job.method) + '</code> · every ' + job.intervalSec + 's · ' +
            escapeHtml(job.mode === 'server' ? 'server proxy' : 'browser') +
            '</div>' +
            '<div class="cron-job-url hint">' + escapeHtml(job.url) + '</div>' +
            '</div>' +
            '<div class="cron-job-actions">' +
            '<button type="button" class="btn btn-primary btn-sm cron-btn-run" data-id="' + job.id + '">Run now</button>' +
            '<button type="button" class="btn btn-secondary btn-sm cron-btn-toggle" data-id="' + job.id + '">' +
            (job.enabled ? 'Pause' : 'Resume') + '</button>' +
            '<button type="button" class="btn btn-secondary btn-sm cron-btn-remove" data-id="' + job.id + '">Remove</button>' +
            '</div>' +
            '</div>' +
            '<div class="cron-job-stats">' +
            '<span>Last run: ' + formatTime(job.lastRun) + '</span> ' + lastStatus +
            '</div>' +
            '<details class="cron-job-logs">' +
            '<summary>Request / response logs (' + logs.length + ')</summary>' +
            '<div class="cron-logs-body">' + logsHtml + '</div>' +
            '</details>' +
            '</article>';
    }

    function renderJobs() {
        var jobs = loadJobs();
        $('#cron-job-count').text(jobs.length + ' job' + (jobs.length === 1 ? '' : 's'));
        $('#cron-jobs-empty').toggleClass('hidden', jobs.length > 0);
        $('#cron-jobs-list').html(jobs.map(renderJobCard).join(''));
    }

    function addJob() {
        hideFormError();
        var url = $('#cron-url').val().trim();
        if (!url) {
            showFormError('Enter a URL.');
            return;
        }
        try {
            new URL(url);
        } catch (e) {
            showFormError('Enter a valid URL (include https://).');
            return;
        }

        var job = {
            id: uid(),
            name: $('#cron-name').val().trim(),
            url: url,
            method: $('#cron-method').val(),
            body: $('#cron-body').val(),
            intervalSec: intervalSeconds(),
            mode: $('input[name="cron-mode"]:checked').val() || 'server',
            enabled: true,
            running: false,
            createdAt: new Date().toISOString(),
            lastRun: null,
            lastStatus: null,
            logs: []
        };

        var jobs = loadJobs();
        jobs.unshift(job);
        saveJobs(jobs);
        renderJobs();
        scheduleJob(job);

        $('#cron-name, #cron-url, #cron-body').val('');
    }

    $(function () {
        renderJobs();
        startAllTimers();

        $('#cron-interval').on('change', function () {
            $('#cron-custom-wrap').toggleClass('hidden', $(this).val() !== 'custom');
        });

        $('#btn-cron-add').on('click', addJob);

        $('#btn-cron-clear-form').on('click', function () {
            $('#cron-name, #cron-url, #cron-body').val('');
            $('#cron-method').val('GET');
            $('#cron-interval').val('300');
            $('#cron-custom-wrap').addClass('hidden');
            hideFormError();
        });

        $('#cron-jobs-list').on('click', '.cron-btn-run', function () {
            runJob($(this).data('id'), true);
        });

        $('#cron-jobs-list').on('click', '.cron-btn-toggle', function () {
            var id = $(this).data('id');
            var job = getJob(id);
            if (!job) return;
            updateJob(id, { enabled: !job.enabled });
            if (!job.enabled) {
                scheduleJob(getJob(id));
            } else {
                stopTimer(id);
            }
            renderJobs();
        });

        $('#cron-jobs-list').on('click', '.cron-btn-remove', function () {
            if (window.confirm('Remove this cron job and its logs?')) {
                removeJob($(this).data('id'));
            }
        });

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                startAllTimers();
            }
        });
    });
})();
