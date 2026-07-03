$(function () {
    'use strict';

    var $dropZone = $('#convert-drop-zone');
    var $fileInput = $('#convert-file-input');
    var $controls = $('#convert-controls');
    var $preview = $('#convert-preview');
    var $quality = $('#convert-quality');
    var $error = $('#convert-error');

    var originalFile = null;

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
    }

    function hideError() {
        $error.addClass('hidden');
    }

    function extForMime(mime) {
        if (mime === 'image/png') return 'png';
        if (mime === 'image/webp') return 'webp';
        return 'jpg';
    }

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function toggleQuality() {
        var isPng = $('#convert-format').val() === 'image/png';
        $('#convert-quality-wrap').toggleClass('hidden', isPng);
    }

    function loadFile(file) {
        if (!file || !file.type.match(/^image\/(jpeg|png|webp)$/)) {
            showError('Please select a JPG, PNG, or WebP image.');
            return;
        }

        hideError();
        originalFile = file;

        var reader = new FileReader();
        reader.onload = function (e) {
            $preview.attr('src', e.target.result);
            $('#convert-original-info').text(
                'Original: ' + file.name + ' (' + formatBytes(file.size) + ')'
            );
            $controls.removeClass('hidden');
            $dropZone.addClass('hidden');
            toggleQuality();
        };
        reader.readAsDataURL(file);
    }

    function convert(callback) {
        var img = new Image();
        img.onload = function () {
            var canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            canvas.getContext('2d').drawImage(img, 0, 0);
            var mime = $('#convert-format').val();
            var quality = mime === 'image/png' ? undefined : parseInt($quality.val(), 10) / 100;
            canvas.toBlob(callback, mime, quality);
        };
        img.src = $preview.attr('src');
    }

    $dropZone.on('click', function () { $fileInput.trigger('click'); });
    $fileInput.on('change', function () {
        if (this.files && this.files[0]) loadFile(this.files[0]);
    });

    $dropZone.on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    }).on('drop', function (e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files && files[0]) loadFile(files[0]);
    });

    $('#convert-format').on('change', toggleQuality);
    $quality.on('input', function () {
        $('#convert-quality-value').text($(this).val());
    });

    $('#btn-convert-download').on('click', function () {
        hideError();
        convert(function (blob) {
            if (!blob) {
                showError('Conversion failed.');
                return;
            }
            var mime = $('#convert-format').val();
            var name = (originalFile.name.replace(/\.[^.]+$/, '') || 'image') + '.' + extForMime(mime);
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = name;
            a.click();
            URL.revokeObjectURL(url);
        });
    });

    $('#btn-convert-reset').on('click', function () {
        originalFile = null;
        $fileInput.val('');
        $controls.addClass('hidden');
        $dropZone.removeClass('hidden');
        hideError();
    });
});
