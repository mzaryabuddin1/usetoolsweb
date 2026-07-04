$(function () {
    'use strict';

    var pdfFile = null;
    var sigFile = null;

    $('#pdf-sign-pdf').on('change', function () {
        pdfFile = (this.files && this.files[0]) || null;
        updateBtn();
    });

    $('#pdf-sign-img').on('change', function () {
        sigFile = (this.files && this.files[0]) || null;
        updateBtn();
    });

    function updateBtn() {
        $('#btn-pdf-sign').prop('disabled', !(pdfFile && sigFile));
    }

    $('#btn-pdf-sign').on('click', async function () {
        if (!pdfFile || !sigFile) return;

        PdfUtils.showStatus($('#pdf-sign-status'), 'Adding signature…', 'info');
        $('#pdf-sign-error').addClass('hidden');

        try {
            var pdf = await PdfUtils.loadPdfDocument(pdfFile);
            var pageNum = parseInt($('#pdf-sign-page').val(), 10) || 1;
            var pages = pdf.getPages();
            if (pageNum < 1 || pageNum > pages.length) {
                throw new Error('Page must be between 1 and ' + pages.length);
            }

            var imgBytes = await sigFile.arrayBuffer();
            var isPng = /\.png$/i.test(sigFile.name);
            var img = isPng ? await pdf.embedPng(imgBytes) : await pdf.embedJpg(imgBytes);
            var page = pages[pageNum - 1];
            var w = parseFloat($('#pdf-sign-width').val()) || 150;

            var dims = img.scale(w / img.width);

            page.drawImage(img, {
                x: parseFloat($('#pdf-sign-x').val()) || 72,
                y: parseFloat($('#pdf-sign-y').val()) || 72,
                width: dims.width,
                height: dims.height
            });

            PdfUtils.downloadBytes(await pdf.save(), 'signed.pdf');
            PdfUtils.showStatus($('#pdf-sign-status'), 'Signature added!', 'success');
        } catch (e) {
            $('#pdf-sign-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-sign-status'));
        }
    });

    $('#btn-pdf-sign-clear').on('click', function () {
        pdfFile = null;
        sigFile = null;
        $('#pdf-sign-pdf, #pdf-sign-img').val('');
        $('#btn-pdf-sign').prop('disabled', true);
        $('#pdf-sign-status, #pdf-sign-error').addClass('hidden');
    });
});
