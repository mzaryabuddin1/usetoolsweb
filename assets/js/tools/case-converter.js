$(function () {
    'use strict';

    var $input = $('#case-input');

    function toTitleCase(str) {
        return str.toLowerCase().replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    function toSentenceCase(str) {
        return str.toLowerCase().replace(/(^\s*\w|[.!?]\s+\w)/g, function (c) {
            return c.toUpperCase();
        });
    }

    function toCamelCase(str) {
        return str.trim()
            .replace(/[^a-zA-Z0-9\s]/g, ' ')
            .split(/\s+/)
            .map(function (word, i) {
                if (!word) return '';
                var lower = word.toLowerCase();
                return i === 0 ? lower : lower.charAt(0).toUpperCase() + lower.slice(1);
            })
            .join('');
    }

    function toSnakeCase(str) {
        return str.trim()
            .replace(/[^a-zA-Z0-9\s]/g, ' ')
            .split(/\s+/)
            .filter(Boolean)
            .map(function (w) { return w.toLowerCase(); })
            .join('_');
    }

    function toKebabCase(str) {
        return str.trim()
            .replace(/[^a-zA-Z0-9\s]/g, ' ')
            .split(/\s+/)
            .filter(Boolean)
            .map(function (w) { return w.toLowerCase(); })
            .join('-');
    }

    $('[data-case]').on('click', function () {
        var text = $input.val();
        if (!text) return;

        var type = $(this).data('case');
        switch (type) {
            case 'upper': $input.val(text.toUpperCase()); break;
            case 'lower': $input.val(text.toLowerCase()); break;
            case 'title': $input.val(toTitleCase(text)); break;
            case 'sentence': $input.val(toSentenceCase(text)); break;
            case 'camel': $input.val(toCamelCase(text)); break;
            case 'snake': $input.val(toSnakeCase(text)); break;
            case 'kebab': $input.val(toKebabCase(text)); break;
        }
    });

    $('#btn-case-copy').on('click', function () {
        var text = $input.val();
        if (!text) return;
        navigator.clipboard.writeText(text);
    });

    $('#btn-case-clear').on('click', function () {
        $input.val('');
    });
});
