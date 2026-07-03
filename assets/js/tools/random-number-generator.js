$(function () {
    'use strict';

    $('#btn-rng-generate').on('click', function () {
        var min = parseInt($('#rng-min').val(), 10);
        var max = parseInt($('#rng-max').val(), 10);
        var count = parseInt($('#rng-count').val(), 10);

        if (isNaN(min) || isNaN(max) || isNaN(count) || count < 1 || count > 1000) {
            $('#rng-error').text('Enter valid min, max, and count (1–1000).').removeClass('hidden');
            return;
        }
        if (min > max) {
            $('#rng-error').text('Minimum cannot be greater than maximum.').removeClass('hidden');
            return;
        }

        var results = [];
        for (var i = 0; i < count; i++) {
            results.push(Math.floor(Math.random() * (max - min + 1)) + min);
        }

        $('#rng-output').val(results.join('\n'));
        $('#rng-error').addClass('hidden');
    });

    $('#btn-rng-copy').on('click', function () {
        var text = $('#rng-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-rng-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-rng-clear').on('click', function () {
        $('#rng-min').val('1');
        $('#rng-max').val('100');
        $('#rng-count').val('1');
        $('#rng-output').val('');
        $('#rng-error').addClass('hidden');
    });
});
