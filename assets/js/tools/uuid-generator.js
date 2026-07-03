$(function () {
    'use strict';

    function uuidV4() {
        if (typeof crypto !== 'undefined' && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = crypto.getRandomValues(new Uint8Array(1))[0] & 15;
            var v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    function generate() {
        var count = Math.min(50, Math.max(1, parseInt($('#uuid-count').val(), 10) || 1));
        var upper = $('#uuid-uppercase').is(':checked');
        var uuids = [];

        for (var i = 0; i < count; i++) {
            var id = uuidV4();
            uuids.push(upper ? id.toUpperCase() : id);
        }

        var html = uuids.map(function (u) {
            return '<div class="uuid-line">' + u + '</div>';
        }).join('');

        $('#uuid-output').html(html);
    }

    $('#btn-generate-uuid').on('click', generate);
    generate();

    $('#btn-copy-uuid').on('click', function () {
        var lines = [];
        $('#uuid-output .uuid-line').each(function () {
            lines.push($(this).text());
        });
        if (!lines.length) return;
        navigator.clipboard.writeText(lines.join('\n'));
    });
});
