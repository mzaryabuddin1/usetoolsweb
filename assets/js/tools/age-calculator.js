$(function () {
    'use strict';

    function calcAge(birth) {
        var today = new Date();
        var years = today.getFullYear() - birth.getFullYear();
        var months = today.getMonth() - birth.getMonth();
        var days = today.getDate() - birth.getDate();

        if (days < 0) {
            months--;
            var prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            days += prevMonth.getDate();
        }
        if (months < 0) {
            years--;
            months += 12;
        }

        var totalDays = Math.floor((today - birth) / (1000 * 60 * 60 * 24));
        return { years: years, months: months, days: days, totalDays: totalDays };
    }

    $('#btn-age-calc').on('click', function () {
        var val = $('#age-dob').val();
        if (!val) {
            $('#age-error').text('Please select a date of birth.').removeClass('hidden');
            $('#age-result').addClass('hidden');
            return;
        }

        var birth = new Date(val + 'T00:00:00');
        var today = new Date();
        today.setHours(0, 0, 0, 0);

        if (birth > today) {
            $('#age-error').text('Date of birth cannot be in the future.').removeClass('hidden');
            $('#age-result').addClass('hidden');
            return;
        }

        $('#age-error').addClass('hidden');
        var age = calcAge(birth);
        $('#age-years').text(age.years);
        $('#age-months').text(age.months);
        $('#age-days').text(age.days);
        $('#age-total-days').text(age.totalDays.toLocaleString());
        $('#age-result').removeClass('hidden');
    });

    $('#btn-age-clear').on('click', function () {
        $('#age-dob').val('');
        $('#age-error').addClass('hidden');
        $('#age-result').addClass('hidden');
    });
});
