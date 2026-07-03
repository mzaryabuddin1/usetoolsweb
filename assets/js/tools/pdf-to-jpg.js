$(function () {
    'use strict';

    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    var pdfFile = null;

    $('#pdf-jpg-input').on('change', function () {
        pdfFile = this.files && this.files[0] ? this.files[0] : null;
        $('#btn-pdf-jpg-convert').prop('disabled', !pdfFile);
        $('#pdf-jpg-preview').addClass('hidden').empty();
        $('#pdf-jpg-error').addClass('hidden');
    });

    $('#btn-pdf-jpg-convert').on('click', async function () {
        if (!pdfFile) return;

        var $status = $('#pdf-jpg-status');
        var $preview = $('#pdf-jpg-preview');
        $status.text('Converting pages...').removeClass('hidden');
        $preview.addClass('hidden').empty();
        $('#pdf-jpg-error').addClass('hidden');

        try {
            var bytes = await pdfFile.arrayBuffer();
            var pdf = await pdfjsLib.getDocument({ data: bytes }).promise;
            $preview.removeClass('hidden');

            for (var i = 1; i <= pdf.numPages; i++) {
                var page = await pdf.getPage(i);
                var viewport = page.getViewport({ scale: 2 });
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                var wrap = document.createElement('div');
                wrap.style.marginBottom = '1rem';
                var label = document.createElement('p');
                label.textContent = 'Page ' + i;
                label.className = 'text-muted';
                var img = document.createElement('img');
                img.src = canvas.toDataURL('image/jpeg', 0.92);
                img.alt = 'Page ' + i;
                var dl = document.createElement('a');
                dl.href = img.src;
                dl.download = 'page-' + i + '.jpg';
                dl.className = 'btn btn-secondary btn-sm';
                dl.style.marginTop = '0.5rem';
                dl.style.display = 'inline-block';
                dl.textContent = 'Download Page ' + i;
                wrap.appendChild(label);
                wrap.appendChild(img);
                wrap.appendChild(document.createElement('br'));
                wrap.appendChild(dl);
                $preview[0].appendChild(wrap);
            }

            $status.text('Converted ' + pdf.numPages + ' page(s).').removeClass('alert-error').addClass('alert-success');
        } catch (e) {
            $('#pdf-jpg-error').text('Conversion failed: ' + e.message).removeClass('hidden');
            $status.addClass('hidden');
        }
    });

    $('#btn-pdf-jpg-clear').on('click', function () {
        pdfFile = null;
        $('#pdf-jpg-input').val('');
        $('#btn-pdf-jpg-convert').prop('disabled', true);
        $('#pdf-jpg-preview').addClass('hidden').empty();
        $('#pdf-jpg-status, #pdf-jpg-error').addClass('hidden');
    });
});
