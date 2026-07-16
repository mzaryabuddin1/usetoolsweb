(function () {
    'use strict';

    var dropZone = document.getElementById('ocr-drop-zone');
    var fileInput = document.getElementById('ocr-file-input');
    var progressWrap = document.getElementById('ocr-progress');
    var progressLabel = document.getElementById('ocr-progress-label');
    var progressFill = document.getElementById('ocr-progress-fill');
    var resultsWrap = document.getElementById('ocr-results');
    var previewImg = document.getElementById('ocr-preview');
    var outputEl = document.getElementById('ocr-output');
    var errorEl = document.getElementById('ocr-error');
    var btnCopy = document.getElementById('btn-ocr-copy');
    var btnDownload = document.getElementById('btn-ocr-download');
    var btnReset = document.getElementById('btn-ocr-reset');
    var langSelect = document.getElementById('ocr-lang');

    var busy = false;
    var previewUrl = null;
    var lastText = '';

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

    function revokePreviewUrl() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }
    }

    function setOutput(text) {
        lastText = text;
        outputEl.value = text;
        var hasText = text.length > 0;
        btnCopy.disabled = !hasText;
        btnDownload.disabled = !hasText;
    }

    function resetView() {
        revokePreviewUrl();
        fileInput.value = '';
        previewImg.removeAttribute('src');
        setOutput('');
        resultsWrap.classList.add('hidden');
        dropZone.classList.remove('hidden');
        showProgress(false);
        hideError();
    }

    function isImageFile(file) {
        return file && file.type && file.type.indexOf('image') === 0;
    }

    function formatStatus(status) {
        return String(status || 'working').replace(/_/g, ' ');
    }

    function prepareImageBlob(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var img = new Image();

            img.onload = function () {
                URL.revokeObjectURL(url);

                var maxSide = 2400;
                var width = img.naturalWidth || img.width;
                var height = img.naturalHeight || img.height;
                var scale = Math.min(1, maxSide / Math.max(width, height));
                var targetW = Math.max(1, Math.round(width * scale));
                var targetH = Math.max(1, Math.round(height * scale));

                var canvas = document.createElement('canvas');
                canvas.width = targetW;
                canvas.height = targetH;
                canvas.getContext('2d').drawImage(img, 0, 0, targetW, targetH);

                canvas.toBlob(function (blob) {
                    resolve(blob || file);
                }, 'image/png');
            };

            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Could not load the image.'));
            };

            img.src = url;
        });
    }

    async function runOcr(file) {
        if (busy) {
            return;
        }
        if (!isImageFile(file)) {
            showError('Please upload or paste an image file (JPG, PNG, WebP, GIF, or BMP).');
            return;
        }
        if (typeof Tesseract === 'undefined') {
            showError('OCR library failed to load. Refresh the page and try again.');
            return;
        }

        hideError();
        busy = true;
        dropZone.classList.add('hidden');
        resultsWrap.classList.add('hidden');
        showProgress(true);
        setProgress('Preparing image…', 8);

        revokePreviewUrl();
        previewUrl = URL.createObjectURL(file);
        previewImg.src = previewUrl;

        try {
            var imageBlob = await prepareImageBlob(file);
            var lang = langSelect.value || 'eng';

            setProgress('Loading OCR engine…', 15);

            var result = await Tesseract.recognize(imageBlob, lang, {
                logger: function (m) {
                    if (!m) {
                        return;
                    }
                    if (m.status === 'recognizing text' && typeof m.progress === 'number') {
                        setProgress('Recognizing text…', 20 + Math.round(m.progress * 70));
                        return;
                    }
                    if (m.status) {
                        var pct = 15;
                        if (m.status === 'loading tesseract core') pct = 20;
                        if (m.status === 'initializing tesseract') pct = 30;
                        if (m.status === 'loading language traineddata') pct = 40;
                        setProgress(formatStatus(m.status) + '…', pct);
                    }
                }
            });

            var text = (result && result.data && result.data.text) ? result.data.text.trim() : '';
            setOutput(text);
            resultsWrap.classList.remove('hidden');
            showProgress(false);

            if (!text) {
                showError('No text was found in this image. Try a clearer photo or another language.');
            }
        } catch (err) {
            showProgress(false);
            dropZone.classList.remove('hidden');
            showError(err.message || 'OCR failed. Try another image.');
        } finally {
            busy = false;
        }
    }

    function handleFile(file) {
        if (file) {
            runOcr(file);
        }
    }

    dropZone.addEventListener('click', function () {
        if (!busy) {
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            handleFile(this.files[0]);
        }
    });

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        if (!busy) {
            dropZone.classList.add('dragover');
        }
    });

    dropZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    document.addEventListener('paste', function (e) {
        var items = e.clipboardData && e.clipboardData.items;
        if (!items) {
            return;
        }

        for (var i = 0; i < items.length; i++) {
            if (items[i].type && items[i].type.indexOf('image') === 0) {
                e.preventDefault();
                var blob = items[i].getAsFile();
                if (blob) {
                    handleFile(blob);
                }
                return;
            }
        }
    });

    btnCopy.addEventListener('click', function () {
        if (!lastText) {
            return;
        }
        navigator.clipboard.writeText(lastText).then(function () {
            var orig = btnCopy.textContent;
            btnCopy.textContent = 'Copied!';
            setTimeout(function () {
                btnCopy.textContent = orig;
            }, 1500);
        });
    });

    btnDownload.addEventListener('click', function () {
        if (!lastText) {
            return;
        }
        var blob = new Blob([lastText], { type: 'text/plain;charset=utf-8' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'extracted-text.txt';
        link.click();
        URL.revokeObjectURL(link.href);
    });

    btnReset.addEventListener('click', resetView);

    window.addEventListener('beforeunload', revokePreviewUrl);
})();
