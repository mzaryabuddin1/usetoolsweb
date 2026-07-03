$(function () {
    'use strict';

    var $input = $('#base64-input');
    var $status = $('#base64-status');

    function showStatus(msg, type) {
        $status.removeClass('hidden alert-error alert-success alert-info')
            .addClass('alert alert-' + type).text(msg);
    }

    function encodeBase64(str) {
        var bytes = new TextEncoder().encode(str);
        var binary = '';
        bytes.forEach(function (b) { binary += String.fromCharCode(b); });
        return btoa(binary);
    }

    function decodeBase64(str) {
        var binary = atob(str.replace(/\s/g, ''));
        var bytes = new Uint8Array(binary.length);
        for (var i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return new TextDecoder().decode(bytes);
    }

    $('#btn-base64-encode').on('click', function () {
        try {
            var text = $input.val();
            if (!text) throw new Error('Input is empty.');
            $input.val(encodeBase64(text));
            showStatus('Encoded to Base64.', 'success');
        } catch (e) {
            showStatus(e.message, 'error');
        }
    });

    $('#btn-base64-decode').on('click', function () {
        try {
            var text = $.trim($input.val());
            if (!text) throw new Error('Input is empty.');
            $input.val(decodeBase64(text));
            showStatus('Decoded from Base64.', 'success');
        } catch (e) {
            showStatus('Invalid Base64 string.', 'error');
        }
    });

    $('#btn-base64-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            showStatus('Copied to clipboard.', 'info');
        });
    });

    $('#btn-base64-clear').on('click', function () {
        $input.val('');
        $status.addClass('hidden');
    });
});
