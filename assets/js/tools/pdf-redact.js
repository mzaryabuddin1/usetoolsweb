$(function () {
    'use strict';

    var pdfFile = null;

    $('#pdf-redact-input').on('change', function () {
        pdfFile = (this.files && this.files[0]) || null;
        $('#btn-pdf-redact').prop('disabled', !pdfFile);
        $('#pdf-redact-error').addClass('hidden');
    });

    $('#btn-pdf-redact').on('click', async function () {
        if (!pdfFile) return;

        var lines = $('#pdf-redact-areas').val().trim().split(/\n+/).filter(Boolean);
        if (lines.length === 0) {
            $('#pdf-redact-error').text('Add at least one redaction area.').removeClass('hidden');
            return;
        }

        PdfUtils.showStatus($('#pdf-redact-status'), 'Redacting…', 'info');
        $('#pdf-redact-error').addClass('hidden');

        try {
            var pdf = await PdfUtils.loadPdfDocument(pdfFile);
            var pages = pdf.getPages();

            lines.forEach(function (line) {
                var p = line.split(',').map(function (v) { return parseFloat(v.trim()); });
                if (p.length < 5) throw new Error('Invalid line: ' + line + ' — use page,x,y,width,height');
                var pageIdx = Math.floor(p[0]) - 1;
                if (pageIdx < 0 || pageIdx >= pages.length) throw new Error('Invalid page: ' + p[0]);
                pages[pageIdx].drawRectangle({
                    x: p[1], y: p[2], width: p[3], height: p[4],
                    color: PDFLib.rgb(0, 0, 0),
                    borderWidth: 0
                });
            });

            PdfUtils.downloadBytes(await pdf.save(), 'redacted.pdf');
            PdfUtils.showStatus($('#pdf-redact-status'), 'Redaction complete!', 'success');
        } catch (e) {
            $('#pdf-redact-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-redact-status'));
        }
    });

    $('#btn-pdf-redact-clear').on('click', function () {
        pdfFile = null;
        $('#pdf-redact-input').val('');
        $('#btn-pdf-redact').prop('disabled', true);
        $('#pdf-redact-status, #pdf-redact-error').addClass('hidden');
    });
});
