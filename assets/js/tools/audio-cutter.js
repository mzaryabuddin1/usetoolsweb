$(function () {
    'use strict';

    var $dropZone = $('#drop-zone');
    var $fileInput = $('#file-input');
    var $controls = $('#audio-controls');
    var $error = $('#audio-error');
    var $info = $('#audio-info');
    var $exportProgress = $('#export-progress');
    var $exportProgressFill = $('#export-progress-fill');
    var $exportProgressText = $('#export-progress-text');
    var $timelineList = $('#timeline-list');

    var wavesurfer = null;
    var regionsPlugin = null;
    var activeRegion = null;
    var originalFile = null;
    var isPlayingSelection = false;
    var updatingFromRegion = false;
    var zoomLevel = 50;

    var audioContext = null;
    var decodedBuffer = null;
    var timelineClips = [];
    var clipIdCounter = 0;
    var previewSources = [];
    var dragClipId = null;

    var REGION_COLORS = [
        'rgba(37, 99, 235, 0.28)',
        'rgba(16, 185, 129, 0.28)',
        'rgba(245, 158, 11, 0.28)',
        'rgba(139, 92, 246, 0.28)'
    ];

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

    function nextClipId() {
        clipIdCounter += 1;
        return 'clip-' + clipIdCounter;
    }

    function getDuration() {
        return wavesurfer ? wavesurfer.getDuration() : 0;
    }

    function getSelection() {
        if (!activeRegion) {
            return { start: 0, end: getDuration() };
        }
        return {
            start: Math.max(0, activeRegion.start),
            end: Math.min(getDuration(), activeRegion.end)
        };
    }

    function calcTimelineDuration() {
        var total = 0;
        timelineClips.forEach(function (clip) {
            if (clip.type === 'silence') {
                total += clip.duration;
            } else {
                total += clip.srcEnd - clip.srcStart;
            }
        });
        return total;
    }

    function updateStats() {
        var sel = getSelection();
        var selLen = Math.max(0, sel.end - sel.start);
        $('#duration-display').val(formatTime(selLen));

        var outLen = timelineClips.length > 0 ? calcTimelineDuration() : selLen;
        $('#stat-output').text(formatTime(outLen));
    }

    function syncInputsFromRegion() {
        if (!activeRegion) return;
        updatingFromRegion = true;
        $('#start-time').val(formatTime(activeRegion.start));
        $('#end-time').val(formatTime(activeRegion.end));
        updatingFromRegion = false;
        updateStats();
    }

    function applyInputsToRegion() {
        if (updatingFromRegion || !wavesurfer || !activeRegion) return;
        var dur = getDuration();
        var start = parseTime($('#start-time').val());
        var end = parseTime($('#end-time').val());
        if (isNaN(start)) start = 0;
        if (isNaN(end)) end = dur;
        start = Math.max(0, Math.min(start, dur));
        end = Math.max(start + 0.1, Math.min(end, dur));
        activeRegion.update({ start: start, end: end });
        updateStats();
    }

    function highlightActiveRegion() {
        if (!wavesurfer) return;
        var regions = wavesurfer.regions ? wavesurfer.regions.list : {};
        Object.keys(regions).forEach(function (id) {
            var r = regions[id];
            var isActive = activeRegion && r === activeRegion;
            r.update({
                color: isActive ? 'rgba(37, 99, 235, 0.38)' : 'rgba(37, 99, 235, 0.18)'
            });
        });
    }

    function createRegion(start, end, activate) {
        if (!wavesurfer) return null;
        var color = REGION_COLORS[Object.keys(wavesurfer.regions.list || {}).length % REGION_COLORS.length];
        var region = wavesurfer.addRegion({
            start: start,
            end: end,
            color: color,
            drag: true,
            resize: true
        });
        if (activate !== false) {
            activeRegion = region;
            syncInputsFromRegion();
            highlightActiveRegion();
        }
        return region;
    }

    function stopPreviewSources() {
        previewSources.forEach(function (src) {
            try { src.stop(); } catch (e) { /* ignore */ }
        });
        previewSources = [];
    }

    function destroyWaveSurfer() {
        stopPreviewSources();
        isPlayingSelection = false;
        if (wavesurfer) {
            wavesurfer.destroy();
            wavesurfer = null;
        }
        regionsPlugin = null;
        activeRegion = null;
    }

    async function decodeAudioFile(file) {
        var arrayBuffer = await file.arrayBuffer();
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        decodedBuffer = await audioContext.decodeAudioData(arrayBuffer.slice(0));
    }

    function initWaveSurfer(file) {
        destroyWaveSurfer();
        $('#waveform').empty();
        timelineClips = [];
        renderTimelineUI();

        regionsPlugin = WaveSurfer.regions.create({
            dragSelection: true,
            regionsMinLength: 0.1
        });

        wavesurfer = WaveSurfer.create({
            container: '#waveform',
            waveColor: '#94a3b8',
            progressColor: '#2563eb',
            cursorColor: '#2563eb',
            height: 120,
            normalize: true,
            responsive: true,
            barWidth: 2,
            barGap: 1,
            backend: 'WebAudio',
            plugins: [regionsPlugin]
        });

        wavesurfer.on('ready', function () {
            var dur = wavesurfer.getDuration();
            $('#stat-duration').text(formatTime(dur));
            createRegion(0, dur);
            setStep(2);
            hideMessages();
            updateStats();
        });

        wavesurfer.on('error', function (err) {
            showError('Could not load this audio file. Try MP3 or WAV.');
            console.error('WaveSurfer error:', err);
        });

        wavesurfer.on('play', function () {
            $('#btn-play').text('⏸ Pause');
        });

        wavesurfer.on('pause', function () {
            $('#btn-play').text('▶ Play');
            isPlayingSelection = false;
        });

        wavesurfer.on('finish', function () {
            $('#btn-play').text('▶ Play');
            isPlayingSelection = false;
        });

        wavesurfer.on('audioprocess', function () {
            if (!isPlayingSelection || !activeRegion) return;
            if (wavesurfer.getCurrentTime() >= activeRegion.end - 0.05) {
                if ($('#loop-selection').is(':checked')) {
                    wavesurfer.play(activeRegion.start, activeRegion.end);
                } else {
                    wavesurfer.pause();
                    isPlayingSelection = false;
                }
            }
        });

        wavesurfer.on('region-created', function (region) {
            activeRegion = region;
            syncInputsFromRegion();
            highlightActiveRegion();
        });

        wavesurfer.on('region-click', function (region) {
            activeRegion = region;
            syncInputsFromRegion();
            highlightActiveRegion();
        });

        wavesurfer.on('region-updated', function (region) {
            if (activeRegion === region) syncInputsFromRegion();
        });

        wavesurfer.on('region-update-end', function (region) {
            if (activeRegion === region) syncInputsFromRegion();
        });

        wavesurfer.loadBlob(file);
    }

    function renderTimelineUI() {
        $timelineList.empty();
        timelineClips.forEach(function (clip, index) {
            var $li = $('<li class="timeline-item"></li>');
            $li.attr('draggable', 'true');
            $li.attr('data-id', clip.id);

            if (clip.type === 'silence') {
                $li.addClass('is-silence');
                $li.append(
                    '<span class="timeline-item-handle" aria-hidden="true">⋮⋮</span>' +
                    '<div class="timeline-item-body">' +
                    '<strong>Silence — ' + clip.duration.toFixed(1) + 's</strong>' +
                    '<span>Blank gap in output</span></div>'
                );
            } else {
                var len = clip.srcEnd - clip.srcStart;
                $li.append(
                    '<span class="timeline-item-handle" aria-hidden="true">⋮⋮</span>' +
                    '<div class="timeline-item-body">' +
                    '<strong>Audio clip ' + (index + 1) + '</strong>' +
                    '<span>From source ' + formatTime(clip.srcStart) + ' → ' + formatTime(clip.srcEnd) +
                    ' (' + formatTime(len) + ')</span></div>'
                );
            }

            var $actions = $('<div class="timeline-item-actions"></div>');
            if (index > 0) {
                $actions.append('<button type="button" class="btn btn-secondary btn-sm btn-icon btn-move-up" title="Move up">↑</button>');
            }
            if (index < timelineClips.length - 1) {
                $actions.append('<button type="button" class="btn btn-secondary btn-sm btn-icon btn-move-down" title="Move down">↓</button>');
            }
            $actions.append('<button type="button" class="btn btn-secondary btn-sm btn-icon btn-remove-clip" title="Remove">×</button>');
            $li.append($actions);
            $timelineList.append($li);
        });
        updateStats();
    }

    function moveClip(id, direction) {
        var idx = timelineClips.findIndex(function (c) { return c.id === id; });
        if (idx < 0) return;
        var newIdx = idx + direction;
        if (newIdx < 0 || newIdx >= timelineClips.length) return;
        var item = timelineClips.splice(idx, 1)[0];
        timelineClips.splice(newIdx, 0, item);
        renderTimelineUI();
        setStep(2);
    }

    function removeClip(id) {
        timelineClips = timelineClips.filter(function (c) { return c.id !== id; });
        renderTimelineUI();
    }

    function addSelectionToTimeline() {
        var sel = getSelection();
        if (sel.end - sel.start < 0.1) {
            showError('Select at least 0.1 seconds on the waveform first.');
            return;
        }
        timelineClips.push({
            id: nextClipId(),
            type: 'segment',
            srcStart: sel.start,
            srcEnd: sel.end
        });
        renderTimelineUI();
        setStep(2);
        hideMessages();
        showInfo('Clip added to timeline. Drag rows to reorder, or insert silence between clips.');
    }

    function insertSilence() {
        var duration = parseFloat($('#silence-duration').val()) || 1;
        if (duration < 0.1) {
            showError('Silence must be at least 0.1 seconds.');
            return;
        }
        timelineClips.push({
            id: nextClipId(),
            type: 'silence',
            duration: duration
        });
        renderTimelineUI();
        setStep(2);
        showInfo('Silence gap added (' + duration.toFixed(1) + 's).');
    }

    function detectNonSilentSegments(buffer, threshold, minSilenceSec) {
        var rate = buffer.sampleRate;
        var data = buffer.getChannelData(0);
        var windowSize = Math.max(1, Math.floor(rate * 0.02));
        var minSilenceSamples = Math.floor(minSilenceSec * rate);
        var segments = [];
        var inSound = false;
        var segStart = 0;
        var silenceRun = 0;

        function rms(start) {
            var sum = 0;
            var end = Math.min(start + windowSize, data.length);
            for (var i = start; i < end; i++) {
                sum += data[i] * data[i];
            }
            return Math.sqrt(sum / (end - start));
        }

        for (var i = 0; i < data.length; i += windowSize) {
            var level = rms(i);
            if (level >= threshold) {
                if (!inSound) {
                    segStart = i / rate;
                    inSound = true;
                }
                silenceRun = 0;
            } else if (inSound) {
                silenceRun += windowSize;
                if (silenceRun >= minSilenceSamples) {
                    segments.push({ start: segStart, end: (i - silenceRun) / rate });
                    inSound = false;
                    silenceRun = 0;
                }
            }
        }

        if (inSound) {
            segments.push({ start: segStart, end: buffer.duration });
        }

        return segments.filter(function (s) { return s.end - s.start >= 0.1; });
    }

    function removeBlankAreas() {
        if (!decodedBuffer) {
            showError('Audio is still loading. Wait a moment and try again.');
            return;
        }

        var threshold = parseFloat($('#silence-threshold').val()) || 0.015;
        var minSilence = parseFloat($('#min-silence').val()) || 0.3;
        var segments = detectNonSilentSegments(decodedBuffer, threshold, minSilence);

        if (segments.length === 0) {
            showError('No non-blank audio found. Try lowering sensitivity.');
            return;
        }

        timelineClips = segments.map(function (seg) {
            return {
                id: nextClipId(),
                type: 'segment',
                srcStart: seg.start,
                srcEnd: seg.end
            };
        });

        renderTimelineUI();
        setStep(2);
        showInfo('Removed blank areas — ' + segments.length + ' clip(s) on timeline. Drag to reorder or add silence gaps.');
    }

    function clearTimeline() {
        timelineClips = [];
        renderTimelineUI();
        showInfo('Timeline cleared.');
    }

    function resumeAudioIfNeeded() {
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
        if (wavesurfer && wavesurfer.backend) {
            var ac = wavesurfer.backend.ac || wavesurfer.backend.audioContext;
            if (ac && ac.state === 'suspended') ac.resume();
        }
    }

    function playTimelinePreview() {
        if (!decodedBuffer || timelineClips.length === 0) {
            showError('Add clips to the timeline first.');
            return;
        }

        stopPreviewSources();
        if (wavesurfer) wavesurfer.pause();
        resumeAudioIfNeeded();

        var when = audioContext.currentTime + 0.05;
        timelineClips.forEach(function (clip) {
            if (clip.type === 'silence') {
                when += clip.duration;
                return;
            }
            var src = audioContext.createBufferSource();
            src.buffer = decodedBuffer;
            src.connect(audioContext.destination);
            var dur = clip.srcEnd - clip.srcStart;
            src.start(when, clip.srcStart, dur);
            previewSources.push(src);
            when += dur;
        });

        showInfo('Playing timeline preview…');
    }

    function applyFade(buffer, fadeIn, fadeOut) {
        var channels = buffer.numberOfChannels;
        var length = buffer.length;
        var rate = buffer.sampleRate;
        var fadeInSamples = Math.floor(fadeIn * rate);
        var fadeOutSamples = Math.floor(fadeOut * rate);

        for (var c = 0; c < channels; c++) {
            var data = buffer.getChannelData(c);
            var i;
            for (i = 0; i < fadeInSamples && i < length; i++) {
                data[i] *= fadeInSamples > 0 ? i / fadeInSamples : 1;
            }
            for (i = 0; i < fadeOutSamples && i < length; i++) {
                var idx = length - 1 - i;
                data[idx] *= fadeOutSamples > 0 ? i / fadeOutSamples : 1;
            }
        }
        return buffer;
    }

    function applyGain(buffer, gain) {
        if (gain === 1) return buffer;
        for (var c = 0; c < buffer.numberOfChannels; c++) {
            var data = buffer.getChannelData(c);
            for (var i = 0; i < data.length; i++) {
                data[i] = Math.max(-1, Math.min(1, data[i] * gain));
            }
        }
        return buffer;
    }

    function copySegmentBuffer(ctx, source, srcStart, srcEnd) {
        var rate = source.sampleRate;
        var startSample = Math.floor(srcStart * rate);
        var endSample = Math.floor(srcEnd * rate);
        var length = endSample - startSample;
        var out = ctx.createBuffer(source.numberOfChannels, length, rate);
        for (var c = 0; c < source.numberOfChannels; c++) {
            out.getChannelData(c).set(source.getChannelData(c).subarray(startSample, endSample));
        }
        return out;
    }

    function createSilenceBuffer(ctx, source, durationSec) {
        var length = Math.floor(durationSec * source.sampleRate);
        return ctx.createBuffer(source.numberOfChannels, length, source.sampleRate);
    }

    function concatBuffers(ctx, parts) {
        var channels = parts[0].numberOfChannels;
        var rate = parts[0].sampleRate;
        var total = parts.reduce(function (sum, p) { return sum + p.length; }, 0);
        var out = ctx.createBuffer(channels, total, rate);
        var offset = 0;
        parts.forEach(function (part) {
            for (var c = 0; c < channels; c++) {
                out.getChannelData(c).set(part.getChannelData(c), offset);
            }
            offset += part.length;
        });
        return out;
    }

    function bufferToWav(buffer) {
        var numChannels = buffer.numberOfChannels;
        var sampleRate = buffer.sampleRate;
        var bitDepth = 16;
        var bytesPerSample = bitDepth / 8;
        var blockAlign = numChannels * bytesPerSample;
        var length = buffer.length;
        var dataSize = length * blockAlign;
        var arrayBuffer = new ArrayBuffer(44 + dataSize);
        var view = new DataView(arrayBuffer);

        function writeString(offset, str) {
            for (var i = 0; i < str.length; i++) {
                view.setUint8(offset + i, str.charCodeAt(i));
            }
        }

        writeString(0, 'RIFF');
        view.setUint32(4, 36 + dataSize, true);
        writeString(8, 'WAVE');
        writeString(12, 'fmt ');
        view.setUint32(16, 16, true);
        view.setUint16(20, 1, true);
        view.setUint16(22, numChannels, true);
        view.setUint32(24, sampleRate, true);
        view.setUint32(28, sampleRate * blockAlign, true);
        view.setUint16(32, blockAlign, true);
        view.setUint16(34, bitDepth, true);
        writeString(36, 'data');
        view.setUint32(40, dataSize, true);

        var offset = 44;
        for (var i = 0; i < length; i++) {
            for (var c = 0; c < numChannels; c++) {
                var sample = Math.max(-1, Math.min(1, buffer.getChannelData(c)[i]));
                view.setInt16(offset, sample < 0 ? sample * 0x8000 : sample * 0x7fff, true);
                offset += 2;
            }
        }

        return new Blob([arrayBuffer], { type: 'audio/wav' });
    }

    function bufferToMp3(buffer, bitrate) {
        var channels = buffer.numberOfChannels;
        var sampleRate = buffer.sampleRate;
        var left = buffer.getChannelData(0);
        var right = channels > 1 ? buffer.getChannelData(1) : left;
        var mp3encoder = new lamejs.Mp3Encoder(channels, sampleRate, bitrate);
        var mp3Data = [];
        var blockSize = 1152;
        var chLeft = new Int16Array(buffer.length);
        var chRight = channels > 1 ? new Int16Array(buffer.length) : chLeft;

        for (var i = 0; i < buffer.length; i++) {
            var l = Math.max(-1, Math.min(1, left[i]));
            chLeft[i] = l < 0 ? l * 0x8000 : l * 0x7fff;
            if (channels > 1) {
                var r = Math.max(-1, Math.min(1, right[i]));
                chRight[i] = r < 0 ? r * 0x8000 : r * 0x7fff;
            }
        }

        for (var k = 0; k < buffer.length; k += blockSize) {
            var mp3buf = channels > 1
                ? mp3encoder.encodeBuffer(chLeft.subarray(k, k + blockSize), chRight.subarray(k, k + blockSize))
                : mp3encoder.encodeBuffer(chLeft.subarray(k, k + blockSize));
            if (mp3buf.length > 0) mp3Data.push(mp3buf);
        }

        var end = mp3encoder.flush();
        if (end.length > 0) mp3Data.push(end);

        return new Blob(mp3Data, { type: 'audio/mp3' });
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

    async function buildOutputBuffer() {
        if (!decodedBuffer) {
            if (originalFile) await decodeAudioFile(originalFile);
        }
        if (!decodedBuffer) throw new Error('Could not decode audio.');

        var ctx = audioContext || new (window.AudioContext || window.webkitAudioContext)();
        var parts = [];

        if (timelineClips.length > 0) {
            timelineClips.forEach(function (clip) {
                if (clip.type === 'silence') {
                    parts.push(createSilenceBuffer(ctx, decodedBuffer, clip.duration));
                } else {
                    parts.push(copySegmentBuffer(ctx, decodedBuffer, clip.srcStart, clip.srcEnd));
                }
            });
        } else {
            var sel = getSelection();
            if (sel.end - sel.start < 0.1) {
                throw new Error('Add clips to the timeline or select a range to export.');
            }
            parts.push(copySegmentBuffer(ctx, decodedBuffer, sel.start, sel.end));
        }

        var merged = concatBuffers(ctx, parts);
        var fadeIn = parseFloat($('#fade-in').val()) || 0;
        var fadeOut = parseFloat($('#fade-out').val()) || 0;
        var maxFade = merged.duration / 2;
        applyFade(merged, Math.min(fadeIn, maxFade), Math.min(fadeOut, maxFade));
        applyGain(merged, parseFloat($('#volume-gain').val()) || 1);
        return merged;
    }

    async function exportAudio() {
        if (!originalFile || !wavesurfer) return;

        hideMessages();
        setExportProgress(0.1, 'Building output…');
        $('#btn-export').prop('disabled', true);

        try {
            var output = await buildOutputBuffer();

            setExportProgress(0.65, 'Encoding…');
            var format = $('#export-format').val();
            var blob;
            var ext;

            if (format === 'mp3') {
                var bitrate = parseInt($('#mp3-bitrate').val(), 10) || 192;
                blob = bufferToMp3(output, bitrate);
                ext = 'mp3';
            } else {
                blob = bufferToWav(output);
                ext = 'wav';
            }

            setExportProgress(1, 'Done!');
            setStep(3);

            var baseName = originalFile.name.replace(/\.[^.]+$/, '');
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = baseName + '-edited.' + ext;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            showInfo('Download started — ' + formatBytes(blob.size) + ', ' + formatTime(output.duration) + ' long.');
        } catch (err) {
            showError('Export failed: ' + (err.message || 'Unknown error'));
        } finally {
            hideExportProgress();
            $('#btn-export').prop('disabled', false);
        }
    }

    function resetTool() {
        destroyWaveSurfer();
        originalFile = null;
        decodedBuffer = null;
        audioContext = null;
        timelineClips = [];
        renderTimelineUI();
        $controls.addClass('hidden');
        $dropZone.removeClass('hidden');
        $fileInput.val('');
        hideMessages();
        hideExportProgress();
        $('#stat-original, #stat-duration, #stat-output').text('—');
        setStep(1);
    }

    async function loadFile(file) {
        if (!file || !file.type.match(/^audio\//) && !file.name.match(/\.(mp3|wav|ogg|m4a|flac|aac|webm)$/i)) {
            showError('Please select an audio file (MP3, WAV, OGG, M4A, FLAC).');
            return;
        }

        hideMessages();
        originalFile = file;
        decodedBuffer = null;
        $('#stat-original').text(formatBytes(file.size));
        $controls.removeClass('hidden');
        $dropZone.addClass('hidden');
        setStep(1);

        initWaveSurfer(file);
        try {
            await decodeAudioFile(file);
        } catch (e) {
            console.warn('Decode for timeline failed:', e);
        }
    }

    // Drop zone
    $dropZone.on('click', function () { $fileInput.trigger('click'); });
    $dropZone.on('dragover', function (e) {
        e.preventDefault();
        $dropZone.addClass('dragover');
    });
    $dropZone.on('dragleave drop', function () { $dropZone.removeClass('dragover'); });
    $dropZone.on('drop', function (e) {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) loadFile(files[0]);
    });
    $fileInput.on('change', function () {
        if (this.files.length) loadFile(this.files[0]);
    });

    // Playback
    $('#btn-play').on('click', function () {
        if (!wavesurfer) return;
        stopPreviewSources();
        resumeAudioIfNeeded();
        wavesurfer.playPause();
        isPlayingSelection = false;
    });

    $('#btn-play-selection').on('click', function () {
        if (!wavesurfer || !activeRegion) return;
        stopPreviewSources();
        resumeAudioIfNeeded();
        isPlayingSelection = true;
        wavesurfer.play(activeRegion.start, activeRegion.end);
    });

    $('#btn-play-timeline').on('click', function () {
        playTimelinePreview();
    });

    $('#btn-stop').on('click', function () {
        stopPreviewSources();
        if (wavesurfer) wavesurfer.stop();
        isPlayingSelection = false;
        $('#btn-play').text('▶ Play');
    });

    $('#btn-zoom-in').on('click', function () {
        if (!wavesurfer) return;
        zoomLevel = Math.min(500, zoomLevel * 1.5);
        wavesurfer.zoom(zoomLevel);
    });
    $('#btn-zoom-out').on('click', function () {
        if (!wavesurfer) return;
        zoomLevel = Math.max(10, zoomLevel / 1.5);
        wavesurfer.zoom(zoomLevel);
    });
    $('#btn-fit').on('click', function () {
        if (!wavesurfer) return;
        zoomLevel = 50;
        wavesurfer.zoom(0);
    });

    $('#start-time, #end-time').on('change blur', applyInputsToRegion);

    $('#btn-set-from-playhead').on('click', function () {
        if (!wavesurfer) return;
        var t = wavesurfer.getCurrentTime();
        if (!activeRegion) {
            createRegion(t, getDuration());
        } else {
            var mid = (activeRegion.start + activeRegion.end) / 2;
            if (t <= mid) {
                activeRegion.update({ start: t });
            } else {
                activeRegion.update({ end: t });
            }
            syncInputsFromRegion();
        }
    });

    $('#btn-select-all').on('click', function () {
        if (!wavesurfer) return;
        if (activeRegion) activeRegion.remove();
        createRegion(0, getDuration());
    });

    $('#btn-add-clip').on('click', addSelectionToTimeline);
    $('#btn-add-silence').on('click', insertSilence);
    $('#btn-remove-silence').on('click', removeBlankAreas);
    $('#btn-clear-timeline').on('click', clearTimeline);

    // Timeline drag reorder
    $timelineList.on('dragstart', '.timeline-item', function (e) {
        dragClipId = $(this).data('id');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        $(this).addClass('drag-over');
    });

    $timelineList.on('dragend', '.timeline-item', function () {
        dragClipId = null;
        $('.timeline-item').removeClass('drag-over');
    });

    $timelineList.on('dragover', '.timeline-item', function (e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        $('.timeline-item').removeClass('drag-over');
        $(this).addClass('drag-over');
    });

    $timelineList.on('drop', '.timeline-item', function (e) {
        e.preventDefault();
        var targetId = $(this).data('id');
        if (!dragClipId || dragClipId === targetId) return;

        var fromIdx = timelineClips.findIndex(function (c) { return c.id === dragClipId; });
        var toIdx = timelineClips.findIndex(function (c) { return c.id === targetId; });
        if (fromIdx < 0 || toIdx < 0) return;

        var item = timelineClips.splice(fromIdx, 1)[0];
        timelineClips.splice(toIdx, 0, item);
        renderTimelineUI();
        $('.timeline-item').removeClass('drag-over');
        dragClipId = null;
    });

    $timelineList.on('click', '.btn-move-up', function () {
        moveClip($(this).closest('.timeline-item').data('id'), -1);
    });
    $timelineList.on('click', '.btn-move-down', function () {
        moveClip($(this).closest('.timeline-item').data('id'), 1);
    });
    $timelineList.on('click', '.btn-remove-clip', function () {
        removeClip($(this).closest('.timeline-item').data('id'));
    });

    $('#btn-export').on('click', exportAudio);
    $('#btn-reset').on('click', resetTool);

    $('#export-format').on('change', function () {
        $('#mp3-bitrate').prop('disabled', $(this).val() !== 'mp3');
    });
});
