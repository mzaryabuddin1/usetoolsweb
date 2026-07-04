$(function () {
    'use strict';

    var pdfFile = null;

    $('#pdf-compress-input').on('change', function () {
        pdfFile = (this.files && this.files[0]) || null;
        $('#btn-pdf-compress-client, #btn-pdf-compress-server').prop('disabled', !pdfFile);
        $('#pdf-compress-error').addClass('hidden');
    });

    $('#btn-pdf-compress-client').on('click', async function () {
        if (!pdfFile) return;
        PdfUtils.showStatus($('#pdf-compress-status'), 'Optimizing in browser…', 'info');
        try {
            var pdf = await PdfUtils.loadPdfDocument(pdfFile);
            var saved = await pdf.save({ useObjectStreams: true });
            PdfUtils.downloadBytes(saved, 'compressed.pdf');
            PdfUtils.showStatus($('#pdf-compress-status'), 'Saved optimized PDF (browser). For stronger compression, use server mode.', 'success');
        } catch (e) {
            $('#pdf-compress-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-compress-status'));
        }
    });

    $('#btn-pdf-compress-server').on('click', async function () {
        if (!pdfFile) return;
        PdfUtils.showStatus($('#pdf-compress-status'), 'Compressing on server…', 'info');
        $('#pdf-compress-error').addClass('hidden');
        try {
            var fd = new FormData();
            fd.append('action', 'compress');
            fd.append('file', pdfFile);
            fd.append('quality', $('#pdf-compress-quality').val());
            var blob = await PdfUtils.postPdfServer(fd);
            PdfUtils.downloadBlob(blob, 'compressed.pdf');
            PdfUtils.showStatus($('#pdf-compress-status'), 'Compressed on server!', 'success');
        } catch (e) {
            $('#pdf-compress-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-compress-status'));
        }
    });

    $('#btn-pdf-compress-clear').on('click', function () {
        pdfFile = null;
        $('#pdf-compress-input').val('');
        $('#btn-pdf-compress-client, #btn-pdf-compress-server').prop('disabled', true);
        $('#pdf-compress-status, #pdf-compress-error').addClass('hidden');
    });
});
