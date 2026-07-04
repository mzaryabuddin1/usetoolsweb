$(function () {
    'use strict';

    var $panel = $('.pdf-server-tool');
    if (!$panel.length) return;

    var action = $panel.data('action');
    var direction = $panel.data('direction') || '';
    var outputName = $panel.data('output') || 'output.pdf';

    PdfUtils.fetchServerStatus().then(function (status) {
        var req = $panel.data('requires') || action;
        var ok = true;
        var msg = '';

        if (req === 'compress' || req === 'to-pdfa' || req === 'ocr') {
            ok = status.ghostscript;
            msg = 'Ghostscript is required on the server for this tool.';
        } else if (req === 'repair' || req === 'unlock' || req === 'protect') {
            ok = status.qpdf;
            msg = 'qpdf is required on the server for this tool.';
        } else if (req === 'convert') {
            ok = status.libreoffice;
            msg = 'LibreOffice is required on the server for this tool.';
        } else if (req === 'html-to-pdf') {
            ok = false;
            msg = 'HTML to PDF requires wkhtmltopdf on the server.';
        }

        if (!ok) {
            $panel.find('.pdf-server-note').text(msg + ' Upload will still be attempted; your host may need to install the required software.').removeClass('hidden');
        }
    }).catch(function () {});

    $panel.find('.pdf-server-input').on('change', function () {
        $panel.find('.btn-pdf-server').prop('disabled', !(this.files && this.files[0]));
        $panel.find('.pdf-server-error').addClass('hidden');
    });

    if (action === 'html-to-pdf') {
        $panel.find('.btn-pdf-server').prop('disabled', false);
    }

    $panel.find('.btn-pdf-server').on('click', async function () {
        var input = $panel.find('.pdf-server-input')[0];
        if (action !== 'html-to-pdf') {
            if (!input || !input.files || !input.files[0]) return;
        }

        PdfUtils.showStatus($panel.find('.pdf-server-status'), 'Processing on server…', 'info');
        $panel.find('.pdf-server-error').addClass('hidden');

        try {
            var fd = new FormData();
            fd.append('action', action);

            if (input && input.files && input.files[0]) {
                fd.append('file', input.files[0]);
            }

            if (direction) fd.append('direction', direction);

            $panel.find('[data-server-field]').each(function () {
                fd.append($(this).data('server-field'), $(this).val());
            });

            var result = await PdfUtils.postPdfServer(fd);

            if (result instanceof Blob) {
                PdfUtils.downloadBlob(result, outputName);
            } else if (result.text) {
                var blob = new Blob([result.text], { type: 'text/plain' });
                PdfUtils.downloadBlob(blob, 'ocr-text.txt');
            }

            PdfUtils.showStatus($panel.find('.pdf-server-status'), 'Done!', 'success');
        } catch (e) {
            $panel.find('.pdf-server-error').text(e.message).removeClass('hidden');
            PdfUtils.hideStatus($panel.find('.pdf-server-status'));
        }
    });

    $panel.find('.btn-pdf-server-clear').on('click', function () {
        $panel.find('.pdf-server-input').val('');
        $panel.find('.btn-pdf-server').prop('disabled', true);
        $panel.find('.pdf-server-status, .pdf-server-error').addClass('hidden');
    });
});
