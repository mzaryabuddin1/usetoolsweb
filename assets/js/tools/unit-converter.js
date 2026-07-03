$(function () {
    'use strict';

    var lenToM = { m: 1, km: 1000, ft: 0.3048, mi: 1609.344, in: 0.0254 };
    var wtToKg = { kg: 1, lb: 0.453592, oz: 0.0283495 };

    function convertLength() {
        var val = parseFloat($('#len-value').val());
        if (isNaN(val)) { $('#len-result').text('—'); return; }
        var from = $('#len-from').val();
        var to = $('#len-to').val();
        var meters = val * lenToM[from];
        var result = meters / lenToM[to];
        $('#len-result').text(result.toFixed(6).replace(/\.?0+$/, ''));
    }

    function convertWeight() {
        var val = parseFloat($('#wt-value').val());
        if (isNaN(val)) { $('#wt-result').text('—'); return; }
        var from = $('#wt-from').val();
        var to = $('#wt-to').val();
        var kg = val * wtToKg[from];
        var result = kg / wtToKg[to];
        $('#wt-result').text(result.toFixed(6).replace(/\.?0+$/, ''));
    }

    function toCelsius(val, from) {
        if (from === 'C') return val;
        if (from === 'F') return (val - 32) * 5 / 9;
        return val - 273.15;
    }

    function fromCelsius(c, to) {
        if (to === 'C') return c;
        if (to === 'F') return c * 9 / 5 + 32;
        return c + 273.15;
    }

    function convertTemp() {
        var val = parseFloat($('#temp-value').val());
        if (isNaN(val)) { $('#temp-result').text('—'); return; }
        var from = $('#temp-from').val();
        var to = $('#temp-to').val();
        var c = toCelsius(val, from);
        var result = fromCelsius(c, to);
        $('#temp-result').text(result.toFixed(4).replace(/\.?0+$/, ''));
    }

    $('.unit-tab').on('click', function () {
        var tab = $(this).data('tab');
        $('.unit-tab').removeClass('active');
        $(this).addClass('active');
        $('.unit-panel').addClass('hidden');
        $('#unit-' + tab).removeClass('hidden');
    });

    $('#len-value, #len-from, #len-to').on('input change', convertLength);
    $('#wt-value, #wt-from, #wt-to').on('input change', convertWeight);
    $('#temp-value, #temp-from, #temp-to').on('input change', convertTemp);
});
