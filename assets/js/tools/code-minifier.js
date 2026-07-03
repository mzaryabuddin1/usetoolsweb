$(function () {
    'use strict';

    var mode = 'html';

    function minifyHtml(html) {
        return html
            .replace(/<!--[\s\S]*?-->/g, '')
            .replace(/\s+/g, ' ')
            .replace(/>\s+</g, '><')
            .trim();
    }

    function minifyJs(js) {
        return js
            .replace(/\/\*[\s\S]*?\*\//g, '')
            .replace(/\/\/[^\n]*/g, '')
            .replace(/\s+/g, ' ')
            .replace(/\s*([{}();,:=+\-*\/<>!&|?\[\]])\s*/g, '$1')
            .trim();
    }

    $('.code-tab').on('click', function () {
        mode = $(this).data('tab');
        $('.code-tab').removeClass('active');
        $(this).addClass('active');
    });

    $('#btn-code-minify').on('click', function () {
        var input = $('#code-input').val();
        if (!input) return;
        var output = mode === 'html' ? minifyHtml(input) : minifyJs(input);
        $('#code-output').val(output);
    });

    $('#btn-code-copy').on('click', function () {
        var text = $('#code-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-code-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-code-clear').on('click', function () {
        $('#code-input, #code-output').val('');
    });
});
