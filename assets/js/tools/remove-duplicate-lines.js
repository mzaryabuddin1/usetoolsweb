$(function () {
    'use strict';

    var $input = $('#dedupe-input');

    $('#btn-dedupe').on('click', function () {
        var text = $input.val();
        var lines = text.split('\n');
        var before = lines.length;
        var seen = {};
        var unique = [];

        lines.forEach(function (line) {
            if (!seen.hasOwnProperty(line)) {
                seen[line] = true;
                unique.push(line);
            }
        });

        $input.val(unique.join('\n'));
        $('#dedupe-before').text(before);
        $('#dedupe-after').text(unique.length);
        $('#dedupe-removed').text(before - unique.length);
        $('#dedupe-stats').removeClass('hidden');
    });

    $('#btn-dedupe-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-dedupe-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-dedupe-clear').on('click', function () {
        $input.val('');
        $('#dedupe-stats').addClass('hidden');
    });
});
