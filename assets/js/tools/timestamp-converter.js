$(function () {
    'use strict';

    var $unix = $('#ts-unix');
    var $date = $('#ts-date');
    var $utc = $('#ts-utc');
    var $error = $('#ts-error');

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function toDatetimeLocal(date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) +
            'T' + pad(date.getHours()) + ':' + pad(date.getMinutes()) + ':' + pad(date.getSeconds());
    }

    function updateCurrent() {
        var now = Math.floor(Date.now() / 1000);
        $('#current-timestamp').text(now);
    }

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
    }

    function hideError() {
        $error.addClass('hidden');
    }

    function fromUnix(ts) {
        var sec = parseInt(ts, 10);
        if (isNaN(sec)) throw new Error('Invalid Unix timestamp.');
        var date = new Date(sec * 1000);
        if (isNaN(date.getTime())) throw new Error('Invalid Unix timestamp.');
        $date.val(toDatetimeLocal(date));
        $utc.val(date.toUTCString());
    }

    function fromDate(val) {
        if (!val) throw new Error('Select a date and time.');
        var date = new Date(val);
        if (isNaN(date.getTime())) throw new Error('Invalid date.');
        var ts = Math.floor(date.getTime() / 1000);
        $unix.val(ts);
        $utc.val(date.toUTCString());
    }

    function convert() {
        hideError();
        try {
            var unixVal = $.trim($unix.val());
            var dateVal = $date.val();

            if (unixVal && !dateVal) {
                fromUnix(unixVal);
            } else if (dateVal) {
                fromDate(dateVal);
            } else if (unixVal) {
                fromUnix(unixVal);
            } else {
                throw new Error('Enter a Unix timestamp or select a date.');
            }
        } catch (e) {
            showError(e.message);
        }
    }

    updateCurrent();
    setInterval(updateCurrent, 1000);

    $unix.on('input', function () {
        if ($.trim($(this).val())) {
            try { fromUnix($(this).val()); hideError(); } catch (e) { /* wait for convert */ }
        }
    });

    $date.on('change', function () {
        if ($(this).val()) {
            try { fromDate($(this).val()); hideError(); } catch (e) { /* wait */ }
        }
    });

    $('#btn-ts-convert').on('click', convert);

    $('#btn-use-now').on('click', function () {
        var now = Math.floor(Date.now() / 1000);
        $unix.val(now);
        fromUnix(now);
        hideError();
    });

    $('#btn-ts-clear').on('click', function () {
        $unix.val('');
        $date.val('');
        $utc.val('');
        hideError();
    });
});
