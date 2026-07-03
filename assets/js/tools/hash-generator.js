$(function () {
    'use strict';

    var $input = $('#hash-input');
    var $results = $('#hash-results');

    function generateHashes() {
        var text = $input.val();
        if (!text) return;

        $('#hash-md5').text(CryptoJS.MD5(text).toString());
        $('#hash-sha1').text(CryptoJS.SHA1(text).toString());
        $('#hash-sha256').text(CryptoJS.SHA256(text).toString());
        $('#hash-sha512').text(CryptoJS.SHA512(text).toString());
        $results.removeClass('hidden');
    }

    $('#btn-hash-generate').on('click', generateHashes);

    $input.on('input', function () {
        if ($.trim($input.val())) {
            generateHashes();
        } else {
            $results.addClass('hidden');
        }
    });

    $('.btn-copy-hash').on('click', function () {
        var id = $(this).data('target');
        var text = $('#' + id).text();
        if (!text) return;
        var $btn = $(this);
        navigator.clipboard.writeText(text).then(function () {
            $btn.text('Copied!');
            setTimeout(function () { $btn.text('Copy'); }, 1500);
        });
    });

    $('#btn-hash-clear').on('click', function () {
        $input.val('');
        $results.addClass('hidden');
    });
});
