$(function () {
    'use strict';

    var morseMap = {
        'A': '.-', 'B': '-...', 'C': '-.-.', 'D': '-..', 'E': '.', 'F': '..-.',
        'G': '--.', 'H': '....', 'I': '..', 'J': '.---', 'K': '-.-', 'L': '.-..',
        'M': '--', 'N': '-.', 'O': '---', 'P': '.--.', 'Q': '--.-', 'R': '.-.',
        'S': '...', 'T': '-', 'U': '..-', 'V': '...-', 'W': '.--', 'X': '-..-',
        'Y': '-.--', 'Z': '--..', '0': '-----', '1': '.----', '2': '..---',
        '3': '...--', '4': '....-', '5': '.....', '6': '-....', '7': '--...',
        '8': '---..', '9': '----.', '.': '.-.-.-', ',': '--..--', '?': '..--..',
        '/': '-..-.', '@': '.--.-.'
    };

    var reverseMap = {};
    Object.keys(morseMap).forEach(function (k) {
        reverseMap[morseMap[k]] = k;
    });

    var $input = $('#morse-input');

    $('#btn-morse-encode').on('click', function () {
        var text = $input.val().toUpperCase();
        var words = text.split(/\s+/);
        var result = words.map(function (word) {
            return word.split('').map(function (ch) {
                return morseMap[ch] || (ch === ' ' ? '' : ch);
            }).filter(Boolean).join(' ');
        }).join(' / ');
        $input.val(result);
    });

    $('#btn-morse-decode').on('click', function () {
        var text = $input.val().trim();
        var words = text.split(/\s*\/\s*|\s{3,}/);
        var result = words.map(function (word) {
            return word.trim().split(/\s+/).map(function (code) {
                return reverseMap[code] || '?';
            }).join('');
        }).join(' ');
        $input.val(result);
    });

    $('#btn-morse-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-morse-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-morse-clear').on('click', function () {
        $input.val('');
    });
});
