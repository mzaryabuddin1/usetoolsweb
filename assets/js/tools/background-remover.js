(function () {
    'use strict';

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

    var resultBlob = null;
    var outputName = 'no-background.png';
    var busy = false;

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
        if (busy) return;
        if (!file || !/^image\/(jpeg|png|webp)$/i.test(file.type)) {
            showError('Please select a JPG, PNG, or WebP image.');
            return;
        }

        hideError();
        outputName = file.name.replace(/\.[^.]+$/, '') + '-no-bg.png';

        var objectUrl = URL.createObjectURL(file);
        previewOriginal.src = objectUrl;
        previewResult.removeAttribute('src');
        resultsWrap.classList.add('hidden');
        dropZone.classList.add('hidden');
        showProgress(true);
        setProgress('Uploading and removing background…', 15);
        btnDownload.disabled = true;
        busy = true;

        try {
            var fd = new FormData();
            fd.append('image', file);

            setProgress('Removing background on server…', 45);

            var res = await fetch('/api/bg-remove.php', { method: 'POST', body: fd });

            if (!res.ok) {
                var errData = null;
                try {
                    errData = await res.json();
                } catch (e) { /* not json */ }
                throw new Error((errData && errData.error) || ('Server error (' + res.status + ')'));
            }

            setProgress('Almost done…', 90);
            resultBlob = await res.blob();
            if (!resultBlob || resultBlob.size === 0) {
                throw new Error('Empty response from server.');
            }

            previewResult.src = URL.createObjectURL(resultBlob);
            resultsWrap.classList.remove('hidden');
            btnDownload.disabled = false;
            setProgress('Done', 100);
        } catch (err) {
            console.error(err);
            showError(err.message || 'Background removal failed.');
            dropZone.classList.remove('hidden');
        } finally {
            busy = false;
            showProgress(false);
            URL.revokeObjectURL(objectUrl);
        }
    }

    dropZone.addEventListener('click', function () {
        if (!busy) fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) {
            processFile(fileInput.files[0]);
        }
    });

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        if (!busy) dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (!busy && e.dataTransfer.files && e.dataTransfer.files[0]) {
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
