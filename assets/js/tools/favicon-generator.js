$(function () {
    'use strict';

    var sizes = [16, 32, 48];
    var sourceImg = null;

    function drawFavicons(img) {
        sizes.forEach(function (size) {
            var canvas = document.getElementById('fav-' + size);
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, size, size);
            ctx.drawImage(img, 0, 0, size, size);
        });
        $('#favicon-preview').removeClass('hidden');
    }

    $('#favicon-upload').on('change', function () {
        var file = this.files && this.files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                sourceImg = img;
                drawFavicons(img);
                $('#favicon-error').addClass('hidden');
            };
            img.onerror = function () {
                $('#favicon-error').text('Failed to load image.').removeClass('hidden');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    function downloadCanvas(size) {
        var canvas = document.getElementById('fav-' + size);
        var link = document.createElement('a');
        link.download = 'favicon-' + size + 'x' + size + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }

    $('.fav-download').on('click', function () {
        downloadCanvas($(this).data('size'));
    });

    $('#btn-fav-download-all').on('click', function () {
        sizes.forEach(function (size) {
            setTimeout(function () { downloadCanvas(size); }, size * 10);
        });
    });
});
