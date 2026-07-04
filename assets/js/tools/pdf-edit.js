$(function () {
    'use strict';

    var pdfFile = null;

    $('#pdf-edit-input').on('change', function () {
        pdfFile = (this.files && this.files[0]) || null;
        $('#btn-pdf-edit').prop('disabled', !pdfFile);
        $('#pdf-edit-error').addClass('hidden');
    });

    $('#btn-pdf-edit').on('click', async function () {
        if (!pdfFile) return;

        var text = $('#pdf-edit-text').val();
        if (!text.trim()) {
            $('#pdf-edit-error').text('Enter text to add.').removeClass('hidden');
            return;
        }

        PdfUtils.showStatus($('#pdf-edit-status'), 'Adding text…', 'info');
        $('#pdf-edit-error').addClass('hidden');

        try {
            var pdf = await PdfUtils.loadPdfDocument(pdfFile);
            var pageNum = parseInt($('#pdf-edit-page').val(), 10) || 1;
            var pages = pdf.getPages();
            if (pageNum < 1 || pageNum > pages.length) {
                throw new Error('Page must be between 1 and ' + pages.length);
            }

            var page = pages[pageNum - 1];
            var font = await pdf.embedFont(PDFLib.StandardFonts.Helvetica);
            var size = parseInt($('#pdf-edit-size').val(), 10) || 14;
            var colorHex = $('#pdf-edit-color').val() || '#000000';
            var rgb = hexToRgb(colorHex);

            page.drawText(text, {
                x: parseFloat($('#pdf-edit-x').val()) || 72,
                y: parseFloat($('#pdf-edit-y').val()) || 72,
                size: size,
                font: font,
                color: PDFLib.rgb(rgb.r / 255, rgb.g / 255, rgb.b / 255)
            });

            PdfUtils.downloadBytes(await pdf.save(), 'edited.pdf');
            PdfUtils.showStatus($('#pdf-edit-status'), 'Text added!', 'success');
        } catch (e) {
            $('#pdf-edit-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-edit-status'));
        }
    });

    function hexToRgb(hex) {
        var m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return m ? { r: parseInt(m[1], 16), g: parseInt(m[2], 16), b: parseInt(m[3], 16) } : { r: 0, g: 0, b: 0 };
    }

    $('#btn-pdf-edit-clear').on('click', function () {
        pdfFile = null;
        $('#pdf-edit-input').val('');
        $('#btn-pdf-edit').prop('disabled', true);
        $('#pdf-edit-status, #pdf-edit-error').addClass('hidden');
    });
});
