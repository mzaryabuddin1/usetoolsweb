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

    var wavesurfer = null;
    var regionsPlugin = null;
    var activeRegion = null;
    var originalFile = null;
    var loopInterval = null;
    var isPlayingSelection = false;
    var updatingFromRegion = false;

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

    function updateStats() {
        var sel = getSelection();
        var len = Math.max(0, sel.end - sel.start);
        $('#stat-selection').text(formatTime(len));
        $('#duration-display').val(formatTime(len));
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
        if (updatingFromRegion || !wavesurfer) return;
        var dur = getDuration();
        var start = parseTime($('#start-time').val());
        var end = parseTime($('#end-time').val());
        if (isNaN(start)) start = 0;
        if (isNaN(end)) end = dur;
        start = Math.max(0, Math.min(start, dur));
        end = Math.max(start + 0.1, Math.min(end, dur));

        if (activeRegion) {
            activeRegion.update({ start: start, end: end });
        } else {
            createRegion(start, end);
        }
        updateStats();
    }

    function createRegion(start, end) {
        if (!wavesurfer) return null;
        if (activeRegion) {
            activeRegion.remove();
        }
        activeRegion = wavesurfer.addRegion({
            start: start,
            end: end,
            color: 'rgba(37, 99, 235, 0.25)',
            drag: true,
            resize: true
        });
        syncInputsFromRegion();
        return activeRegion;
    }

    function destroyWaveSurfer() {
        stopLoop();
        if (wavesurfer) {
            wavesurfer.destroy();
            wavesurfer = null;
        }
        regionsPlugin = null;
        activeRegion = null;
    }

    function initWaveSurfer(file) {
        destroyWaveSurfer();
        $('#waveform').empty();

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
            if (activeRegion && activeRegion !== region) {
                activeRegion.remove();
            }
            activeRegion = region;
            syncInputsFromRegion();
        });

        wavesurfer.on('region-updated', syncInputsFromRegion);
        wavesurfer.on('region-update-end', syncInputsFromRegion);

        wavesurfer.loadBlob(file);
    }

    function stopLoop() {
        isPlayingSelection = false;
        if (loopInterval) {
            clearInterval(loopInterval);
            loopInterval = null;
        }
    }

    function loadFile(file) {
        if (!file || !file.type.match(/^audio\//) && !file.name.match(/\.(mp3|wav|ogg|m4a|flac|aac|webm)$/i)) {
            showError('Please select an audio file (MP3, WAV, OGG, M4A, FLAC).');
            return;
        }

        hideMessages();
        originalFile = file;
        $('#stat-original').text(formatBytes(file.size));
        $controls.removeClass('hidden');
        $dropZone.addClass('hidden');
        setStep(1);
        initWaveSurfer(file);
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
                data[i] *= i / fadeInSamples;
            }
            for (i = 0; i < fadeOutSamples && i < length; i++) {
                var idx = length - 1 - i;
                data[idx] *= i / fadeOutSamples;
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

    function bufferToWav(buffer) {
        var numChannels = buffer.numberOfChannels;
        var sampleRate = buffer.sampleRate;
        var format = 1;
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
        view.setUint16(20, format, true);
        view.setUint16(22, numChannels, true);
        view.setUint32(24, sampleRate, true);
        view.setUint32(28, sampleRate * blockAlign, true);
        view.setUint16(32, blockAlign, true);
        view.setUint16(34, bitDepth, true);
        writeString(36, 'data');
        view.setUint32(40, dataSize, true);

        var offset = 44;
        var channelData = [];
        for (var ch = 0; ch < numChannels; ch++) {
            channelData.push(buffer.getChannelData(ch));
        }

        for (var i = 0; i < length; i++) {
            for (var c = 0; c < numChannels; c++) {
                var sample = Math.max(-1, Math.min(1, channelData[c][i]));
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

        var samples = new Int16Array(buffer.length);
        for (var i = 0; i < buffer.length; i++) {
            var l = Math.max(-1, Math.min(1, left[i]));
            samples[i] = l < 0 ? l * 0x8000 : l * 0x7fff;
        }

        var mp3encoder = new lamejs.Mp3Encoder(channels, sampleRate, bitrate);
        var mp3Data = [];
        var blockSize = 1152;
        var chLeft = samples;
        var chRight = channels > 1 ? new Int16Array(buffer.length) : chLeft;

        if (channels > 1) {
            for (var j = 0; j < buffer.length; j++) {
                var r = Math.max(-1, Math.min(1, right[j]));
                chRight[j] = r < 0 ? r * 0x8000 : r * 0x7fff;
            }
        }

        for (var k = 0; k < samples.length; k += blockSize) {
            var leftChunk = chLeft.subarray(k, k + blockSize);
            var rightChunk = chRight.subarray(k, k + blockSize);
            var mp3buf = channels > 1
                ? mp3encoder.encodeBuffer(leftChunk, rightChunk)
                : mp3encoder.encodeBuffer(leftChunk);
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

    async function exportAudio() {
        if (!originalFile || !wavesurfer) return;

        var sel = getSelection();
        if (sel.end - sel.start < 0.1) {
            showError('Selection must be at least 0.1 seconds.');
            return;
        }

        hideMessages();
        setExportProgress(0.1, 'Decoding audio…');
        $('#btn-export').prop('disabled', true);

        try {
            var arrayBuffer = await originalFile.arrayBuffer();
            var audioContext = new (window.AudioContext || window.webkitAudioContext)();
            var decoded = await audioContext.decodeAudioData(arrayBuffer.slice(0));

            var rate = decoded.sampleRate;
            var startSample = Math.floor(sel.start * rate);
            var endSample = Math.floor(sel.end * rate);
            var length = endSample - startSample;

            if (length <= 0) {
                throw new Error('Invalid selection range.');
            }

            setExportProgress(0.35, 'Trimming selection…');

            var trimmed = audioContext.createBuffer(
                decoded.numberOfChannels,
                length,
                rate
            );

            for (var c = 0; c < decoded.numberOfChannels; c++) {
                trimmed.getChannelData(c).set(
                    decoded.getChannelData(c).subarray(startSample, endSample)
                );
            }

            var fadeIn = parseFloat($('#fade-in').val()) || 0;
            var fadeOut = parseFloat($('#fade-out').val()) || 0;
            var maxFade = (sel.end - sel.start) / 2;
            fadeIn = Math.min(fadeIn, maxFade);
            fadeOut = Math.min(fadeOut, maxFade);

            applyFade(trimmed, fadeIn, fadeOut);
            applyGain(trimmed, parseFloat($('#volume-gain').val()) || 1);

            setExportProgress(0.65, 'Encoding…');

            var format = $('#export-format').val();
            var blob;
            var ext;

            if (format === 'mp3') {
                var bitrate = parseInt($('#mp3-bitrate').val(), 10) || 192;
                blob = bufferToMp3(trimmed, bitrate);
                ext = 'mp3';
            } else {
                blob = bufferToWav(trimmed);
                ext = 'wav';
            }

            setExportProgress(1, 'Done!');
            setStep(3);

            var baseName = originalFile.name.replace(/\.[^.]+$/, '');
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = baseName + '-trimmed.' + ext;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            showInfo('Download started — ' + formatBytes(blob.size) + ' trimmed file.');
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
        $controls.addClass('hidden');
        $dropZone.removeClass('hidden');
        $fileInput.val('');
        hideMessages();
        hideExportProgress();
        $('#stat-original, #stat-duration, #stat-selection').text('—');
        setStep(1);
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

    function resumeAudioIfNeeded() {
        if (!wavesurfer || !wavesurfer.backend) return;
        var ac = wavesurfer.backend.ac || wavesurfer.backend.audioContext;
        if (ac && ac.state === 'suspended') {
            ac.resume();
        }
    }

    // Playback
    $('#btn-play').on('click', function () {
        if (!wavesurfer) return;
        resumeAudioIfNeeded();
        wavesurfer.playPause();
        stopLoop();
        isPlayingSelection = false;
    });

    $('#btn-play-selection').on('click', function () {
        if (!wavesurfer || !activeRegion) return;
        resumeAudioIfNeeded();
        isPlayingSelection = true;
        wavesurfer.play(activeRegion.start, activeRegion.end);
    });

    $('#btn-stop').on('click', function () {
        if (!wavesurfer) return;
        wavesurfer.stop();
        stopLoop();
        $('#btn-play').text('▶ Play');
    });

    var zoomLevel = 50;

    // Zoom
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

    // Time inputs
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
        }
        syncInputsFromRegion();
    });

    $('#btn-select-all').on('click', function () {
        if (!wavesurfer) return;
        createRegion(0, getDuration());
    });

    $('#btn-export').on('click', exportAudio);
    $('#btn-reset').on('click', resetTool);

    $('#export-format').on('change', function () {
        $('#mp3-bitrate').prop('disabled', $(this).val() !== 'mp3');
    });
});
