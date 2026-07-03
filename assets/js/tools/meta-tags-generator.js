$(function () {
    'use strict';

    function escapeAttr(str) {
        return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function generate() {
        var title = $.trim($('#meta-title').val());
        var desc = $.trim($('#meta-description').val());
        var url = $.trim($('#meta-url').val());
        var image = $.trim($('#meta-image').val());
        var lines = [];

        if (title) {
            lines.push('<title>' + escapeAttr(title) + '</title>');
            lines.push('<meta name="title" content="' + escapeAttr(title) + '">');
            lines.push('<meta property="og:title" content="' + escapeAttr(title) + '">');
            lines.push('<meta name="twitter:title" content="' + escapeAttr(title) + '">');
        }
        if (desc) {
            lines.push('<meta name="description" content="' + escapeAttr(desc) + '">');
            lines.push('<meta property="og:description" content="' + escapeAttr(desc) + '">');
            lines.push('<meta name="twitter:description" content="' + escapeAttr(desc) + '">');
        }
        if (url) {
            lines.push('<link rel="canonical" href="' + escapeAttr(url) + '">');
            lines.push('<meta property="og:url" content="' + escapeAttr(url) + '">');
        }
        if (image) {
            lines.push('<meta property="og:image" content="' + escapeAttr(image) + '">');
            lines.push('<meta name="twitter:image" content="' + escapeAttr(image) + '">');
        }
        lines.push('<meta property="og:type" content="website">');
        lines.push('<meta name="twitter:card" content="summary_large_image">');

        $('#meta-output').val(lines.join('\n'));
    }

    $('#btn-meta-generate').on('click', generate);
    $('#meta-title, #meta-description, #meta-url, #meta-image').on('input', generate);

    $('#btn-meta-copy').on('click', function () {
        var text = $('#meta-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-meta-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-meta-clear').on('click', function () {
        $('#meta-title, #meta-description, #meta-url, #meta-image, #meta-output').val('');
    });
});
