$(function () {
    'use strict';

    function fmt(n) {
        return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $('#btn-emi-calc').on('click', function () {
        var principal = parseFloat($('#emi-amount').val());
        var rate = parseFloat($('#emi-rate').val());
        var tenure = parseFloat($('#emi-tenure').val());
        var unit = $('#emi-tenure-type').val();

        if (isNaN(principal) || principal <= 0 || isNaN(rate) || rate < 0 || isNaN(tenure) || tenure <= 0) {
            $('#emi-error').text('Enter valid loan amount, rate, and tenure.').removeClass('hidden');
            $('#emi-result').addClass('hidden');
            return;
        }

        var months = unit === 'years' ? tenure * 12 : tenure;
        var monthlyRate = rate / 12 / 100;
        var emi;

        if (monthlyRate === 0) {
            emi = principal / months;
        } else {
            emi = principal * monthlyRate * Math.pow(1 + monthlyRate, months) / (Math.pow(1 + monthlyRate, months) - 1);
        }

        var total = emi * months;
        var interest = total - principal;

        $('#emi-error').addClass('hidden');
        $('#emi-monthly').text(fmt(emi));
        $('#emi-interest').text(fmt(interest));
        $('#emi-total').text(fmt(total));
        $('#emi-result').removeClass('hidden');
    });

    $('#btn-emi-clear').on('click', function () {
        $('#emi-amount, #emi-rate, #emi-tenure').val('');
        $('#emi-tenure-type').val('months');
        $('#emi-error').addClass('hidden');
        $('#emi-result').addClass('hidden');
    });
});
