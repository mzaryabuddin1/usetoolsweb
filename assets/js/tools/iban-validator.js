$(function () {
    'use strict';

    function mod97(iban) {
        var rearranged = iban.substring(4) + iban.substring(0, 4);
        var numeric = '';
        for (var i = 0; i < rearranged.length; i++) {
            var c = rearranged.charCodeAt(i);
            numeric += c >= 65 && c <= 90 ? (c - 55).toString() : rearranged[i];
        }
        var remainder = numeric;
        while (remainder.length > 2) {
            var block = remainder.substring(0, 9);
            remainder = (parseInt(block, 10) % 97).toString() + remainder.substring(block.length);
        }
        return parseInt(remainder, 10) % 97;
    }

    $('#btn-iban-validate').on('click', function () {
        var iban = $('#iban-input').val().replace(/\s+/g, '').toUpperCase();

        if (!iban) {
            $('#iban-error').text('Enter an IBAN.').removeClass('hidden');
            $('#iban-result').addClass('hidden');
            return;
        }

        if (!/^[A-Z]{2}\d{2}[A-Z0-9]+$/.test(iban)) {
            $('#iban-error').text('Invalid IBAN format. Must start with 2 letters, 2 digits, then alphanumeric.').removeClass('hidden');
            $('#iban-result').addClass('hidden');
            return;
        }

        if (iban.length < 15 || iban.length > 34) {
            $('#iban-error').text('IBAN length must be between 15 and 34 characters.').removeClass('hidden');
            $('#iban-result').addClass('hidden');
            return;
        }

        var valid = mod97(iban) === 1;
        $('#iban-error').addClass('hidden');
        $('#iban-status')
            .text(valid ? 'Valid IBAN — mod-97 check passed.' : 'Invalid IBAN — mod-97 check failed.')
            .removeClass('alert-success alert-error')
            .addClass(valid ? 'alert-success' : 'alert-error');
        $('#iban-country').text(iban.substring(0, 2));
        $('#iban-length').text(iban.length);
        $('#iban-result').removeClass('hidden');
    });

    $('#iban-input').on('input', function () {
        $(this).val($(this).val().toUpperCase());
    });

    $('#btn-iban-clear').on('click', function () {
        $('#iban-input').val('');
        $('#iban-error').addClass('hidden');
        $('#iban-result').addClass('hidden');
    });
});
