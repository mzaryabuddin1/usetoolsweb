$(function () {
    'use strict';

    var qrInstance = null;

    var $text = $('#qr-text');
    var $size = $('#qr-size');
    var $sizeValue = $('#qr-size-value');
    var $qrcode = $('#qrcode');
    var $btnGenerate = $('#btn-generate-qr');
    var $btnDownload = $('#btn-download-qr');
    var $error = $('#qr-error');

    function showError(msg) {
        $error.text(msg).removeClass('hidden');
    }

    function hideError() {
        $error.addClass('hidden');
    }

    function generateQR() {
        var text = $.trim($text.val());
        if (!text) {
            showError('Please enter some text or a URL.');
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

    $size.on('input', function () {
        $sizeValue.text($(this).val());
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
});
