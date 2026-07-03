$(function () {
    'use strict';

    function fmt(n) {
        return '$' + n.toFixed(2);
    }

    $('#btn-tip-calc').on('click', function () {
        var bill = parseFloat($('#tip-bill').val());
        var pct = parseFloat($('#tip-percent').val());
        var people = parseInt($('#tip-people').val(), 10);

        if (isNaN(bill) || bill < 0 || isNaN(pct) || pct < 0 || isNaN(people) || people < 1) {
            $('#tip-error').text('Enter valid bill, tip percentage, and number of people.').removeClass('hidden');
            $('#tip-result').addClass('hidden');
            return;
        }

        var tip = bill * (pct / 100);
        var total = bill + tip;
        var perPerson = total / people;

        $('#tip-error').addClass('hidden');
        $('#tip-amount').text(fmt(tip));
        $('#tip-total').text(fmt(total));
        $('#tip-per-person').text(fmt(perPerson));
        $('#tip-result').removeClass('hidden');
    });

    $('#btn-tip-clear').on('click', function () {
        $('#tip-bill').val('');
        $('#tip-percent').val('15');
        $('#tip-people').val('1');
        $('#tip-error').addClass('hidden');
        $('#tip-result').addClass('hidden');
    });
});
