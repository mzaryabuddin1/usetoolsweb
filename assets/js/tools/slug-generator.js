$(function () {
    'use strict';

    function toSlug(str) {
        return str
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    $('#slug-title').on('input', function () {
        $('#slug-output').val(toSlug($(this).val()));
    });

    $('#btn-slug-copy').on('click', function () {
        var text = $('#slug-output').val();
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var $btn = $('#btn-slug-copy');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-slug-clear').on('click', function () {
        $('#slug-title, #slug-output').val('');
    });
});
