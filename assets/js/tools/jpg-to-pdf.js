$(function () {
    'use strict';

    var images = [];

    function renderList() {
        var $list = $('#jpg-pdf-list');
        $list.empty();
        if (images.length === 0) {
            $list.addClass('hidden');
            $('#btn-jpg-pdf').prop('disabled', true);
            return;
        }
        images.forEach(function (f, i) {
            $list.append('<li>' + (i + 1) + '. ' + $('<span>').text(f.name).html() + '</li>');
        });
        $list.removeClass('hidden');
        $('#btn-jpg-pdf').prop('disabled', false);
    }

    $('#jpg-pdf-input').on('change', function () {
        images = Array.prototype.slice.call(this.files || []);
        renderList();
        $('#jpg-pdf-error').addClass('hidden');
    });

    $('#btn-jpg-pdf').on('click', async function () {
        if (images.length === 0) return;

        PdfUtils.showStatus($('#jpg-pdf-status'), 'Creating PDF…', 'info');
        $('#jpg-pdf-error').addClass('hidden');

        try {
            var pdf = await PDFLib.PDFDocument.create();
            var pageSizeEl = $('input[name="jpg-page-size"]:checked');
            var pageSize = pageSizeEl.length ? pageSizeEl.val() : ($('input[name="jpg-page-size"]').val() || 'fit');
            var margin = parseInt($('#jpg-pdf-margin').val(), 10) || 0;
            var orientEl = $('input[name="jpg-orientation"]:checked');
            var orientation = orientEl.length ? orientEl.val() : ($('input[name="jpg-orientation"]').val() || 'portrait');

            for (var i = 0; i < images.length; i++) {
                var bytes = await images[i].arrayBuffer();
                var isPng = /\.png$/i.test(images[i].name) || images[i].type === 'image/png';
                var embedded = isPng
                    ? await pdf.embedPng(bytes)
                    : await pdf.embedJpg(bytes);

                var dims = embedded.scale(1);
                var width = dims.width;
                var height = dims.height;

                if (pageSize === 'a4') {
                    width = 595.28;
                    height = 841.89;
                } else if (pageSize === 'letter') {
                    width = 612;
                    height = 792;
                }

                if (orientation === 'landscape' && width < height) {
                    var t = width; width = height; height = t;
                }

                var page = pdf.addPage([width, height]);
                var maxW = width - margin * 2;
                var maxH = height - margin * 2;
                var scale = Math.min(maxW / dims.width, maxH / dims.height, 1);
                var drawW = dims.width * scale;
                var drawH = dims.height * scale;
                var x = (width - drawW) / 2;
                var y = (height - drawH) / 2;

                page.drawImage(embedded, { x: x, y: y, width: drawW, height: drawH });
            }

            var out = await pdf.save();
            PdfUtils.downloadBytes(out, 'images.pdf');
            PdfUtils.showStatus($('#jpg-pdf-status'), 'PDF created!', 'success');
        } catch (e) {
            $('#jpg-pdf-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#jpg-pdf-status'));
        }
    });

    $('#btn-jpg-pdf-clear').on('click', function () {
        images = [];
        $('#jpg-pdf-input').val('');
        renderList();
        $('#jpg-pdf-status, #jpg-pdf-error').addClass('hidden');
    });
});
