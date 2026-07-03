$(function () {
    'use strict';

    var stream = null;
    var scanInterval = null;
    var video = document.getElementById('qr-video');
    var canvas = document.getElementById('qr-canvas');
    var ctx = canvas.getContext('2d');

    function stopCamera() {
        if (scanInterval) {
            clearInterval(scanInterval);
            scanInterval = null;
        }
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
        video.style.display = 'none';
        video.srcObject = null;
        $('#btn-qr-start').prop('disabled', false);
        $('#btn-qr-stop').prop('disabled', true);
    }

    function scanFrame() {
        if (!stream || video.readyState !== video.HAVE_ENOUGH_DATA) return;

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code && code.data) {
            $('#qr-decoded').val(code.data);
            $('#btn-qr-copy').prop('disabled', false);
            $('#qr-error').addClass('hidden');
        }
    }

    $('#btn-qr-start').on('click', function () {
        $('#qr-error').addClass('hidden');

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (s) {
                stream = s;
                video.srcObject = stream;
                video.style.display = 'block';
                video.play();
                $('#btn-qr-start').prop('disabled', true);
                $('#btn-qr-stop').prop('disabled', false);
                scanInterval = setInterval(scanFrame, 300);
            })
            .catch(function (err) {
                $('#qr-error').text('Camera access denied or unavailable: ' + err.message).removeClass('hidden');
            });
    });

    $('#btn-qr-stop').on('click', stopCamera);

    $('#btn-qr-copy').on('click', function () {
        var text = $('#qr-decoded').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-qr-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $(window).on('beforeunload', stopCamera);
});
