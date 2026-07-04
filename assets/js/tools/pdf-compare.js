$(function () {
    'use strict';

    var fileA = null;
    var fileB = null;

    function updateBtn() {
        $('#btn-pdf-compare').prop('disabled', !(fileA && fileB));
    }

    $('#pdf-compare-a, #pdf-compare-b').on('change', function () {
        if (this.id === 'pdf-compare-a') fileA = (this.files && this.files[0]) || null;
        else fileB = (this.files && this.files[0]) || null;
        updateBtn();
        $('#pdf-compare-error').addClass('hidden');
    });

    async function renderPdf(file, canvas) {
        var bytes = await file.arrayBuffer();
        var task = pdfjsLib.getDocument({ data: bytes }).promise;
        var pdf = await task;
        var page = await pdf.getPage(1);
        var viewport = page.getViewport({ scale: 1.2 });
        var ctx = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        return pdf.numPages;
    }

    $('#btn-pdf-compare').on('click', async function () {
        if (!fileA || !fileB) return;

        PdfUtils.showStatus($('#pdf-compare-status'), 'Comparing first pages…', 'info');
        $('#pdf-compare-error').addClass('hidden');
        $('#pdf-compare-result').removeClass('hidden');

        try {
            var canvasA = document.getElementById('pdf-compare-canvas-a');
            var canvasB = document.getElementById('pdf-compare-canvas-b');
            var pagesA = await renderPdf(fileA, canvasA);
            var pagesB = await renderPdf(fileB, canvasB);

            var summary = 'File A: ' + pagesA + ' page(s), File B: ' + pagesB + ' page(s).';
            if (pagesA !== pagesB) summary += ' Page counts differ.';
            else summary += ' Showing page 1 side by side — review visually for differences.';
            $('#pdf-compare-summary').text(summary);

            PdfUtils.showStatus($('#pdf-compare-status'), 'Comparison ready.', 'success');
        } catch (e) {
            $('#pdf-compare-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-compare-status'));
        }
    });

    $('#btn-pdf-compare-clear').on('click', function () {
        fileA = fileB = null;
        $('#pdf-compare-a, #pdf-compare-b').val('');
        updateBtn();
        $('#pdf-compare-result').addClass('hidden');
        $('#pdf-compare-status, #pdf-compare-error').addClass('hidden');
    });
});
