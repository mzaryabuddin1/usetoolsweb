$(function () {
    'use strict';

    var UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var LOWER = 'abcdefghijklmnopqrstuvwxyz';
    var NUMBERS = '0123456789';
    var SYMBOLS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    var $output = $('#password-output');
    var $length = $('#pw-length');
    var $lengthVal = $('#pw-length-value');
    var $error = $('#pw-error');

    function getCharset() {
        var charset = '';
        if ($('#pw-upper').is(':checked')) charset += UPPER;
        if ($('#pw-lower').is(':checked')) charset += LOWER;
        if ($('#pw-numbers').is(':checked')) charset += NUMBERS;
        if ($('#pw-symbols').is(':checked')) charset += SYMBOLS;
        return charset;
    }

    function generate() {
        var charset = getCharset();
        var len = parseInt($length.val(), 10);

        if (!charset) {
            $error.text('Select at least one character type.').removeClass('hidden');
            return;
        }

        $error.addClass('hidden');
        var array = new Uint32Array(len);
        crypto.getRandomValues(array);
        var password = '';
        for (var i = 0; i < len; i++) {
            password += charset[array[i] % charset.length];
        }
        $output.val(password);
    }

    $length.on('input', function () {
        $lengthVal.text($(this).val());
    });

    $('#btn-generate-password').on('click', generate);
    generate();

    $('#btn-copy-password').on('click', function () {
        var val = $output.val();
        if (!val) return;
        navigator.clipboard.writeText(val).then(function () {
            $('#btn-copy-password').text('Copied!');
            setTimeout(function () { $('#btn-copy-password').text('Copy'); }, 1500);
        });
    });
});
