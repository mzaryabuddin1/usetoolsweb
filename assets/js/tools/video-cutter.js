$(function () {
    'use strict';

    var API_URL = '/api/video-cut.php';

    var $dropZone = $('#drop-zone');
    var $fileInput = $('#file-input');
    var $controls = $('#video-controls');
    var $error = $('#video-error');
    var $info = $('#video-info');
    var $video = $('#video-preview');
    var video = $video[0];
    var $rangeStart = $('#range-start');
    var $rangeEnd = $('#range-end');
    var $timelineFill = $('#timeline-fill');
    var $playhead = $('#timeline-playhead');
    var $exportProgress = $('#export-progress');
    var $exportProgressFill = $('#export-progress-fill');
    var $exportProgressText = $('#export-progress-text');

    var originalFile = null;
    var videoDuration = 0;
    var loopHandler = null;
    var syncingRanges = false;
    var serverReady = null;

    var RANGE_STEPS = 10000;

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function formatTime(seconds) {
        if (!isFinite(seconds) || seconds < 0) return '0:00.0';
        var mins = Math.floor(seconds / 60);
        var secs = seconds % 60;
        return mins + ':' + (secs < 10 ? '0' : '') + secs.toFixed(1);
    }

    function parseTime(str) {
        if (!str || !str.trim()) return NaN;
        str = str.trim();
        if (str.indexOf(':') >= 0) {
            var parts = str.split(':');
            var secs = parseFloat(parts.pop());
            var mins = parseFloat(parts.pop() || 0);
            var hrs = parts.length ? parseFloat(parts.pop() || 0) : 0;
            return hrs * 3600 + mins * 60 + secs;
        }
        return parseFloat(str);
    }

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
        $info.addClass('hidden');
    }

    function showInfo(msg) {
        $info.text(msg).removeClass('hidden');
        $error.addClass('hidden');
    }

    function hideMessages() {
        $error.addClass('hidden');
        $info.addClass('hidden');
    }

    function setStep(step) {
        $('.tool-step').removeClass('active');
        $('.tool-step[data-step="' + step + '"]').addClass('active');
    }

    function stepToTime(step) {
        if (!videoDuration) return 0;
        return (step / RANGE_STEPS) * videoDuration;
    }

    function timeToStep(time) {
        if (!videoDuration) return 0;
        return Math.round((time / videoDuration) * RANGE_STEPS);
    }

    function getSelection() {
        var start = stepToTime(parseInt($rangeStart.val(), 10));
        var end = stepToTime(parseInt($rangeEnd.val(), 10));
        if (end <= start) end = Math.min(videoDuration, start + 0.1);
        return { start: start, end: end };
    }

    function updateTimelineUI() {
        var startStep = parseInt($rangeStart.val(), 10);
        var endStep = parseInt($rangeEnd.val(), 10);
        var startPct = (startStep / RANGE_STEPS) * 100;
        var endPct = (endStep / RANGE_STEPS) * 100;

        $timelineFill.css({ left: startPct + '%', width: (endPct - startPct) + '%' });
        $('#timeline-start-label').text(formatTime(stepToTime(startStep)));
        $('#timeline-end-label').text(formatTime(stepToTime(endStep)));

        var sel = getSelection();
        $('#start-time').val(formatTime(sel.start));
        $('#end-time').val(formatTime(sel.end));
        var len = Math.max(0, sel.end - sel.start);
        $('#duration-display').val(formatTime(len));
        $('#stat-selection').text(formatTime(len));
    }

    function syncRangesFromInputs() {
        if (!videoDuration) return;
        var start = parseTime($('#start-time').val());
        var end = parseTime($('#end-time').val());
        if (isNaN(start)) start = 0;
        if (isNaN(end)) end = videoDuration;
        start = Math.max(0, Math.min(start, videoDuration));
        end = Math.max(start + 0.1, Math.min(end, videoDuration));

        syncingRanges = true;
        $rangeStart.val(timeToStep(start));
        $rangeEnd.val(timeToStep(end));
        syncingRanges = false;
        updateTimelineUI();
    }

    function updatePlayhead() {
        if (!videoDuration) return;
        var pct = (video.currentTime / videoDuration) * 100;
        $playhead.css('left', pct + '%');
    }

    function stopLoop() {
        if (loopHandler) {
            video.removeEventListener('timeupdate', loopHandler);
            loopHandler = null;
        }
    }

    function setupLoop() {
        stopLoop();
        if (!$('#loop-selection').is(':checked')) return;

        loopHandler = function () {
            var sel = getSelection();
            if (video.currentTime >= sel.end - 0.05) {
                video.currentTime = sel.start;
            }
        };
        video.addEventListener('timeupdate', loopHandler);
    }

    function checkServer() {
        if (serverReady !== null) {
            return $.Deferred().resolve(serverReady).promise();
        }

        return $.getJSON(API_URL).then(function (data) {
            serverReady = !!data.available;
            if (!serverReady) {
                showError('FFmpeg is not available on this server. Install FFmpeg or set FFMPEG_BINARY in config.php.');
            }
            return serverReady;
        }).fail(function () {
            serverReady = false;
            showError('Could not reach the video processing API.');
            return false;
        });
    }

    function loadVideoFile(file) {
        if (!file || (!file.type.match(/^video\//) && !file.name.match(/\.(mp4|webm|mov|mkv|avi|m4v)$/i))) {
            showError('Please select a video file (MP4, WebM, MOV, MKV).');
            return;
        }

        hideMessages();
        originalFile = file;
        $('#stat-original').text(formatBytes(file.size));
        checkServer();

        var url = URL.createObjectURL(file);
        video.src = url;
        $controls.removeClass('hidden');
        $dropZone.addClass('hidden');
        setStep(1);

        video.onloadedmetadata = function () {
            videoDuration = video.duration;
            if (!isFinite(videoDuration) || videoDuration <= 0) {
                showError('Could not read video duration.');
                return;
            }

            $rangeStart.attr({ min: 0, max: RANGE_STEPS }).val(0);
            $rangeEnd.attr({ min: 0, max: RANGE_STEPS }).val(RANGE_STEPS);
            $('#stat-duration').text(formatTime(videoDuration));
            updateTimelineUI();
            setStep(2);
            hideMessages();
        };

        video.onerror = function () {
            showError('Could not load this video. Try MP4 or WebM.');
        };
    }

    function setExportProgress(pct, text) {
        $exportProgress.removeClass('hidden');
        $exportProgressFill.css('width', Math.round(pct * 100) + '%');
        $exportProgressText.text(text || 'Processing…');
    }

    function hideExportProgress() {
        $exportProgress.addClass('hidden');
        $exportProgressFill.css('width', '0%');
    }

    function parseFilename(disposition) {
        if (!disposition) return 'trimmed-video.mp4';
        var match = /filename="([^"]+)"/i.exec(disposition);
        return match ? match[1] : 'trimmed-video.mp4';
    }

    function readJsonFromBlob(blob) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () {
                try {
                    resolve(JSON.parse(reader.result));
                } catch (e) {
                    reject(e);
                }
            };
            reader.onerror = reject;
            reader.readAsText(blob);
        });
    }

    function exportVideo() {
        if (!originalFile || !videoDuration) return;

        var sel = getSelection();
        if (sel.end - sel.start < 0.1) {
            showError('Selection must be at least 0.1 seconds.');
            return;
        }

        hideMessages();
        $('#btn-export').prop('disabled', true);

        checkServer().then(function (ready) {
            if (!ready) {
                $('#btn-export').prop('disabled', false);
                return;
            }

            var formData = new FormData();
            formData.append('video', originalFile);
            formData.append('start', sel.start.toFixed(3));
            formData.append('end', sel.end.toFixed(3));
            formData.append('mode', $('#cut-mode').val());
            formData.append('format', $('#output-format').val());
            formData.append('quality', $('#video-quality').val());
            formData.append('mute', $('#mute-audio').is(':checked') ? '1' : '0');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', API_URL);
            xhr.responseType = 'blob';

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    var pct = (e.loaded / e.total) * 0.45;
                    setExportProgress(pct, 'Uploading… ' + Math.round(pct / 0.45 * 100) + '%');
                }
            };

            xhr.onload = function () {
                if (xhr.status === 200) {
                    var blob = xhr.response;
                    var filename = parseFilename(xhr.getResponseHeader('Content-Disposition'));
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);

                    setExportProgress(1, 'Done!');
                    setStep(3);
                    showInfo('Download started — ' + formatBytes(blob.size) + ' trimmed video.');
                    setTimeout(hideExportProgress, 1500);
                    $('#btn-export').prop('disabled', false);
                    return;
                }

                readJsonFromBlob(xhr.response).then(function (data) {
                    showError(data.error || 'Export failed. Try precise cut mode or a different format.');
                }).catch(function () {
                    showError('Export failed (HTTP ' + xhr.status + ').');
                }).finally(function () {
                    hideExportProgress();
                    $('#btn-export').prop('disabled', false);
                });
            };

            xhr.onerror = function () {
                showError('Network error while exporting. Check your connection and try again.');
                hideExportProgress();
                $('#btn-export').prop('disabled', false);
            };

            setExportProgress(0.05, 'Uploading video…');
            xhr.send(formData);
            setExportProgress(0.5, 'Processing on server with FFmpeg…');
        });
    }

    function resetTool() {
        stopLoop();
        if (video.src) {
            URL.revokeObjectURL(video.src);
            video.removeAttribute('src');
            video.load();
        }
        originalFile = null;
        videoDuration = 0;
        $controls.addClass('hidden');
        $dropZone.removeClass('hidden');
        $fileInput.val('');
        hideMessages();
        hideExportProgress();
        $('#stat-original, #stat-duration, #stat-selection').text('—');
        setStep(1);
    }

    // Events
    $dropZone.on('click', function () { $fileInput.trigger('click'); });
    $dropZone.on('dragover', function (e) {
        e.preventDefault();
        $dropZone.addClass('dragover');
    });
    $dropZone.on('dragleave drop', function () { $dropZone.removeClass('dragover'); });
    $dropZone.on('drop', function (e) {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) loadVideoFile(files[0]);
    });
    $fileInput.on('change', function () {
        if (this.files.length) loadVideoFile(this.files[0]);
    });

    $rangeStart.on('input', function () {
        if (syncingRanges) return;
        var start = parseInt($rangeStart.val(), 10);
        var end = parseInt($rangeEnd.val(), 10);
        if (start >= end - 10) {
            $rangeStart.val(Math.max(0, end - 10));
        }
        updateTimelineUI();
        video.currentTime = stepToTime(parseInt($rangeStart.val(), 10));
    });

    $rangeEnd.on('input', function () {
        if (syncingRanges) return;
        var start = parseInt($rangeStart.val(), 10);
        var end = parseInt($rangeEnd.val(), 10);
        if (end <= start + 10) {
            $rangeEnd.val(Math.min(RANGE_STEPS, start + 10));
        }
        updateTimelineUI();
        video.currentTime = stepToTime(parseInt($rangeEnd.val(), 10));
    });

    $('#start-time, #end-time').on('change blur', syncRangesFromInputs);

    $('#btn-set-start').on('click', function () {
        syncingRanges = true;
        $rangeStart.val(timeToStep(video.currentTime));
        var end = parseInt($rangeEnd.val(), 10);
        if (parseInt($rangeStart.val(), 10) >= end - 10) {
            $rangeEnd.val(Math.min(RANGE_STEPS, parseInt($rangeStart.val(), 10) + 10));
        }
        syncingRanges = false;
        updateTimelineUI();
    });

    $('#btn-set-end').on('click', function () {
        syncingRanges = true;
        $rangeEnd.val(timeToStep(video.currentTime));
        var start = parseInt($rangeStart.val(), 10);
        if (parseInt($rangeEnd.val(), 10) <= start + 10) {
            $rangeStart.val(Math.max(0, parseInt($rangeEnd.val(), 10) - 10));
        }
        syncingRanges = false;
        updateTimelineUI();
    });

    $('#btn-play-selection').on('click', function () {
        var sel = getSelection();
        video.currentTime = sel.start;
        video.play();
        setupLoop();
    });

    $('#btn-pause').on('click', function () {
        video.pause();
        stopLoop();
    });

    $('#loop-selection').on('change', setupLoop);

    video.addEventListener('timeupdate', updatePlayhead);

    $('#btn-select-all').on('click', function () {
        syncingRanges = true;
        $rangeStart.val(0);
        $rangeEnd.val(RANGE_STEPS);
        syncingRanges = false;
        updateTimelineUI();
    });

    $('#cut-mode').on('change', function () {
        var fast = $(this).val() === 'fast';
        $('#output-format').prop('disabled', fast);
        $('#video-quality').prop('disabled', fast);
        if (fast) {
            showInfo('Fast cut keeps the original format and quality. Switch to precise cut to change format.');
        } else {
            hideMessages();
        }
    });

    $('#btn-export').on('click', exportVideo);
    $('#btn-reset').on('click', resetTool);

    $('#output-format, #video-quality').prop('disabled', true);
    checkServer();
});
