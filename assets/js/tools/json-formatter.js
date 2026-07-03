$(function () {
    'use strict';

    var $input = $('#json-input');
    var $status = $('#json-status');

    function showStatus(msg, type) {
        $status
            .removeClass('hidden alert-error alert-success alert-info')
            .addClass('alert alert-' + type)
            .text(msg);
    }

    function hideStatus() {
        $status.addClass('hidden');
    }

    function parseJSON() {
        var text = $.trim($input.val());
        if (!text) {
            throw new Error('JSON input is empty.');
        }
        return JSON.parse(text);
    }

    $('#btn-beautify').on('click', function () {
        try {
            var parsed = parseJSON();
            $input.val(JSON.stringify(parsed, null, 2));
            showStatus('JSON beautified successfully.', 'success');
        } catch (e) {
            showStatus('Invalid JSON: ' + e.message, 'error');
        }
    });

    $('#btn-minify').on('click', function () {
        try {
            var parsed = parseJSON();
            $input.val(JSON.stringify(parsed));
            showStatus('JSON minified successfully.', 'success');
        } catch (e) {
            showStatus('Invalid JSON: ' + e.message, 'error');
        }
    });

    $('#btn-validate').on('click', function () {
        try {
            parseJSON();
            showStatus('Valid JSON.', 'success');
        } catch (e) {
            showStatus('Invalid JSON: ' + e.message, 'error');
        }
    });

    $('#btn-copy-json').on('click', function () {
        var text = $input.val();
        if (!text) return;

        navigator.clipboard.writeText(text).then(function () {
            showStatus('Copied to clipboard.', 'info');
        });
    });

    $('#btn-clear-json').on('click', function () {
        $input.val('');
        hideStatus();
    });
});
