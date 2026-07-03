$(function () {
    'use strict';

    $('#btn-dice-roll').on('click', function () {
        var sides = parseInt($('#dice-type').val(), 10);
        var count = parseInt($('#dice-count').val(), 10);
        if (isNaN(count) || count < 1 || count > 100) return;

        var rolls = [];
        for (var i = 0; i < count; i++) {
            rolls.push(Math.floor(Math.random() * sides) + 1);
        }

        var $results = $('#dice-results');
        $results.empty();
        rolls.forEach(function (r) {
            $results.append('<span class="dice-die">' + r + '</span>');
        });
        $results.removeClass('hidden');

        var total = rolls.reduce(function (a, b) { return a + b; }, 0);
        $('#dice-total').text(total);
        $('#dice-avg').text((total / count).toFixed(1));
        $('#dice-stats').removeClass('hidden');
    });

    $('#btn-dice-clear').on('click', function () {
        $('#dice-count').val('1');
        $('#dice-type').val('6');
        $('#dice-results').empty().addClass('hidden');
        $('#dice-stats').addClass('hidden');
    });
});
