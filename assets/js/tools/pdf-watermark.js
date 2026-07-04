$(function () {
    'use strict';

    var pdfFile = null;

    $('#pdf-wm-input').on('change', function () {
        pdfFile = (this.files && this.files[0]) || null;
        $('#btn-pdf-wm').prop('disabled', !pdfFile);
        $('#pdf-wm-error').addClass('hidden');
    });

    $('#btn-pdf-wm').on('click', async function () {
        if (!pdfFile) return;

        var text = $('#pdf-wm-text').val().trim();
        if (!text) {
            $('#pdf-wm-error').text('Enter watermark text.').removeClass('hidden');
            return;
        }

        PdfUtils.showStatus($('#pdf-wm-status'), 'Adding watermark…', 'info');
        $('#pdf-wm-error').addClass('hidden');

        try {
            var pdf = await PdfUtils.loadPdfDocument(pdfFile);
            var font = await pdf.embedFont(PDFLib.StandardFonts.HelveticaBold);
            var opacity = parseFloat($('#pdf-wm-opacity').val()) || 0.3;
            var size = parseInt($('#pdf-wm-size').val(), 10) || 48;

            pdf.getPages().forEach(function (page) {
                var w = page.getWidth();
                var h = page.getHeight();
                var tw = font.widthOfTextAtSize(text, size);
                page.drawText(text, {
                    x: (w - tw) / 2,
                    y: h / 2,
                    size: size,
                    font: font,
                    opacity: opacity,
                    rotate: PDFLib.degrees(-45)
                });
            });

            PdfUtils.downloadBytes(await pdf.save(), 'watermarked.pdf');
            PdfUtils.showStatus($('#pdf-wm-status'), 'Watermark added!', 'success');
        } catch (e) {
            $('#pdf-wm-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-wm-status'));
        }
    });

    $('#btn-pdf-wm-clear').on('click', function () {
        pdfFile = null;
        $('#pdf-wm-input').val('');
        $('#btn-pdf-wm').prop('disabled', true);
        $('#pdf-wm-status, #pdf-wm-error').addClass('hidden');
    });
});
