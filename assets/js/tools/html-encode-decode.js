$(function () {
    'use strict';

    var $input = $('#html-input');

    function encodeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function decodeHtml(str) {
        var el = document.createElement('textarea');
        el.innerHTML = str;
        return el.value;
    }

    $('#btn-html-encode').on('click', function () {
        $input.val(encodeHtml($input.val()));
    });

    $('#btn-html-decode').on('click', function () {
        $input.val(decodeHtml($input.val()));
    });

    $('#btn-html-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-html-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-html-clear').on('click', function () {
        $input.val('');
    });
});
