$(function () {
    'use strict';

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    $('#btn-diff').on('click', function () {
        var original = $('#diff-original').val().split('\n');
        var modified = $('#diff-modified').val().split('\n');
        var maxLen = Math.max(original.length, modified.length);
        var html = '';

        for (var i = 0; i < maxLen; i++) {
            var a = original[i];
            var b = modified[i];
            if (a === b) {
                if (a !== undefined) {
                    html += '<div class="diff-line">' + escapeHtml(a) + '</div>';
                }
            } else {
                if (a !== undefined) {
                    html += '<div class="diff-line diff-removed">- ' + escapeHtml(a) + '</div>';
                }
                if (b !== undefined) {
                    html += '<div class="diff-line diff-added">+ ' + escapeHtml(b) + '</div>';
                }
            }
        }

        $('#diff-output').html(html || '<div class="text-muted">No differences found.</div>');
        $('#diff-output-wrap').removeClass('hidden');
    });

    $('#btn-diff-clear').on('click', function () {
        $('#diff-original, #diff-modified').val('');
        $('#diff-output').empty();
        $('#diff-output-wrap').addClass('hidden');
    });
});
