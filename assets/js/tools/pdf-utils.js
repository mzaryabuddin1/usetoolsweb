/* global PDFLib, pdfjsLib, JSZip */
window.PdfUtils = (function () {
    'use strict';

    function downloadBlob(blob, filename) {
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function downloadBytes(bytes, filename, mime) {
        downloadBlob(new Blob([bytes], { type: mime || 'application/pdf' }), filename);
    }

    async function readFile(file) {
        return file.arrayBuffer();
    }

    async function loadPdfDocument(file) {
        var bytes = await readFile(file);
        return PDFLib.PDFDocument.load(bytes, { ignoreEncryption: true });
    }

    async function getPageCount(file) {
        var pdf = await loadPdfDocument(file);
        return pdf.getPageCount();
    }

    /**
     * Parse page spec like "1,3-5,8" into 0-based unique sorted indices.
     */
    function parsePageSpec(spec, pageCount) {
        if (!spec || !String(spec).trim()) {
            throw new Error('Enter page numbers (e.g. 1,3-5).');
        }

        var parts = String(spec).split(/[,;\s]+/);
        var indices = [];
        var seen = {};

        parts.forEach(function (part) {
            part = part.trim();
            if (!part) return;

            var range = part.match(/^(\d+)\s*-\s*(\d+)$/);
            if (range) {
                var start = parseInt(range[1], 10);
                var end = parseInt(range[2], 10);
                if (start > end) {
                    var tmp = start;
                    start = end;
                    end = tmp;
                }
                for (var i = start; i <= end; i++) {
                    addIndex(i - 1);
                }
                return;
            }

            var n = parseInt(part, 10);
            if (isNaN(n)) {
                throw new Error('Invalid page: ' + part);
            }
            addIndex(n - 1);
        });

        function addIndex(idx) {
            if (idx < 0 || idx >= pageCount) {
                throw new Error('Page ' + (idx + 1) + ' is out of range (1-' + pageCount + ').');
            }
            if (!seen[idx]) {
                seen[idx] = true;
                indices.push(idx);
            }
        }

        if (indices.length === 0) {
            throw new Error('No valid pages selected.');
        }

        return indices;
    }

    async function buildPdfFromPages(sourcePdf, indices) {
        var out = await PDFLib.PDFDocument.create();
        var pages = await out.copyPages(sourcePdf, indices);
        pages.forEach(function (p) { out.addPage(p); });
        return out;
    }

    function showStatus($el, message, type) {
        $el.text(message)
            .removeClass('hidden alert-info alert-success alert-error')
            .addClass(type === 'error' ? 'alert-error' : (type === 'success' ? 'alert-success' : 'alert-info'));
    }

    function hideStatus($el) {
        $el.addClass('hidden');
    }

    async function zipAndDownload(files, zipName) {
        if (typeof JSZip === 'undefined') {
            throw new Error('JSZip is required for multiple downloads.');
        }
        var zip = new JSZip();
        files.forEach(function (f) {
            zip.file(f.name, f.bytes);
        });
        var blob = await zip.generateAsync({ type: 'blob' });
        downloadBlob(blob, zipName);
    }

    async function postPdfServer(formData) {
        var res = await fetch('/api/pdf-server.php', {
            method: 'POST',
            body: formData
        });
        var ct = res.headers.get('Content-Type') || '';
        if (ct.indexOf('application/json') !== -1) {
            var data = await res.json();
            if (!data.ok) {
                throw new Error(data.error || 'Server processing failed.');
            }
            return data;
        }
        if (!res.ok) {
            throw new Error('Server error (' + res.status + ').');
        }
        return res.blob();
    }

    async function fetchServerStatus() {
        var res = await fetch('/api/pdf-server.php');
        return res.json();
    }

    return {
        downloadBlob: downloadBlob,
        downloadBytes: downloadBytes,
        readFile: readFile,
        loadPdfDocument: loadPdfDocument,
        getPageCount: getPageCount,
        parsePageSpec: parsePageSpec,
        buildPdfFromPages: buildPdfFromPages,
        showStatus: showStatus,
        hideStatus: hideStatus,
        zipAndDownload: zipAndDownload,
        postPdfServer: postPdfServer,
        fetchServerStatus: fetchServerStatus
    };
})();
