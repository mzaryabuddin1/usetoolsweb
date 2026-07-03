$(function () {
    'use strict';

    function parseCSV(text) {
        var rows = [];
        var row = [];
        var cell = '';
        var inQuotes = false;

        for (var i = 0; i < text.length; i++) {
            var c = text[i];
            var next = text[i + 1];

            if (inQuotes) {
                if (c === '"' && next === '"') {
                    cell += '"';
                    i++;
                } else if (c === '"') {
                    inQuotes = false;
                } else {
                    cell += c;
                }
            } else {
                if (c === '"') {
                    inQuotes = true;
                } else if (c === ',') {
                    row.push(cell);
                    cell = '';
                } else if (c === '\n' || (c === '\r' && next === '\n')) {
                    row.push(cell);
                    rows.push(row);
                    row = [];
                    cell = '';
                    if (c === '\r') i++;
                } else if (c !== '\r') {
                    cell += c;
                }
            }
        }

        if (cell !== '' || row.length > 0) {
            row.push(cell);
            rows.push(row);
        }

        return rows.filter(function (r) {
            return r.some(function (c) { return $.trim(c) !== ''; });
        });
    }

    $('#btn-csv-convert').on('click', function () {
        var text = $.trim($('#csv-input').val());
        if (!text) {
            $('#csv-error').text('Paste CSV data first.').removeClass('hidden');
            return;
        }

        try {
            var rows = parseCSV(text);
            if (rows.length < 2) {
                throw new Error('CSV must have a header row and at least one data row.');
            }

            var headers = rows[0].map(function (h) { return $.trim(h); });
            var data = [];

            for (var i = 1; i < rows.length; i++) {
                var obj = {};
                for (var j = 0; j < headers.length; j++) {
                    obj[headers[j] || 'column_' + (j + 1)] = rows[i][j] !== undefined ? rows[i][j] : '';
                }
                data.push(obj);
            }

            $('#csv-output').val(JSON.stringify(data, null, 2));
            $('#csv-error').addClass('hidden');
        } catch (e) {
            $('#csv-error').text(e.message || 'Failed to parse CSV.').removeClass('hidden');
        }
    });

    $('#btn-csv-copy').on('click', function () {
        var text = $('#csv-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-csv-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-csv-clear').on('click', function () {
        $('#csv-input, #csv-output').val('');
        $('#csv-error').addClass('hidden');
    });
});
