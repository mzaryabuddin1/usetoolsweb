$(function () {
    'use strict';

    function minifyCss(css) {
        return css
            .replace(/\/\*[\s\S]*?\*\//g, '')
            .replace(/\s+/g, ' ')
            .replace(/\s*([{}:;,>+~])\s*/g, '$1')
            .replace(/;}/g, '}')
            .trim();
    }

    $('#btn-css-minify').on('click', function () {
        var input = $('#css-input').val();
        if (!input) return;

        var output = minifyCss(input);
        $('#css-output').val(output);

        var before = new Blob([input]).size;
        var after = new Blob([output]).size;
        var saved = before > 0 ? Math.round((1 - after / before) * 100) : 0;

        $('#css-before').text(before);
        $('#css-after').text(after);
        $('#css-saved').text(saved + '%');
        $('#css-stats').removeClass('hidden');
    });

    $('#btn-css-copy').on('click', function () {
        var text = $('#css-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-css-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-css-clear').on('click', function () {
        $('#css-input, #css-output').val('');
        $('#css-stats').addClass('hidden');
    });
});
