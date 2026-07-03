$(function () {
    'use strict';

    var $input = $('#binary-input');

    $('#btn-binary-encode').on('click', function () {
        var text = $input.val();
        var binary = text.split('').map(function (ch) {
            return ch.charCodeAt(0).toString(2).padStart(8, '0');
        }).join(' ');
        $input.val(binary);
        $('#binary-error').addClass('hidden');
    });

    $('#btn-binary-decode').on('click', function () {
        var text = $.trim($input.val());
        if (!text) return;

        try {
            var bytes = text.split(/\s+/);
            var result = bytes.map(function (byte) {
                if (!/^[01]{1,8}$/.test(byte)) throw new Error('Invalid binary byte: ' + byte);
                return String.fromCharCode(parseInt(byte, 2));
            }).join('');
            $input.val(result);
            $('#binary-error').addClass('hidden');
        } catch (e) {
            $('#binary-error').text(e.message).removeClass('hidden');
        }
    });

    $('#btn-binary-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-binary-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-binary-clear').on('click', function () {
        $input.val('');
        $('#binary-error').addClass('hidden');
    });
});
