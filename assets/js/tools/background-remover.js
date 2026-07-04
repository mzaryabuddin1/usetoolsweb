import { removeBackground } from 'https://esm.sh/@imgly/background-removal@1.5.8';

(function () {
    'use strict';

    // Model + WASM files are hosted by IMG.LY (not bundled in the npm package)
    var BG_REMOVER_PUBLIC_PATH = 'https://staticimgly.com/@imgly/background-removal-data/1.5.8/dist/';

    var dropZone = document.getElementById('bg-drop-zone');
    var fileInput = document.getElementById('bg-file-input');
    var progressWrap = document.getElementById('bg-remover-progress');
    var progressLabel = document.getElementById('bg-progress-label');
    var progressFill = document.getElementById('bg-progress-fill');
    var resultsWrap = document.getElementById('bg-remover-results');
    var previewOriginal = document.getElementById('bg-preview-original');
    var previewResult = document.getElementById('bg-preview-result');
    var btnDownload = document.getElementById('bg-btn-download');
    var btnReset = document.getElementById('bg-btn-reset');
    var errorEl = document.getElementById('bg-remover-error');

    var originalFile = null;
    var resultBlob = null;
    var outputName = 'no-background.png';

    function showError(msg) {
        errorEl.textContent = msg;
        errorEl.classList.remove('hidden');
    }

    function hideError() {
        errorEl.classList.add('hidden');
    }

    function setProgress(label, pct) {
        progressLabel.textContent = label;
        progressFill.style.width = Math.min(100, Math.max(0, pct)) + '%';
    }

    function showProgress(show) {
        progressWrap.classList.toggle('hidden', !show);
    }

    async function processFile(file) {
        if (!file || !/^image\/(jpeg|png|webp)$/i.test(file.type)) {
            showError('Please select a JPG, PNG, or WebP image.');
            return;
        }

        hideError();
        originalFile = file;
        outputName = file.name.replace(/\.[^.]+$/, '') + '-no-bg.png';

        var objectUrl = URL.createObjectURL(file);
        previewOriginal.src = objectUrl;
        previewResult.removeAttribute('src');
        resultsWrap.classList.add('hidden');
        dropZone.classList.add('hidden');
        showProgress(true);
        setProgress('Preparing…', 5);
        btnDownload.disabled = true;

        try {
            resultBlob = await removeBackground(file, {
                publicPath: BG_REMOVER_PUBLIC_PATH,
                progress: function (key, current, total) {
                    if (!total) return;
                    var pct = Math.round((current / total) * 100);
                    var labels = {
                        'fetch:model': 'Downloading AI model…',
                        'compute:inference': 'Removing background…',
                        'wasm:load': 'Loading engine…'
                    };
                    setProgress(labels[key] || 'Processing…', pct);
                }
            });

            previewResult.src = URL.createObjectURL(resultBlob);
            resultsWrap.classList.remove('hidden');
            btnDownload.disabled = false;
        } catch (err) {
            console.error(err);
            showError(err.message || 'Background removal failed. Try a smaller image or refresh the page.');
            dropZone.classList.remove('hidden');
        } finally {
            showProgress(false);
            URL.revokeObjectURL(objectUrl);
        }
    }

    dropZone.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) {
            processFile(fileInput.files[0]);
        }
    });

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            processFile(e.dataTransfer.files[0]);
        }
    });

    btnDownload.addEventListener('click', function () {
        if (!resultBlob) return;
        var url = URL.createObjectURL(resultBlob);
        var a = document.createElement('a');
        a.href = url;
        a.download = outputName;
        a.click();
        URL.revokeObjectURL(url);
    });

    btnReset.addEventListener('click', function () {
        originalFile = null;
        resultBlob = null;
        fileInput.value = '';
        previewOriginal.removeAttribute('src');
        previewResult.removeAttribute('src');
        resultsWrap.classList.add('hidden');
        dropZone.classList.remove('hidden');
        btnDownload.disabled = true;
        hideError();
        showProgress(false);
    });
})();
