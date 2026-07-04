$(function () {
    'use strict';

    var qrInstance = null;
    var selectedFile = null;
    var shareUrl = '';
    var uploading = false;

    var $text = $('#qr-text');
    var $size = $('#qr-size');
    var $sizeValue = $('#qr-size-value');
    var $qrcode = $('#qrcode');
    var $btnGenerate = $('#btn-generate-qr');
    var $btnDownload = $('#btn-download-qr');
    var $error = $('#qr-error');
    var $status = $('#qr-status');

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
    }

    function hideError() {
        $error.addClass('hidden');
    }

    function showStatus(msg, type) {
        $status.text(msg)
            .removeClass('hidden alert-info alert-success alert-error')
            .addClass(type === 'error' ? 'alert-error' : (type === 'success' ? 'alert-success' : 'alert-info'));
    }

    function hideStatus() {
        $status.addClass('hidden');
    }

    function isFileMode() {
        return $('.qr-mode-tab.active').data('mode') === 'file';
    }

    function getQrText() {
        if (isFileMode()) {
            return shareUrl || $.trim($('#qr-share-url').val());
        }
        return $.trim($text.val());
    }

    function generateQR() {
        var text = getQrText();
        if (!text) {
            if (!isFileMode()) {
                showError('Enter text or a URL.');
            }
            $btnDownload.prop('disabled', true);
            return;
        }

        hideError();
        $qrcode.empty();

        var size = parseInt($size.val(), 10);

        qrInstance = new QRCode(document.getElementById('qrcode'), {
            text: text,
            width: size,
            height: size,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });

        $btnDownload.prop('disabled', false);
    }

    function setMode(mode) {
        $('.qr-mode-tab').removeClass('active').attr('aria-selected', 'false');
        $('.qr-mode-tab[data-mode="' + mode + '"]').addClass('active').attr('aria-selected', 'true');
        $('#qr-mode-text, #qr-mode-file').addClass('hidden');
        $('#qr-mode-' + mode).removeClass('hidden');
        $btnGenerate.toggleClass('hidden', mode === 'file');
        hideError();
        hideStatus();
    }

    $('.qr-mode-tab').on('click', function () {
        setMode($(this).data('mode'));
    });

    $size.on('input', function () {
        $sizeValue.text($(this).val());
        if (shareUrl) {
            generateQR();
        }
    });

    $btnGenerate.on('click', generateQR);

    $text.on('keydown', function (e) {
        if (e.key === 'Enter') {
            generateQR();
        }
    });

    $btnDownload.on('click', function () {
        var canvas = $qrcode.find('canvas')[0];
        if (!canvas) return;

        var url = canvas.toDataURL('image/png');
        var a = document.createElement('a');
        a.href = url;
        a.download = 'qrcode.png';
        a.click();
    });

    // --- File upload share (auto on select) ---
    var $drop = $('#qr-file-drop');
    var $fileInput = $('#qr-file-input');
    var $fileSelected = $('#qr-file-selected');
    var $fileName = $('#qr-file-name');
    var $shareResult = $('#qr-share-result');
    var $shareUrlInput = $('#qr-share-url');
    var $shareExpiry = $('#qr-share-expiry');

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function resetShare() {
        shareUrl = '';
        $shareResult.addClass('hidden');
        $shareUrlInput.val('');
        $shareExpiry.text('');
        $qrcode.empty();
        $btnDownload.prop('disabled', true);
    }

    function setDropBusy(busy, label) {
        uploading = busy;
        $drop.toggleClass('qr-drop-busy', busy);
        if (busy && label) {
            $drop.find('p:first strong').text(label);
        } else if (!busy) {
            $drop.find('p:first strong').text('Drop a file here');
        }
    }

    async function uploadAndGenerate(file) {
        if (!file || uploading) return;

        resetShare();
        selectedFile = file;
        $fileName.text(file.name + ' (' + formatBytes(file.size) + ')');
        $fileSelected.removeClass('hidden');

        hideError();
        setDropBusy(true, 'Uploading…');
        showStatus('Uploading file and generating QR code…', 'info');

        try {
            var fd = new FormData();
            fd.append('file', file);

            var res = await fetch('/api/qr-share-upload.php', {
                method: 'POST',
                body: fd
            });

            var data = await res.json();
            if (!data.ok) {
                throw new Error(data.error || 'Upload failed.');
            }

            shareUrl = data.url;
            $shareUrlInput.val(data.url);
            $shareResult.removeClass('hidden');

            var expiry = new Date(data.expires_at);
            $shareExpiry.text(
                'File: ' + data.filename + ' — deleted after ' + data.expires_in_days + ' days (expires ' +
                expiry.toLocaleDateString() + ')'
            );

            generateQR();
            showStatus('QR code ready! Scan to download the file.', 'success');
        } catch (err) {
            selectedFile = null;
            $fileSelected.addClass('hidden');
            $fileInput.val('');
            showError(err.message || 'Upload failed.');
            hideStatus();
        } finally {
            setDropBusy(false);
        }
    }

    $drop.on('click', function () {
        if (!uploading) {
            $fileInput.trigger('click');
        }
    });

    $fileInput.on('change', function () {
        if (this.files && this.files[0]) {
            uploadAndGenerate(this.files[0]);
        }
    });

    $drop.on('dragover', function (e) {
        e.preventDefault();
        if (!uploading) {
            $(this).addClass('dragover');
        }
    });

    $drop.on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });

    $drop.on('drop', function (e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files && files[0]) {
            uploadAndGenerate(files[0]);
        }
    });

    $('#qr-file-clear').on('click', function () {
        selectedFile = null;
        $fileInput.val('');
        $fileSelected.addClass('hidden');
        resetShare();
        hideError();
        hideStatus();
    });

    $('#btn-copy-share-url').on('click', function () {
        var url = $shareUrlInput.val();
        if (!url) return;
        navigator.clipboard.writeText(url).then(function () {
            showStatus('Link copied to clipboard.', 'success');
        });
    });

    setMode('text');
});
