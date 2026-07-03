$(function () {
    'use strict';

    var WORDS = [
        'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
        'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore',
        'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam', 'quis', 'nostrud',
        'exercitation', 'ullamco', 'laboris', 'nisi', 'aliquip', 'ex', 'ea', 'commodo',
        'consequat', 'duis', 'aute', 'irure', 'in', 'reprehenderit', 'voluptate',
        'velit', 'esse', 'cillum', 'fugiat', 'nulla', 'pariatur', 'excepteur', 'sint',
        'occaecat', 'cupidatat', 'non', 'proident', 'sunt', 'culpa', 'qui', 'officia',
        'deserunt', 'mollit', 'anim', 'id', 'est', 'laborum'
    ];

    var START = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';

    function randomWord() {
        return WORDS[Math.floor(Math.random() * WORDS.length)];
    }

    function generateSentence() {
        var len = 8 + Math.floor(Math.random() * 12);
        var words = [];
        for (var i = 0; i < len; i++) {
            words.push(randomWord());
        }
        words[0] = words[0].charAt(0).toUpperCase() + words[0].slice(1);
        return words.join(' ') + '.';
    }

    function generateParagraph() {
        var count = 3 + Math.floor(Math.random() * 4);
        var sentences = [];
        for (var i = 0; i < count; i++) {
            sentences.push(generateSentence());
        }
        return sentences.join(' ');
    }

    function generateWords(n) {
        var words = [];
        for (var i = 0; i < n; i++) {
            words.push(randomWord());
        }
        return words.join(' ');
    }

    function generate() {
        var type = $('#lorem-type').val();
        var count = Math.min(50, Math.max(1, parseInt($('#lorem-count').val(), 10) || 1));
        var startWithClassic = $('#lorem-start').is(':checked');
        var result = [];

        if (type === 'paragraphs') {
            for (var p = 0; p < count; p++) {
                if (p === 0 && startWithClassic) {
                    result.push(START + '. ' + generateParagraph());
                } else {
                    result.push(generateParagraph());
                }
            }
            $('#lorem-output').val(result.join('\n\n'));
        } else if (type === 'sentences') {
            for (var s = 0; s < count; s++) {
                result.push(generateSentence());
            }
            if (startWithClassic && result.length) {
                result[0] = START + '.';
            }
            $('#lorem-output').val(result.join(' '));
        } else {
            var text = generateWords(count);
            if (startWithClassic) {
                text = START.split(' ').slice(0, Math.min(count, 8)).join(' ');
                if (count > 8) {
                    text += ' ' + generateWords(count - 8);
                }
            }
            $('#lorem-output').val(text);
        }
    }

    $('#btn-generate-lorem').on('click', generate);
    generate();

    $('#btn-copy-lorem').on('click', function () {
        var text = $('#lorem-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text);
    });
});
