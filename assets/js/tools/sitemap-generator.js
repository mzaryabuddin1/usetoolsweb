$(function () {
    'use strict';

    var sitemapXml = '';

    function escapeXml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    $('#btn-sitemap-generate').on('click', function () {
        var lines = $('#sitemap-urls').val().split('\n').map(function (l) { return $.trim(l); }).filter(Boolean);
        if (lines.length === 0) {
            $('#sitemap-error').text('Enter at least one URL.').removeClass('hidden');
            $('#btn-sitemap-download').prop('disabled', true);
            return;
        }

        var today = new Date().toISOString().split('T')[0];
        var urls = lines.map(function (url) {
            return '  <url>\n    <loc>' + escapeXml(url) + '</loc>\n    <lastmod>' + today + '</lastmod>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>';
        });

        sitemapXml = '<?xml version="1.0" encoding="UTF-8"?>\n' +
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' +
            urls.join('\n') + '\n</urlset>';

        $('#sitemap-output').val(sitemapXml);
        $('#sitemap-error').addClass('hidden');
        $('#btn-sitemap-download').prop('disabled', false);
    });

    $('#btn-sitemap-download').on('click', function () {
        if (!sitemapXml) return;
        var blob = new Blob([sitemapXml], { type: 'application/xml' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'sitemap.xml';
        link.click();
        URL.revokeObjectURL(link.href);
    });

    $('#btn-sitemap-copy').on('click', function () {
        var text = $('#sitemap-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-sitemap-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-sitemap-clear').on('click', function () {
        $('#sitemap-urls, #sitemap-output').val('');
        sitemapXml = '';
        $('#sitemap-error').addClass('hidden');
        $('#btn-sitemap-download').prop('disabled', true);
    });
});
