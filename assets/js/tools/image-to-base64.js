$(function () {
    'use strict';

    $('.img-tab').on('click', function () {
        var tab = $(this).data('tab');
        $('.img-tab').removeClass('active');
        $(this).addClass('active');
        if (tab === 'encode') {
            $('#img-encode-panel').removeClass('hidden');
            $('#img-decode-panel').addClass('hidden');
        } else {
            $('#img-encode-panel').addClass('hidden');
            $('#img-decode-panel').removeClass('hidden');
        }
        $('#img-error').addClass('hidden');
    });

    $('#img-upload').on('change', function () {
        var file = this.files && this.files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#img-base64-out').val(e.target.result);
            $('#btn-img-copy').prop('disabled', false);
        };
        reader.readAsDataURL(file);
    });

    $('#btn-img-copy').on('click', function () {
        var text = $('#img-base64-out').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-img-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-img-preview').on('click', function () {
        var input = $.trim($('#img-base64-in').val());
        if (!input) {
            $('#img-error').text('Paste a Base64 string or data URI.').removeClass('hidden');
            return;
        }

        var src = input.indexOf('data:') === 0 ? input : 'data:image/png;base64,' + input;
        var $img = $('#img-preview');
        $img.off('error').on('error', function () {
            $('#img-error').text('Invalid Base64 image data.').removeClass('hidden');
            $img.addClass('hidden');
        });
        $img.off('load').on('load', function () {
            $('#img-error').addClass('hidden');
            $img.removeClass('hidden');
        });
        $img.attr('src', src);
    });
});
