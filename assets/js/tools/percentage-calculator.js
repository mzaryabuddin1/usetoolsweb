$(function () {
    'use strict';

    var mode = 'of';

    function showError(msg) {
        $('#pct-error').text(msg).removeClass('hidden');
        $('#pct-result').addClass('hidden');
    }

    function hideError() {
        $('#pct-error').addClass('hidden');
    }

    $('.pct-mode').on('click', function () {
        mode = $(this).data('mode');
        $('.pct-mode').removeClass('active');
        $(this).addClass('active');
        $('.pct-panel').addClass('hidden');
        $('#pct-panel-' + mode).removeClass('hidden');
        hideError();
        $('#pct-result').addClass('hidden');
    });

    $('#btn-pct-calc').on('click', function () {
        hideError();
        var result, label;

        if (mode === 'of') {
            var pct = parseFloat($('#pct-of-percent').val());
            var val = parseFloat($('#pct-of-value').val());
            if (isNaN(pct) || isNaN(val)) return showError('Enter valid numbers for percentage and value.');
            result = (pct / 100) * val;
            label = pct + '% of ' + val;
        } else if (mode === 'is') {
            var x = parseFloat($('#pct-is-x').val());
            var y = parseFloat($('#pct-is-y').val());
            if (isNaN(x) || isNaN(y) || y === 0) return showError('Enter valid numbers. Y cannot be zero.');
            result = (x / y) * 100;
            label = x + ' is what % of ' + y;
        } else {
            var orig = parseFloat($('#pct-change-value').val());
            var changePct = parseFloat($('#pct-change-percent').val());
            var type = $('#pct-change-type').val();
            if (isNaN(orig) || isNaN(changePct)) return showError('Enter valid numbers.');
            var delta = orig * (changePct / 100);
            result = type === 'increase' ? orig + delta : orig - delta;
            label = (type === 'increase' ? 'Increased' : 'Decreased') + ' by ' + changePct + '%';
        }

        $('#pct-result-value').text(typeof result === 'number' ? (mode === 'is' ? result.toFixed(2) + '%' : result.toFixed(2)) : result);
        $('#pct-result-label').text(label);
        $('#pct-result').removeClass('hidden');
    });

    $('#btn-pct-clear').on('click', function () {
        $('input[type="number"]').val('');
        hideError();
        $('#pct-result').addClass('hidden');
    });
});
