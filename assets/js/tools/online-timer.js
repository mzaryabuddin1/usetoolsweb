$(function () {
    'use strict';

    var countdownInterval = null;
    var countdownRemaining = 0;
    var stopwatchInterval = null;
    var stopwatchElapsed = 0;
    var stopwatchRunning = false;
    var stopwatchStartTime = 0;

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function formatCountdown(secs) {
        var m = Math.floor(secs / 60);
        var s = secs % 60;
        return pad(m) + ':' + pad(s);
    }

    function formatStopwatch(ms) {
        var totalSec = Math.floor(ms / 1000);
        var h = Math.floor(totalSec / 3600);
        var m = Math.floor((totalSec % 3600) / 60);
        var s = totalSec % 60;
        return pad(h) + ':' + pad(m) + ':' + pad(s);
    }

    $('.timer-tab').on('click', function () {
        var tab = $(this).data('tab');
        $('.timer-tab').removeClass('active');
        $(this).addClass('active');
        if (tab === 'countdown') {
            $('#timer-countdown-panel').removeClass('hidden');
            $('#timer-stopwatch-panel').addClass('hidden');
        } else {
            $('#timer-countdown-panel').addClass('hidden');
            $('#timer-stopwatch-panel').removeClass('hidden');
        }
    });

    function updateCountdownDisplay() {
        $('#countdown-display').text(formatCountdown(countdownRemaining));
    }

    $('#btn-countdown-start').on('click', function () {
        if (countdownInterval) return;
        if (countdownRemaining <= 0) {
            var min = parseInt($('#timer-minutes').val(), 10) || 0;
            var sec = parseInt($('#timer-seconds').val(), 10) || 0;
            countdownRemaining = min * 60 + sec;
            if (countdownRemaining <= 0) return;
        }
        updateCountdownDisplay();
        $('#btn-countdown-start').prop('disabled', true);
        $('#btn-countdown-pause').prop('disabled', false);
        countdownInterval = setInterval(function () {
            countdownRemaining--;
            updateCountdownDisplay();
            if (countdownRemaining <= 0) {
                clearInterval(countdownInterval);
                countdownInterval = null;
                $('#countdown-display').addClass('timer-done');
                $('#btn-countdown-start').prop('disabled', false);
                $('#btn-countdown-pause').prop('disabled', true);
            }
        }, 1000);
    });

    $('#btn-countdown-pause').on('click', function () {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
            $('#btn-countdown-start').prop('disabled', false);
            $('#btn-countdown-pause').prop('disabled', true);
        }
    });

    $('#btn-countdown-reset').on('click', function () {
        if (countdownInterval) clearInterval(countdownInterval);
        countdownInterval = null;
        countdownRemaining = 0;
        var min = parseInt($('#timer-minutes').val(), 10) || 0;
        var sec = parseInt($('#timer-seconds').val(), 10) || 0;
        $('#countdown-display').text(formatCountdown(min * 60 + sec)).removeClass('timer-done');
        $('#btn-countdown-start').prop('disabled', false);
        $('#btn-countdown-pause').prop('disabled', true);
    });

    function updateStopwatch() {
        var elapsed = stopwatchElapsed + (stopwatchRunning ? Date.now() - stopwatchStartTime : 0);
        $('#stopwatch-display').text(formatStopwatch(elapsed));
    }

    $('#btn-stopwatch-start').on('click', function () {
        if (stopwatchRunning) return;
        stopwatchRunning = true;
        stopwatchStartTime = Date.now();
        $('#btn-stopwatch-start').prop('disabled', true);
        $('#btn-stopwatch-pause').prop('disabled', false);
        stopwatchInterval = setInterval(updateStopwatch, 100);
    });

    $('#btn-stopwatch-pause').on('click', function () {
        if (!stopwatchRunning) return;
        stopwatchElapsed += Date.now() - stopwatchStartTime;
        stopwatchRunning = false;
        clearInterval(stopwatchInterval);
        stopwatchInterval = null;
        updateStopwatch();
        $('#btn-stopwatch-start').prop('disabled', false);
        $('#btn-stopwatch-pause').prop('disabled', true);
    });

    $('#btn-stopwatch-reset').on('click', function () {
        if (stopwatchInterval) clearInterval(stopwatchInterval);
        stopwatchInterval = null;
        stopwatchRunning = false;
        stopwatchElapsed = 0;
        $('#stopwatch-display').text('00:00:00');
        $('#btn-stopwatch-start').prop('disabled', false);
        $('#btn-stopwatch-pause').prop('disabled', true);
    });

    $('#btn-countdown-reset').trigger('click');
});
