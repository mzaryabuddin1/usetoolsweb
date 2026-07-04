$(function () {
    'use strict';

    var pdfFile = null;

    $('#pdf-pn-input').on('change', function () {
        pdfFile = (this.files && this.files[0]) || null;
        $('#btn-pdf-pn').prop('disabled', !pdfFile);
        $('#pdf-pn-error').addClass('hidden');
    });

    $('#btn-pdf-pn').on('click', async function () {
        if (!pdfFile) return;

        PdfUtils.showStatus($('#pdf-pn-status'), 'Adding page numbers…', 'info');
        $('#pdf-pn-error').addClass('hidden');

        try {
            var pdf = await PdfUtils.loadPdfDocument(pdfFile);
            var pages = pdf.getPages();
            var position = $('#pdf-pn-position').val();
            var size = parseInt($('#pdf-pn-size').val(), 10) || 12;
            var start = parseInt($('#pdf-pn-start').val(), 10) || 1;
            var font = await pdf.embedFont(PDFLib.StandardFonts.Helvetica);

            pages.forEach(function (page, i) {
                var num = String(start + i);
                var w = page.getWidth();
                var h = page.getHeight();
                var tw = font.widthOfTextAtSize(num, size);
                var coords = { x: (w - tw) / 2, y: 24, size: size, font: font };

                if (position === 'bottom-left') coords = { x: 36, y: 24, size: size, font: font };
                if (position === 'bottom-right') coords = { x: w - tw - 36, y: 24, size: size, font: font };
                if (position === 'top-center') coords = { x: (w - tw) / 2, y: h - 36, size: size, font: font };
                if (position === 'top-left') coords = { x: 36, y: h - 36, size: size, font: font };
                if (position === 'top-right') coords = { x: w - tw - 36, y: h - 36, size: size, font: font };

                page.drawText(num, coords);
            });

            PdfUtils.downloadBytes(await pdf.save(), 'numbered.pdf');
            PdfUtils.showStatus($('#pdf-pn-status'), 'Page numbers added!', 'success');
        } catch (e) {
            $('#pdf-pn-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-pn-status'));
        }
    });

    $('#btn-pdf-pn-clear').on('click', function () {
        pdfFile = null;
        $('#pdf-pn-input').val('');
        $('#btn-pdf-pn').prop('disabled', true);
        $('#pdf-pn-status, #pdf-pn-error').addClass('hidden');
    });
});
