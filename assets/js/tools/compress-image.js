$(function () {
    'use strict';

    var $dropZone = $('#drop-zone');
    var $fileInput = $('#file-input');
    var $controls = $('#compress-controls');
    var $preview = $('#preview');
    var $quality = $('#quality');
    var $qualityValue = $('#quality-value');
    var $btnDownload = $('#btn-download');
    var $btnReset = $('#btn-reset');
    var $error = $('#compress-error');

    var originalFile = null;
    var compressedBlob = null;
    var outputFileName = 'compressed.jpg';

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
    }

    function hideError() {
        $error.addClass('hidden');
    }

    function loadFile(file) {
        if (!file || !file.type.match(/^image\/(jpeg|png|webp)$/)) {
            showError('Please select a JPG, PNG, or WebP image.');
            return;
        }

        hideError();
        originalFile = file;
        outputFileName = file.name.replace(/\.[^.]+$/, '') + '-compressed.jpg';

        var reader = new FileReader();
        reader.onload = function (e) {
            $preview.attr('src', e.target.result);
            $controls.removeClass('hidden');
            $dropZone.addClass('hidden');
            compressImage();
        };
        reader.readAsDataURL(file);
    }

    function compressImage() {
        if (!originalFile) return;

        var quality = parseInt($quality.val(), 10) / 100;
        var img = new Image();

        img.onload = function () {
            var canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;

            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            canvas.toBlob(function (blob) {
                if (!blob) {
                    showError('Compression failed. Try a different image.');
                    return;
                }

                compressedBlob = blob;
                var saved = originalFile.size - blob.size;
                var savedPct = originalFile.size > 0
                    ? Math.round((saved / originalFile.size) * 100)
                    : 0;

                $('#stat-original').text(formatBytes(originalFile.size));
                $('#stat-compressed').text(formatBytes(blob.size));
                $('#stat-saved').text(savedPct + '%');
                $btnDownload.prop('disabled', false);
            }, 'image/jpeg', quality);
        };

        img.onerror = function () {
            showError('Could not load the image.');
        };

        img.src = $preview.attr('src');
    }

    $dropZone.on('click', function () {
        $fileInput.trigger('click');
    });

    $fileInput.on('change', function () {
        if (this.files && this.files[0]) {
            loadFile(this.files[0]);
        }
    });

    $dropZone.on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    $dropZone.on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });

    $dropZone.on('drop', function (e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files && files[0]) {
            loadFile(files[0]);
        }
    });

    $quality.on('input', function () {
        $qualityValue.text($(this).val());
        compressImage();
    });

    $btnDownload.on('click', function () {
        if (!compressedBlob) return;

        var url = URL.createObjectURL(compressedBlob);
        var a = document.createElement('a');
        a.href = url;
        a.download = outputFileName;
        a.click();
        URL.revokeObjectURL(url);
    });

    $btnReset.on('click', function () {
        originalFile = null;
        compressedBlob = null;
        $fileInput.val('');
        $preview.attr('src', '');
        $controls.addClass('hidden');
        $dropZone.removeClass('hidden');
        $btnDownload.prop('disabled', true);
        hideError();
    });
});
