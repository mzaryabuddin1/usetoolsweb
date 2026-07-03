$(function () {
    'use strict';

    var robotsTxt = '';

    function linesFromTextarea(id) {
        return $(id).val().split('\n').map(function (l) { return $.trim(l); }).filter(Boolean);
    }

    $('#btn-robots-generate').on('click', function () {
        var ua = $.trim($('#robots-user-agent').val()) || '*';
        var disallow = linesFromTextarea('#robots-disallow');
        var allow = linesFromTextarea('#robots-allow');
        var sitemap = $.trim($('#robots-sitemap').val());
        var lines = ['User-agent: ' + ua];

        allow.forEach(function (path) {
            lines.push('Allow: ' + path);
        });

        if (disallow.length === 0) {
            lines.push('Disallow:');
        } else {
            disallow.forEach(function (path) {
                lines.push('Disallow: ' + path);
            });
        }

        if (sitemap) {
            lines.push('');
            lines.push('Sitemap: ' + sitemap);
        }

        robotsTxt = lines.join('\n') + '\n';
        $('#robots-output').val(robotsTxt);
        $('#btn-robots-download').prop('disabled', false);
    });

    $('#btn-robots-download').on('click', function () {
        if (!robotsTxt) return;
        var blob = new Blob([robotsTxt], { type: 'text/plain' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'robots.txt';
        link.click();
        URL.revokeObjectURL(link.href);
    });

    $('#btn-robots-copy').on('click', function () {
        var text = $('#robots-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-robots-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-robots-clear').on('click', function () {
        $('#robots-user-agent').val('*');
        $('#robots-disallow, #robots-allow, #robots-sitemap, #robots-output').val('');
        robotsTxt = '';
        $('#btn-robots-download').prop('disabled', true);
    });
});
