$(function () {
    'use strict';

    var $dropZone = $('#resize-drop-zone');
    var $fileInput = $('#resize-file-input');
    var $controls = $('#resize-controls');
    var $preview = $('#resize-preview');
    var $width = $('#resize-width');
    var $height = $('#resize-height');
    var $percent = $('#resize-percent');
    var $error = $('#resize-error');

    var originalFile = null;
    var origW = 0;
    var origH = 0;
    var lockRatio = true;

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

    function loadFile(file) {
        if (!file || !file.type.match(/^image\/(jpeg|png|webp)$/)) {
            showError('Please select a JPG, PNG, or WebP image.');
            return;
        }

        hideError();
        originalFile = file;

        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                origW = img.width;
                origH = img.height;
                $preview.attr('src', e.target.result);
                $width.val(origW);
                $height.val(origH);
                $percent.val(100);
                $('#resize-percent-value').text('100');
                $('#resize-original-size').text('Original: ' + origW + ' × ' + origH + ' px');
                $controls.removeClass('hidden');
                $dropZone.addClass('hidden');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function resizeFromCanvas(callback) {
        var w = parseInt($width.val(), 10);
        var h = parseInt($height.val(), 10);
        if (!w || !h || w < 1 || h < 1) {
            showError('Enter valid width and height.');
            return;
        }

        var img = new Image();
        img.onload = function () {
            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            var mime = $('#resize-format').val();
            var quality = mime === 'image/png' ? undefined : 0.92;
            canvas.toBlob(callback, mime, quality);
        };
        img.src = $preview.attr('src');
    }

    $width.on('input', function () {
        if (!lockRatio || !origW) return;
        var w = parseInt($(this).val(), 10) || 1;
        $height.val(Math.round(w * origH / origW));
    });

    $height.on('input', function () {
        if (!lockRatio || !origH) return;
        var h = parseInt($(this).val(), 10) || 1;
        $width.val(Math.round(h * origW / origH));
    });

    $('#resize-lock-ratio').on('change', function () {
        lockRatio = $(this).is(':checked');
    });

    $percent.on('input', function () {
        var pct = parseInt($(this).val(), 10);
        $('#resize-percent-value').text(pct);
        $width.val(Math.round(origW * pct / 100));
        $height.val(Math.round(origH * pct / 100));
    });

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

    $('#btn-resize-download').on('click', function () {
        hideError();
        resizeFromCanvas(function (blob) {
            if (!blob) {
                showError('Resize failed.');
                return;
            }
            var mime = $('#resize-format').val();
            var name = (originalFile.name.replace(/\.[^.]+$/, '') || 'image') + '-resized.' + extForMime(mime);
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = name;
            a.click();
            URL.revokeObjectURL(url);
        });
    });

    $('#btn-resize-reset').on('click', function () {
        originalFile = null;
        origW = origH = 0;
        $fileInput.val('');
        $controls.addClass('hidden');
        $dropZone.removeClass('hidden');
        hideError();
    });
});
