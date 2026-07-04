$(function () {
    'use strict';

    /**
     * Generic page-selection PDF tool.
     * Config via #pdf-page-tool data attributes on body or .tool-panel
     */
    var $panel = $('.pdf-page-tool');
    if (!$panel.length) return;

    var mode = $panel.data('mode'); // remove | extract | rotate
    var pdfFile = null;
    var pageCount = 0;

    function reset() {
        pdfFile = null;
        pageCount = 0;
        $panel.find('.pdf-file-input').val('');
        $panel.find('.btn-process').prop('disabled', true);
        $panel.find('.pdf-meta').addClass('hidden');
        PdfUtils.hideStatus($panel.find('.pdf-status'));
        $panel.find('.pdf-error').addClass('hidden');
    }

    $panel.find('.pdf-file-input').on('change', async function () {
        pdfFile = (this.files && this.files[0]) || null;
        $panel.find('.btn-process').prop('disabled', !pdfFile);
        $panel.find('.pdf-error').addClass('hidden');

        if (pdfFile) {
            try {
                pageCount = await PdfUtils.getPageCount(pdfFile);
                $panel.find('.pdf-page-count').text(pageCount);
                $panel.find('.pdf-meta').removeClass('hidden');
            } catch (e) {
                $panel.find('.pdf-error').text('Could not read PDF: ' + e.message).removeClass('hidden');
            }
        } else {
            $panel.find('.pdf-meta').addClass('hidden');
        }
    });

    $panel.find('.btn-process').on('click', async function () {
        if (!pdfFile) return;

        PdfUtils.showStatus($panel.find('.pdf-status'), 'Processing…', 'info');
        $panel.find('.pdf-error').addClass('hidden');

        try {
            var src = await PdfUtils.loadPdfDocument(pdfFile);
            var spec = $panel.find('.pdf-page-spec').val();
            var allIndices = [];
            for (var i = 0; i < pageCount; i++) allIndices.push(i);

            var selected = spec.trim() === '' || $panel.find('.pdf-page-spec').is(':disabled')
                ? allIndices
                : PdfUtils.parsePageSpec(spec, pageCount);

            var indices;
            if (mode === 'remove') {
                var removeSet = {};
                selected.forEach(function (x) { removeSet[x] = true; });
                indices = allIndices.filter(function (x) { return !removeSet[x]; });
                if (indices.length === 0) throw new Error('Cannot remove all pages.');
            } else if (mode === 'extract') {
                indices = selected;
            } else if (mode === 'reorder') {
                indices = selected;
                if (indices.length !== pageCount) {
                    throw new Error('Include every page once (1-' + pageCount + '). You listed ' + indices.length + ' page(s).');
                }
                var seenReorder = {};
                indices.forEach(function (idx) {
                    if (seenReorder[idx]) throw new Error('Duplicate page in order: ' + (idx + 1));
                    seenReorder[idx] = true;
                });
            } else if (mode === 'rotate') {
                var angle = parseInt($panel.find('.pdf-rotate-angle').val(), 10);
                var pages = src.getPages();
                var rotateSet = {};
                selected.forEach(function (x) { rotateSet[x] = true; });
                pages.forEach(function (page, idx) {
                    if (rotateSet[idx] || spec.trim() === '') {
                        var current = page.getRotation().angle;
                        page.setRotation(PDFLib.degrees((current + angle) % 360));
                    }
                });
                var rotated = await src.save();
                PdfUtils.downloadBytes(rotated, 'rotated.pdf');
                PdfUtils.showStatus($panel.find('.pdf-status'), 'Done!', 'success');
                return;
            } else {
                throw new Error('Unknown mode');
            }

            var out = await PdfUtils.buildPdfFromPages(src, indices);
            var saved = await out.save();
            var names = { remove: 'removed.pdf', extract: 'extracted.pdf', reorder: 'organized.pdf' };
            PdfUtils.downloadBytes(saved, names[mode] || 'output.pdf');
            PdfUtils.showStatus($panel.find('.pdf-status'), 'Done!', 'success');
        } catch (e) {
            $panel.find('.pdf-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($panel.find('.pdf-status'));
        }
    });

    $panel.find('.btn-clear').on('click', reset);
});
