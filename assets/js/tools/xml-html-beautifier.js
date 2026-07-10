(function () {
    'use strict';

    var $ = window.jQuery;
    var mode = 'html';

    function beautifyHtml(html) {
        var formatted = '';
        var indent = 0;
        html = html.replace(/>\s+</g, '><').trim();
        var tokens = html.split(/(<[^>]+>)/g).filter(Boolean);
        tokens.forEach(function (token) {
            if (/^<\/\w/.test(token)) indent = Math.max(0, indent - 1);
            formatted += '  '.repeat(indent) + token.trim() + '\n';
            if (/^<\w[^>]*[^/]>$/.test(token) && !/^<(br|hr|img|input|meta|link)/i.test(token)) indent++;
        });
        return formatted.trim();
    }

    function minifyHtml(html) {
        return html.replace(/<!--[\s\S]*?-->/g, '').replace(/\s+/g, ' ').replace(/>\s+</g, '><').trim();
    }

    function beautifyXml(xml) {
        var formatted = '';
        var indent = 0;
        xml = xml.replace(/>\s+</g, '><').trim();
        var tokens = xml.split(/(<[^>]+>)/g).filter(Boolean);
        tokens.forEach(function (token) {
            if (/^<\//.test(token)) indent = Math.max(0, indent - 1);
            formatted += '  '.repeat(indent) + token.trim() + '\n';
            if (/^<[^!?/][^>]*[^/]>$/.test(token)) indent++;
        });
        return formatted.trim();
    }

    function minifyXml(xml) {
        return xml.replace(/<!--[\s\S]*?-->/g, '').replace(/>\s+</g, '><').replace(/\s+/g, ' ').trim();
    }

    function showStatus(msg, type) {
        $('#markup-status').removeClass('hidden alert-error alert-success').addClass('alert alert-' + type).text(msg);
    }

    $(function () {
        $('.dev-tab').on('click', function () {
            mode = $(this).data('tab');
            $('.dev-tab').removeClass('active');
            $(this).addClass('active');
        });

        $('#btn-markup-beautify').on('click', function () {
            var input = $('#markup-input').val();
            if (!input.trim()) return;
            try {
                var out = mode === 'html' ? beautifyHtml(input) : beautifyXml(input);
                $('#markup-input').val(out);
                showStatus((mode === 'html' ? 'HTML' : 'XML') + ' beautified.', 'success');
            } catch (e) {
                showStatus(e.message, 'error');
            }
        });

        $('#btn-markup-minify').on('click', function () {
            var input = $('#markup-input').val();
            if (!input.trim()) return;
            $('#markup-input').val(mode === 'html' ? minifyHtml(input) : minifyXml(input));
            showStatus((mode === 'html' ? 'HTML' : 'XML') + ' minified.', 'success');
        });

        $('#btn-markup-copy').on('click', function () {
            var text = $('#markup-input').val();
            if (!text) return;
            navigator.clipboard.writeText(text);
        });

        $('#btn-markup-clear').on('click', function () {
            $('#markup-input').val('');
            $('#markup-status').addClass('hidden');
        });
    });
})();
