(function () {
    'use strict';

    var $ = window.jQuery;
    var lastReport = null;

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function scoreClass(score) {
        if (score >= 90) return 'audit-score-good';
        if (score >= 50) return 'audit-score-warn';
        return 'audit-score-bad';
    }

    function renderChecks(checks) {
        if (!checks || !checks.length) return '';
        return '<ul class="audit-check-list">' + checks.map(function (c) {
            return '<li class="audit-check audit-check-' + c.status + '"><strong>' + escapeHtml(c.label) + '</strong>' +
                (c.detail ? '<span class="hint"> — ' + escapeHtml(c.detail) + '</span>' : '') + '</li>';
        }).join('') + '</ul>';
    }

    function renderSeo(report) {
        var html = '<div class="audit-score-card ' + scoreClass(report.score) + '"><div class="audit-score-num">' + report.score + '</div><div>SEO score — ' + escapeHtml(report.summary) + '</div></div>';
        html += '<h3>Metrics</h3><div class="audit-kv">' +
            Object.keys(report.metrics).map(function (k) {
                return '<div><span>' + escapeHtml(k.replace(/_/g, ' ')) + '</span><strong>' + escapeHtml(String(report.metrics[k])) + '</strong></div>';
            }).join('') + '</div>';
        html += '<h3>Meta</h3><pre class="audit-log">' + escapeHtml(JSON.stringify(report.meta, null, 2)) + '</pre>';
        html += '<h3>Checks</h3>' + renderChecks(report.checks);
        return html;
    }

    function renderVapt(report) {
        var html = '<div class="audit-score-card ' + scoreClass(report.score) + '"><div class="audit-score-num">' + report.score + '</div><div>Security score — ' + escapeHtml(report.summary) + '</div></div>';
        html += '<p class="hint">' + escapeHtml(report.disclaimer || '') + '</p>';
        html += '<h3>Security headers</h3><pre class="audit-log">' + escapeHtml(JSON.stringify(report.headers, null, 2)) + '</pre>';
        html += '<h3>Checks</h3>' + renderChecks(report.checks);
        return html;
    }

    function renderLighthouse(report) {
        var html = '<div class="audit-score-card ' + scoreClass(report.score) + '"><div class="audit-score-num">' + report.score + '</div><div>Overall — ' + escapeHtml(report.summary) + '</div></div>';
        html += '<p class="hint">' + escapeHtml(report.disclaimer || '') + '</p>';
        html += '<div class="audit-categories">';
        Object.keys(report.categories).forEach(function (cat) {
            html += '<div class="audit-cat"><span>' + escapeHtml(cat.replace(/_/g, ' ')) + '</span><strong class="' + scoreClass(report.categories[cat]) + '">' + report.categories[cat] + '</strong></div>';
        });
        html += '</div>';
        Object.keys(report.checks).forEach(function (cat) {
            html += '<h3>' + escapeHtml(cat.replace(/_/g, ' ')) + '</h3>' + renderChecks(report.checks[cat]);
        });
        return html;
    }

    function renderReport(report) {
        if (report.type === 'seo') return renderSeo(report);
        if (report.type === 'vapt') return renderVapt(report);
        return renderLighthouse(report);
    }

    function downloadReport() {
        if (!lastReport) return;
        var blob = new Blob([JSON.stringify(lastReport, null, 2)], { type: 'application/json' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = lastReport.type + '-report.json';
        a.click();
    }

    async function runAudit() {
        var type = $('.dev-tool-panel[data-audit-type]').data('audit-type');
        var url = $.trim($('#audit-url').val());
        $('#audit-error').addClass('hidden');
        $('#audit-result').addClass('hidden');

        if (!url) {
            $('#audit-error').text('Enter a URL.').removeClass('hidden');
            return;
        }

        var $btn = $('#btn-audit-run');
        $btn.prop('disabled', true).text('Analyzing…');

        try {
            var res = await fetch('/api/site-audit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ url: url, type: type })
            });
            var data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.error || 'Audit failed.');
            }
            lastReport = data.report;
            $('#audit-result').html(renderReport(data.report)).removeClass('hidden');
        } catch (e) {
            $('#audit-error').text(e.message).removeClass('hidden');
        } finally {
            var type = $('.dev-tool-panel[data-audit-type]').data('audit-type');
            var labels = { seo: 'Generate SEO report', vapt: 'Generate VAPT report', lighthouse: 'Run audit' };
            $btn.prop('disabled', false).text(labels[type] || 'Generate report');
        }
    }

    $(function () {
        var type = $('.dev-tool-panel[data-audit-type]').data('audit-type');
        if (type === 'seo') $('#btn-audit-run').text('Generate SEO report');
        if (type === 'vapt') $('#btn-audit-run').text('Generate VAPT report');
        $('#btn-audit-run').on('click', runAudit);
        $('#btn-audit-download').on('click', downloadReport);
    });
})();
