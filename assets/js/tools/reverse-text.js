$(function () {
    'use strict';

    var $input = $('#reverse-input');

    $('[data-reverse]').on('click', function () {
        var mode = $(this).data('reverse');
        var text = $input.val();
        if (!text) return;

        if (mode === 'chars') {
            $input.val(text.split('').reverse().join(''));
        } else if (mode === 'words') {
            $input.val(text.trim().split(/\s+/).reverse().join(' '));
        } else {
            $input.val(text.split('\n').reverse().join('\n'));
        }
    });

    $('#btn-reverse-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-reverse-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-reverse-clear').on('click', function () {
        $input.val('');
    });
});
