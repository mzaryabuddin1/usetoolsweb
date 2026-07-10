(function () {
    'use strict';

    var $ = window.jQuery;
    var mode = 'css';

    function minifyCss(css) {
        return css.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\s+/g, ' ').replace(/\s*([{}:;,>+~])\s*/g, '$1').replace(/;}/g, '}').trim();
    }

    function minifyJs(js) {
        return js.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\/\/[^\n]*/g, '').replace(/\s+/g, ' ').replace(/\s*([{}();,:=+\-*\/<>!&|?\[\]])\s*/g, '$1').trim();
    }

    $(function () {
        $('.dev-tab').on('click', function () {
            mode = $(this).data('tab');
            $('.dev-tab').removeClass('active');
            $(this).addClass('active');
            $('#minify-input, #minify-output').val('');
            $('#minify-stats').addClass('hidden');
        });

        $('#btn-minify-run').on('click', function () {
            var input = $('#minify-input').val();
            if (!input.trim()) return;
            var output = mode === 'css' ? minifyCss(input) : minifyJs(input);
            $('#minify-output').val(output);
            var before = new Blob([input]).size;
            var after = new Blob([output]).size;
            var saved = before > 0 ? Math.round((1 - after / before) * 100) : 0;
            $('#minify-stats').text('Before: ' + before + ' B · After: ' + after + ' B · Saved ~' + saved + '%').removeClass('hidden');
        });

        $('#btn-minify-copy').on('click', function () {
            var text = $('#minify-output').val();
            if (text) navigator.clipboard.writeText(text);
        });

        $('#btn-minify-clear').on('click', function () {
            $('#minify-input, #minify-output').val('');
            $('#minify-stats').addClass('hidden');
        });
    });
})();
