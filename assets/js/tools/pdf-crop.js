$(function () {
    'use strict';

    var pdfFile = null;

    $('#pdf-crop-input').on('change', function () {
        pdfFile = (this.files && this.files[0]) || null;
        $('#btn-pdf-crop').prop('disabled', !pdfFile);
        $('#pdf-crop-error').addClass('hidden');
    });

    $('#btn-pdf-crop').on('click', async function () {
        if (!pdfFile) return;

        PdfUtils.showStatus($('#pdf-crop-status'), 'Cropping…', 'info');
        $('#pdf-crop-error').addClass('hidden');

        try {
            var pdf = await PdfUtils.loadPdfDocument(pdfFile);
            var top = parseFloat($('#pdf-crop-top').val()) || 0;
            var right = parseFloat($('#pdf-crop-right').val()) || 0;
            var bottom = parseFloat($('#pdf-crop-bottom').val()) || 0;
            var left = parseFloat($('#pdf-crop-left').val()) || 0;

            pdf.getPages().forEach(function (page) {
                var w = page.getWidth();
                var h = page.getHeight();
                page.setCropBox(left, bottom, w - left - right, h - top - bottom);
            });

            PdfUtils.downloadBytes(await pdf.save(), 'cropped.pdf');
            PdfUtils.showStatus($('#pdf-crop-status'), 'PDF cropped!', 'success');
        } catch (e) {
            $('#pdf-crop-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-crop-status'));
        }
    });

    $('#btn-pdf-crop-clear').on('click', function () {
        pdfFile = null;
        $('#pdf-crop-input').val('');
        $('#btn-pdf-crop').prop('disabled', true);
        $('#pdf-crop-status, #pdf-crop-error').addClass('hidden');
    });
});
