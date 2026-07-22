(function () {
    'use strict';

    var $ = window.jQuery;
    var THUMB_COUNT = 18;
    var SCRUB_STEPS = 10000;
    var FRAME_STEP = 1 / 30;

    var $drop = $('#vfe-drop');
    var $file = $('#vfe-file');
    var $workspace = $('#vfe-workspace');
    var $error = $('#vfe-error');
    var $video = $('#vfe-video');
    var video = $video[0];
    var $canvas = $('#vfe-canvas');
    var canvas = $canvas[0];
    var ctx = canvas.getContext('2d');
    var $scrubber = $('#vfe-scrubber');
    var $filmstrip = $('#vfe-filmstrip');
    var $filmstripStatus = $('#vfe-filmstrip-status');

    var objectUrl = null;
    var duration = 0;
    var seeking = false;
    var buildingStrip = false;

    function formatTime(seconds) {
        if (!isFinite(seconds) || seconds < 0) return '0:00.00';
        var mins = Math.floor(seconds / 60);
        var secs = seconds % 60;
        return mins + ':' + (secs < 10 ? '0' : '') + secs.toFixed(2);
    }

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
    }

    function hideError() {
        $error.addClass('hidden');
    }

    function stepToTime(step) {
        if (!duration) return 0;
        return (step / SCRUB_STEPS) * duration;
    }

    function timeToStep(time) {
        if (!duration) return 0;
        return Math.round((time / duration) * SCRUB_STEPS);
    }

    function seekVideo(time) {
        return new Promise(function (resolve) {
            if (Math.abs(video.currentTime - time) < 0.001) {
                resolve();
                return;
            }
            var done = function () {
                video.removeEventListener('seeked', done);
                resolve();
            };
            video.addEventListener('seeked', done);
            video.currentTime = Math.max(0, Math.min(duration, time));
        });
    }

    function drawFrame() {
        if (!video.videoWidth) return;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0);
        $('#vfe-frame-meta').text(
            video.videoWidth + ' × ' + video.videoHeight + ' px · at ' + formatTime(video.currentTime)
        );
        updateFilmstripActive();
    }

    function updateScrubberUi() {
        var step = timeToStep(video.currentTime);
        $scrubber.val(step);
        $('#vfe-time-current').text(formatTime(video.currentTime));
    }

    function updateFilmstripActive() {
        if (!duration) return;
        var t = video.currentTime;
        $filmstrip.find('.vfe-thumb').each(function () {
            var thumbTime = parseFloat($(this).attr('data-time'));
            $(this).toggleClass('active', Math.abs(thumbTime - t) < duration / THUMB_COUNT / 2);
        });
    }

    function makeThumbCanvas(time) {
        var w = 120;
        var h = Math.round(w * (video.videoHeight / video.videoWidth)) || 68;
        var c = document.createElement('canvas');
        c.width = w;
        c.height = h;
        c.getContext('2d').drawImage(video, 0, 0, w, h);
        return c.toDataURL('image/jpeg', 0.75);
    }

    async function buildFilmstrip() {
        buildingStrip = true;
        $filmstrip.empty();
        $filmstripStatus.removeClass('hidden').text('Generating frame thumbnails…');

        for (var i = 0; i < THUMB_COUNT; i++) {
            var time = THUMB_COUNT === 1 ? 0 : (i / (THUMB_COUNT - 1)) * Math.max(0, duration - 0.05);
            await seekVideo(time);
            var dataUrl = makeThumbCanvas(time);
            var $btn = $('<button type="button" class="vfe-thumb" role="option"></button>');
            $btn.attr('data-time', time);
            $btn.attr('aria-label', 'Frame at ' + formatTime(time));
            $btn.append($('<img>').attr('src', dataUrl).attr('alt', ''));
            $btn.append($('<span class="vfe-thumb-time">').text(formatTime(time)));
            $filmstrip.append($btn);
            $filmstripStatus.text('Generating thumbnails… ' + (i + 1) + '/' + THUMB_COUNT);
        }

        $filmstripStatus.addClass('hidden');
        buildingStrip = false;
        updateFilmstripActive();
    }

    async function onScrubberInput() {
        if (seeking || buildingStrip) return;
        seeking = true;
        var time = stepToTime(parseInt($scrubber.val(), 10));
        await seekVideo(time);
        drawFrame();
        updateScrubberUi();
        seeking = false;
    }

    function loadVideo(file) {
        hideError();
        if (!file || !file.type.startsWith('video/')) {
            showError('Please choose a video file (MP4, WebM, MOV, etc.).');
            return;
        }

        cleanup();
        objectUrl = URL.createObjectURL(file);
        video.src = objectUrl;
        video.load();

        $workspace.removeClass('hidden');
        $drop.addClass('hidden');
    }

    function cleanup() {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
        video.pause();
        video.removeAttribute('src');
        video.load();
        duration = 0;
        $filmstrip.empty();
    }

    function resetAll() {
        cleanup();
        $workspace.addClass('hidden');
        $drop.removeClass('hidden');
        $file.val('');
        hideError();
    }

    function downloadFrame() {
        if (!canvas.width) {
            showError('No frame selected.');
            return;
        }
        hideError();
        var mime = $('#vfe-format').val() || 'image/png';
        var ext = mime === 'image/jpeg' ? 'jpg' : mime === 'image/webp' ? 'webp' : 'png';
        var name = 'frame-' + formatTime(video.currentTime).replace(/:/g, '-') + '.' + ext;

        canvas.toBlob(function (blob) {
            if (!blob) {
                showError('Could not export frame.');
                return;
            }
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = name;
            a.click();
            URL.revokeObjectURL(a.href);
        }, mime, mime === 'image/jpeg' ? 0.92 : undefined);
    }

    $video.on('loadedmetadata', async function () {
        duration = video.duration || 0;
        $('#vfe-time-duration').text(formatTime(duration));
        $scrubber.attr('max', SCRUB_STEPS);
        video.pause();
        await seekVideo(0);
        drawFrame();
        updateScrubberUi();
        buildFilmstrip();
    });

    $video.on('timeupdate', function () {
        if (!seeking && !buildingStrip && !video.paused) {
            updateScrubberUi();
            drawFrame();
        }
    });

    $video.on('error', function () {
        showError('Could not load this video. Try another format or file.');
    });

    $drop.on('click', function () { $file.trigger('click'); });
    $drop.on('dragover', function (e) { e.preventDefault(); $(this).addClass('dragover'); });
    $drop.on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        if (e.type === 'drop' && e.originalEvent.dataTransfer.files.length) {
            loadVideo(e.originalEvent.dataTransfer.files[0]);
        }
    });

    $file.on('change', function () {
        if (this.files && this.files[0]) loadVideo(this.files[0]);
    });

    $scrubber.on('input', onScrubberInput);

    $('#vfe-play-toggle').on('click', function () {
        if (video.paused) {
            video.play();
            $(this).text('Pause');
        } else {
            video.pause();
            $(this).text('Play');
            drawFrame();
        }
    });

    $('#vfe-prev-frame').on('click', async function () {
        seeking = true;
        video.pause();
        $('#vfe-play-toggle').text('Play');
        await seekVideo(Math.max(0, video.currentTime - FRAME_STEP));
        drawFrame();
        updateScrubberUi();
        seeking = false;
    });

    $('#vfe-next-frame').on('click', async function () {
        seeking = true;
        video.pause();
        $('#vfe-play-toggle').text('Play');
        await seekVideo(Math.min(duration, video.currentTime + FRAME_STEP));
        drawFrame();
        updateScrubberUi();
        seeking = false;
    });

    $filmstrip.on('click', '.vfe-thumb', async function () {
        var time = parseFloat($(this).attr('data-time'));
        if (!isFinite(time)) return;
        seeking = true;
        video.pause();
        $('#vfe-play-toggle').text('Play');
        await seekVideo(time);
        drawFrame();
        updateScrubberUi();
        seeking = false;
    });

    $('#vfe-download').on('click', downloadFrame);
    $('#vfe-reset').on('click', resetAll);
})();
