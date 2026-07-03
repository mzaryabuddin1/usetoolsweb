$(function () {
    'use strict';

    var $input = $('#md-input');
    var $preview = $('#md-preview');

    function render() {
        var md = $input.val();
        if (!md) {
            $preview.html('<p class="text-muted">Preview will appear here...</p>');
            return;
        }
        $preview.html(marked.parse(md));
    }

    $input.on('input', render);

    $('#btn-md-copy-html').on('click', function () {
        var html = $preview.html();
        if (!html || $input.val() === '') return;
        navigator.clipboard.writeText(html).then(function () {
            var $btn = $('#btn-md-copy-html');
            var orig = $btn.text();
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1500);
        });
    });

    $('#btn-md-clear').on('click', function () {
        $input.val('');
        render();
    });

    render();
});
