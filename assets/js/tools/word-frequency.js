$(function () {
    'use strict';

    $('#btn-freq-analyze').on('click', function () {
        var text = $.trim($('#freq-input').val()).toLowerCase();
        if (!text) {
            $('#freq-table-wrap').addClass('hidden');
            return;
        }

        var words = text.match(/[a-z0-9']+/g) || [];
        var freq = {};

        words.forEach(function (w) {
            freq[w] = (freq[w] || 0) + 1;
        });

        var sorted = Object.keys(freq).sort(function (a, b) {
            return freq[b] - freq[a] || a.localeCompare(b);
        });

        var $body = $('#freq-table-body');
        $body.empty();
        sorted.forEach(function (word) {
            $body.append('<tr><td>' + $('<span>').text(word).html() + '</td><td>' + freq[word] + '</td></tr>');
        });

        $('#freq-table-wrap').removeClass('hidden');
    });

    $('#btn-freq-clear').on('click', function () {
        $('#freq-input').val('');
        $('#freq-table-body').empty();
        $('#freq-table-wrap').addClass('hidden');
    });
});
