$(function () {
    'use strict';

    var pdfFile = null;

    function reset() {
        pdfFile = null;
        $('#pdf-split-input').val('');
        $('#btn-pdf-split').prop('disabled', true);
        $('#pdf-split-info').addClass('hidden');
        PdfUtils.hideStatus($('#pdf-split-status'));
        $('#pdf-split-error').addClass('hidden');
    }

    $('#pdf-split-input').on('change', async function () {
        pdfFile = (this.files && this.files[0]) || null;
        $('#btn-pdf-split').prop('disabled', !pdfFile);
        $('#pdf-split-error').addClass('hidden');

        if (pdfFile) {
            try {
                var count = await PdfUtils.getPageCount(pdfFile);
                $('#pdf-split-page-count').text(count);
                $('#pdf-split-info').removeClass('hidden');
            } catch (e) {
                $('#pdf-split-error').text('Could not read PDF: ' + e.message).removeClass('hidden');
            }
        } else {
            $('#pdf-split-info').addClass('hidden');
        }
    });

    $('#btn-pdf-split').on('click', async function () {
        if (!pdfFile) return;

        var mode = $('input[name="split-mode"]:checked').val();
        PdfUtils.showStatus($('#pdf-split-status'), 'Splitting PDF…', 'info');
        $('#pdf-split-error').addClass('hidden');

        try {
            var src = await PdfUtils.loadPdfDocument(pdfFile);
            var total = src.getPageCount();
            var bytes = await PdfUtils.readFile(pdfFile);

            if (mode === 'each') {
                var files = [];
                for (var i = 0; i < total; i++) {
                    var out = await PdfUtils.buildPdfFromPages(src, [i]);
                    var saved = await out.save();
                    files.push({ name: 'page-' + (i + 1) + '.pdf', bytes: saved });
                }
                await PdfUtils.zipAndDownload(files, 'split-pages.zip');
            } else {
                var spec = $('#pdf-split-ranges').val();
                var groups = String(spec).split(/[;|]+/).map(function (g) { return g.trim(); }).filter(Boolean);
                if (groups.length === 0) {
                    throw new Error('Enter page ranges separated by ; (e.g. 1-3;4-6;7).');
                }
                var zipFiles = [];
                groups.forEach(function (group, idx) {
                    // handled in async loop below
                });
                var zipItems = [];
                for (var g = 0; g < groups.length; g++) {
                    var indices = PdfUtils.parsePageSpec(groups[g], total);
                    var doc = await PdfUtils.buildPdfFromPages(src, indices);
                    zipItems.push({ name: 'part-' + (g + 1) + '.pdf', bytes: await doc.save() });
                }
                if (zipItems.length === 1) {
                    PdfUtils.downloadBytes(zipItems[0].bytes, zipItems[0].name);
                } else {
                    await PdfUtils.zipAndDownload(zipItems, 'split-parts.zip');
                }
            }

            PdfUtils.showStatus($('#pdf-split-status'), 'PDF split successfully!', 'success');
        } catch (e) {
            $('#pdf-split-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($('#pdf-split-status'));
        }
    });

    $('#btn-pdf-split-clear').on('click', reset);
});
