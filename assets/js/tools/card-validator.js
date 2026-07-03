$(function () {
    'use strict';

    function luhnCheck(num) {
        var sum = 0;
        var alt = false;
        for (var i = num.length - 1; i >= 0; i--) {
            var n = parseInt(num[i], 10);
            if (alt) {
                n *= 2;
                if (n > 9) n -= 9;
            }
            sum += n;
            alt = !alt;
        }
        return sum % 10 === 0;
    }

    function detectType(num) {
        if (/^4/.test(num)) return 'Visa';
        if (/^5[1-5]/.test(num) || /^2[2-7]/.test(num)) return 'Mastercard';
        if (/^3[47]/.test(num)) return 'American Express';
        if (/^6(?:011|5)/.test(num)) return 'Discover';
        if (/^35/.test(num)) return 'JCB';
        if (/^3(?:0[0-5]|[68])/.test(num)) return 'Diners Club';
        return 'Unknown';
    }

    $('#btn-card-validate').on('click', function () {
        var raw = $('#card-number').val().replace(/\s+/g, '').replace(/-/g, '');
        if (!/^\d+$/.test(raw)) {
            $('#card-error').text('Enter digits only (spaces allowed).').removeClass('hidden');
            $('#card-result').addClass('hidden');
            return;
        }
        if (raw.length < 13 || raw.length > 19) {
            $('#card-error').text('Card number must be 13–19 digits.').removeClass('hidden');
            $('#card-result').addClass('hidden');
            return;
        }

        var valid = luhnCheck(raw);
        $('#card-error').addClass('hidden');
        $('#card-type').text(detectType(raw));
        $('#card-valid').text(valid ? 'Valid' : 'Invalid').css('color', valid ? 'var(--color-success)' : 'var(--color-error)');
        $('#card-result').removeClass('hidden');
    });

    $('#card-number').on('input', function () {
        var v = $(this).val().replace(/\D/g, '').substring(0, 19);
        var formatted = v.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
        $(this).val(formatted);
    });

    $('#btn-card-clear').on('click', function () {
        $('#card-number').val('');
        $('#card-error').addClass('hidden');
        $('#card-result').addClass('hidden');
    });
});
