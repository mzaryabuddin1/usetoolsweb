$(function () {
    'use strict';

    $('#btn-barcode-generate').on('click', function () {
        var text = $.trim($('#barcode-text').val());
        if (!text) {
            $('#barcode-error').text('Enter text to encode.').removeClass('hidden');
            $('#btn-barcode-download').prop('disabled', true);
            return;
        }

        try {
            JsBarcode('#barcode-canvas', text, {
                format: 'CODE128',
                displayValue: true,
                margin: 10
            });
            $('#barcode-error').addClass('hidden');
            $('#btn-barcode-download').prop('disabled', false);
        } catch (e) {
            $('#barcode-error').text('Failed to generate barcode: ' + e.message).removeClass('hidden');
            $('#btn-barcode-download').prop('disabled', true);
        }
    });

    $('#btn-barcode-download').on('click', function () {
        var svg = document.getElementById('barcode-canvas');
        if (!svg || !svg.innerHTML) return;

        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        var img = new Image();
        var svgData = new XMLSerializer().serializeToString(svg);
        var blob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        var url = URL.createObjectURL(blob);

        img.onload = function () {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            URL.revokeObjectURL(url);

            var link = document.createElement('a');
            link.download = 'barcode.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        img.src = url;
    });

    $('#btn-barcode-clear').on('click', function () {
        $('#barcode-text').val('');
        $('#barcode-canvas').empty();
        $('#barcode-error').addClass('hidden');
        $('#btn-barcode-download').prop('disabled', true);
    });
});
