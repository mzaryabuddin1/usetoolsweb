$(function () {
    'use strict';

    function getCategory(bmi) {
        if (bmi < 18.5) return 'Underweight';
        if (bmi < 25) return 'Normal weight';
        if (bmi < 30) return 'Overweight';
        return 'Obese';
    }

    $('#btn-bmi-calc').on('click', function () {
        var height = parseFloat($('#bmi-height').val());
        var weight = parseFloat($('#bmi-weight').val());

        if (isNaN(height) || isNaN(weight) || height <= 0 || weight <= 0) {
            $('#bmi-error').text('Enter valid height and weight values.').removeClass('hidden');
            $('#bmi-result').addClass('hidden');
            return;
        }

        var heightM = height / 100;
        var bmi = weight / (heightM * heightM);

        $('#bmi-error').addClass('hidden');
        $('#bmi-value').text(bmi.toFixed(1));
        $('#bmi-category').text(getCategory(bmi));
        $('#bmi-result').removeClass('hidden');
    });

    $('#btn-bmi-clear').on('click', function () {
        $('#bmi-height, #bmi-weight').val('');
        $('#bmi-error').addClass('hidden');
        $('#bmi-result').addClass('hidden');
    });
});
