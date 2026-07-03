<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Online Timer',
    'Free online timer and stopwatch. Countdown timer and stopwatch in your browser.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Online Timer</h1>
        <p>Use the countdown timer or stopwatch tabs below.</p>
    </div>

    <div class="tool-panel">
        <div class="btn-row" style="margin-top:0;">
            <button type="button" class="btn btn-secondary timer-tab active" data-tab="countdown">Countdown</button>
            <button type="button" class="btn btn-secondary timer-tab" data-tab="stopwatch">Stopwatch</button>
        </div>

        <div id="timer-countdown-panel">
            <div class="form-row">
                <div>
                    <label for="timer-minutes">Minutes</label>
                    <input type="number" id="timer-minutes" value="5" min="0" max="999">
                </div>
                <div>
                    <label for="timer-seconds">Seconds</label>
                    <input type="number" id="timer-seconds" value="0" min="0" max="59">
                </div>
            </div>
            <div class="timer-display" id="countdown-display">05:00</div>
            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-countdown-start">Start</button>
                <button type="button" class="btn btn-secondary" id="btn-countdown-pause" disabled>Pause</button>
                <button type="button" class="btn btn-secondary" id="btn-countdown-reset">Reset</button>
            </div>
        </div>

        <div id="timer-stopwatch-panel" class="hidden">
            <div class="timer-display" id="stopwatch-display">00:00:00</div>
            <div class="btn-row">
                <button type="button" class="btn btn-primary" id="btn-stopwatch-start">Start</button>
                <button type="button" class="btn btn-secondary" id="btn-stopwatch-pause" disabled>Pause</button>
                <button type="button" class="btn btn-secondary" id="btn-stopwatch-reset">Reset</button>
            </div>
        </div>
    </div>

    <div class="ad-slot" aria-hidden="true">Ad space — add Google AdSense code here</div>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/online-timer.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
