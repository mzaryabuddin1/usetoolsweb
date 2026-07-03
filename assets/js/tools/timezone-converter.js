$(function () {
    'use strict';

    var zones = [
        'UTC',
        'America/New_York',
        'America/Chicago',
        'America/Denver',
        'America/Los_Angeles',
        'America/Sao_Paulo',
        'Europe/London',
        'Europe/Paris',
        'Europe/Berlin',
        'Europe/Moscow',
        'Asia/Dubai',
        'Asia/Karachi',
        'Asia/Kolkata',
        'Asia/Bangkok',
        'Asia/Singapore',
        'Asia/Tokyo',
        'Asia/Shanghai',
        'Australia/Sydney',
        'Pacific/Auckland'
    ];

    function populateSelect($el, selected) {
        zones.forEach(function (z) {
            $el.append($('<option>').val(z).text(z));
        });
        if (selected) $el.val(selected);
    }

    populateSelect($('#tz-from'), 'UTC');
    populateSelect($('#tz-to'), 'America/New_York');

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function toLocalInputValue(date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) +
            'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
    }

    function parsePartsInZone(dateStr, zone) {
        var parts = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})/);
        if (!parts) return null;

        var y = +parts[1], mo = +parts[2] - 1, d = +parts[3], h = +parts[4], mi = +parts[5];
        var guess = new Date(Date.UTC(y, mo, d, h, mi));
        var fmt = new Intl.DateTimeFormat('en-US', {
            timeZone: zone,
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', hour12: false
        });

        for (var i = 0; i < 3; i++) {
            var p = fmt.formatToParts(guess);
            var got = {};
            p.forEach(function (part) { if (part.type !== 'literal') got[part.type] = part.value; });
            var diff = Date.UTC(+got.year, +got.month - 1, +got.day, +got.hour, +got.minute) -
                Date.UTC(y, mo, d, h, mi);
            guess = new Date(guess.getTime() - diff);
        }
        return guess;
    }

    function formatInZone(date, zone) {
        return new Intl.DateTimeFormat('en-US', {
            timeZone: zone,
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZoneName: 'short'
        }).format(date);
    }

    $('#btn-tz-convert').on('click', function () {
        var dt = $('#tz-datetime').val();
        var from = $('#tz-from').val();
        var to = $('#tz-to').val();

        if (!dt) {
            $('#tz-error').text('Select a date and time.').removeClass('hidden');
            $('#tz-result').addClass('hidden');
            return;
        }

        try {
            var utcDate = parsePartsInZone(dt, from);
            $('#tz-output').text(formatInZone(utcDate, to));
            $('#tz-error').addClass('hidden');
            $('#tz-result').removeClass('hidden');
        } catch (e) {
            $('#tz-error').text('Conversion failed: ' + e.message).removeClass('hidden');
            $('#tz-result').addClass('hidden');
        }
    });

    $('#btn-tz-now').on('click', function () {
        $('#tz-datetime').val(toLocalInputValue(new Date()));
    });

    $('#btn-tz-clear').on('click', function () {
        $('#tz-datetime').val('');
        $('#tz-from').val('UTC');
        $('#tz-to').val('America/New_York');
        $('#tz-error').addClass('hidden');
        $('#tz-result').addClass('hidden');
    });
});
