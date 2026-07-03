$(function () {
    'use strict';

    var numerals = [
        [1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'],
        [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'],
        [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']
    ];

    function toRoman(num) {
        if (num < 1 || num > 3999 || !Number.isInteger(num)) {
            throw new Error('Number must be an integer between 1 and 3999.');
        }
        var result = '';
        numerals.forEach(function (pair) {
            while (num >= pair[0]) {
                result += pair[1];
                num -= pair[0];
            }
        });
        return result;
    }

    function fromRoman(str) {
        var roman = str.toUpperCase().trim();
        if (!/^[IVXLCDM]+$/.test(roman)) {
            throw new Error('Invalid Roman numeral characters.');
        }
        var total = 0;
        for (var i = 0; i < roman.length; i++) {
            var val = { I: 1, V: 5, X: 10, L: 50, C: 100, D: 500, M: 1000 }[roman[i]];
            var next = { I: 1, V: 5, X: 10, L: 50, C: 100, D: 500, M: 1000 }[roman[i + 1]];
            if (next && next > val) total -= val;
            else total += val;
        }
        if (total < 1 || total > 3999) throw new Error('Result out of range (1–3999).');
        if (toRoman(total) !== roman) throw new Error('Invalid Roman numeral format.');
        return total;
    }

    var $input = $('#roman-input');

    $('#btn-roman-to').on('click', function () {
        try {
            var num = parseInt($input.val(), 10);
            $input.val(toRoman(num));
            $('#roman-error').addClass('hidden');
        } catch (e) {
            $('#roman-error').text(e.message).removeClass('hidden');
        }
    });

    $('#btn-roman-from').on('click', function () {
        try {
            $input.val(String(fromRoman($input.val())));
            $('#roman-error').addClass('hidden');
        } catch (e) {
            $('#roman-error').text(e.message).removeClass('hidden');
        }
    });

    $('#btn-roman-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-roman-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-roman-clear').on('click', function () {
        $input.val('');
        $('#roman-error').addClass('hidden');
    });
});
