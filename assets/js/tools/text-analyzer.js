$(function () {
    'use strict';

    var $text = $('#analyzer-text');

    function countWords(text) {
        var trimmed = $.trim(text);
        if (!trimmed) return 0;
        return trimmed.split(/\s+/).length;
    }

    function getWords(text) {
        var trimmed = $.trim(text);
        if (!trimmed) return [];
        return trimmed.toLowerCase().match(/\b[\w']+\b/g) || [];
    }

    function updateStats() {
        var text = $text.val();
        var words = getWords(text);
        var wordCount = countWords(text);
        var lines = text.length === 0 ? 0 : text.split(/\r\n|\r|\n/).length;
        var bytes = new TextEncoder().encode(text).length;
        var unique = new Set(words).size;
        var avgLen = wordCount > 0
            ? (words.reduce(function (s, w) { return s + w.length; }, 0) / wordCount).toFixed(1)
            : '0';
        var longest = words.length
            ? words.reduce(function (a, b) { return a.length >= b.length ? a : b; })
            : '—';
        var reading = wordCount === 0 ? '0 min' : Math.max(1, Math.ceil(wordCount / 200)) + ' min';

        $('#an-words').text(wordCount);
        $('#an-chars').text(text.length);
        $('#an-lines').text(lines);
        $('#an-bytes').text(bytes);
        $('#an-unique').text(unique);
        $('#an-avg-len').text(avgLen);
        $('#an-longest').text(longest.length > 12 ? longest.slice(0, 12) + '…' : longest);
        $('#an-reading').text(reading);
    }

    $text.on('input', updateStats);
    $('#btn-an-clear').on('click', function () {
        $text.val('');
        updateStats();
    });
    updateStats();
});
