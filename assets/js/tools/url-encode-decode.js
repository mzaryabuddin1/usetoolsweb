$(function () {
    'use strict';

    var $input = $('#url-input');
    var $status = $('#url-status');

    function showStatus(msg, type) {
        $status.removeClass('hidden alert-error alert-success alert-info')
            .addClass('alert alert-' + type).text(msg);
    }

    $('#btn-url-encode').on('click', function () {
        try {
            var text = $input.val();
            if (!text) throw new Error('Input is empty.');
            $input.val(encodeURIComponent(text));
            showStatus('URL encoded.', 'success');
        } catch (e) {
            showStatus(e.message, 'error');
        }
    });

    $('#btn-url-decode').on('click', function () {
        try {
            var text = $.trim($input.val());
            if (!text) throw new Error('Input is empty.');
            $input.val(decodeURIComponent(text.replace(/\+/g, ' ')));
            showStatus('URL decoded.', 'success');
        } catch (e) {
            showStatus('Invalid encoded URL string.', 'error');
        }
    });

    $('#btn-url-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            showStatus('Copied to clipboard.', 'info');
        });
    });

    $('#btn-url-clear').on('click', function () {
        $input.val('');
        $status.addClass('hidden');
    });
});
