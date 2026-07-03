$(function () {
    'use strict';

    var $text = $('#word-text');

    function countWords(text) {
        var trimmed = $.trim(text);
        if (!trimmed) return 0;
        return trimmed.split(/\s+/).length;
    }

    function countSentences(text) {
        var trimmed = $.trim(text);
        if (!trimmed) return 0;
        var matches = trimmed.match(/[^.!?]+[.!?]+|\S+$/g);
        return matches ? matches.length : 0;
    }

    function countParagraphs(text) {
        var trimmed = $.trim(text);
        if (!trimmed) return 0;
        return trimmed.split(/\n\s*\n/).filter(function (p) {
            return $.trim(p).length > 0;
        }).length;
    }

    function readingTime(words) {
        return Math.max(1, Math.ceil(words / 200));
    }

    function updateStats() {
        var text = $text.val();
        var words = countWords(text);
        var chars = text.length;
        var charsNoSpace = text.replace(/\s/g, '').length;

        $('#stat-words').text(words);
        $('#stat-chars').text(chars);
        $('#stat-chars-no-space').text(charsNoSpace);
        $('#stat-sentences').text(countSentences(text));
        $('#stat-paragraphs').text(countParagraphs(text));
        $('#stat-reading').text(words === 0 ? '0 min' : readingTime(words) + ' min');
    }

    $text.on('input', updateStats);

    $('#btn-clear-text').on('click', function () {
        $text.val('');
        updateStats();
    });

    $('#btn-copy-text').on('click', function () {
        var text = $text.val();
        if (!text) return;

        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-copy-text');
            var original = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(original); }, 1500);
        });
    });

    updateStats();
});
