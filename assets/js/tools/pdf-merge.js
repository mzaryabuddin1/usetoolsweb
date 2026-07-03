$(function () {
    'use strict';

    var files = [];

    function renderList() {
        var $list = $('#pdf-file-list');
        $list.empty();
        if (files.length === 0) {
            $list.addClass('hidden');
            $('#btn-pdf-merge').prop('disabled', true);
            return;
        }
        files.forEach(function (f, i) {
            $list.append('<li>' + (i + 1) + '. ' + $('<span>').text(f.name).html() + '</li>');
        });
        $list.removeClass('hidden');
        $('#btn-pdf-merge').prop('disabled', files.length < 2);
    }

    $('#pdf-merge-input').on('change', function () {
        files = Array.prototype.slice.call(this.files || []);
        renderList();
        $('#pdf-merge-error').addClass('hidden');
    });

    $('#btn-pdf-merge').on('click', async function () {
        if (files.length < 2) return;

        var $status = $('#pdf-merge-status');
        $status.text('Merging PDFs...').removeClass('hidden alert-error').addClass('alert-info');
        $('#pdf-merge-error').addClass('hidden');

        try {
            var merged = await PDFLib.PDFDocument.create();

            for (var i = 0; i < files.length; i++) {
                var bytes = await files[i].arrayBuffer();
                var pdf = await PDFLib.PDFDocument.load(bytes);
                var pages = await merged.copyPages(pdf, pdf.getPageIndices());
                pages.forEach(function (page) { merged.addPage(page); });
            }

            var mergedBytes = await merged.save();
            var blob = new Blob([mergedBytes], { type: 'application/pdf' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'merged.pdf';
            link.click();
            URL.revokeObjectURL(link.href);

            $status.text('PDF merged successfully!').removeClass('alert-error').addClass('alert-success');
        } catch (e) {
            $('#pdf-merge-error').text('Merge failed: ' + e.message).removeClass('hidden');
            $status.addClass('hidden');
        }
    });

    $('#btn-pdf-clear').on('click', function () {
        files = [];
        $('#pdf-merge-input').val('');
        renderList();
        $('#pdf-merge-status, #pdf-merge-error').addClass('hidden');
    });
});
