(function () {
    'use strict';

    var cfg = window.AIR_SHARE_CONFIG || {};
    var $ = window.jQuery;
    var POLL_MS = cfg.pollMs || 3000;

    var desk = '';
    var shareMode = 'lan';
    var lastUpdated = 0;
    var dirty = false;
    var saving = false;
    var pollTimer = null;

    function showError(msg) {
        $('#air-error').text(msg).removeClass('hidden');
    }

    function hideError() {
        $('#air-error').addClass('hidden');
    }

    function formatBytes(n) {
        if (n < 1024) return n + ' B';
        if (n < 1024 * 1024) return (n / 1024).toFixed(1) + ' KB';
        return (n / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function deskFromUrl() {
        var d = new URLSearchParams(window.location.search).get('d');
        return d ? d.toLowerCase().replace(/[^a-z0-9]/g, '') : '';
    }

    function modeFromUrl() {
        var params = new URLSearchParams(window.location.search);
        if (params.get('d')) {
            return 'link';
        }
        var mode = params.get('mode');
        return mode === 'link' ? 'link' : 'lan';
    }

    function updateUrlState() {
        if (shareMode === 'link' && desk) {
            history.replaceState(null, '', '?mode=link&d=' + desk);
        } else {
            history.replaceState(null, '', window.location.pathname + '?mode=lan');
        }
    }

    function setDeskUrl(id) {
        var url = window.location.origin + window.location.pathname + '?mode=link&d=' + id;
        $('#air-desk-url').val(url);
    }

    function setSaveState(text) {
        $('#air-save-state').text(text || '');
    }

    function setSyncState(text) {
        if (shareMode === 'lan') {
            $('#air-desk-sync').text(text || '');
        } else {
            $('#air-desk-link-sync').text(text || '');
        }
    }

    function setLanHint(text) {
        $('#air-desk-lan-hint').text(text || '');
    }

    function setMode(mode) {
        shareMode = mode === 'link' ? 'link' : 'lan';
        $('.air-mode-btn').removeClass('active').attr('aria-selected', 'false');
        $('.air-mode-btn[data-mode="' + shareMode + '"]').addClass('active').attr('aria-selected', 'true');
        $('#air-desk-bar-lan').toggleClass('hidden', shareMode !== 'lan');
        $('#air-desk-bar-link').toggleClass('hidden', shareMode !== 'link');
    }

    async function apiDesk(method, body, isForm, query) {
        var opts = { method: method };
        if (isForm) {
            opts.body = body;
        } else if (body) {
            opts.headers = { 'Content-Type': 'application/json' };
            opts.body = JSON.stringify(body);
        }
        var url = '/api/air-share-desk.php';
        if (query) {
            url += '?' + query;
        } else if (method === 'GET') {
            url += '?desk=' + encodeURIComponent(desk);
        }
        var res = await fetch(url, opts);
        var data = await res.json();
        if (!data.ok) throw new Error(data.error || 'Request failed.');
        return data;
    }

    function renderFiles(files) {
        var $list = $('#air-desk-files');
        $list.empty();
        if (!files || !files.length) {
            $list.append('<li class="hint air-desk-files-empty">No files yet.</li>');
            return;
        }
        files.forEach(function (f) {
            var li = '<li class="air-desk-file-item">' +
                '<span class="air-desk-file-name">' + escapeHtml(f.name) + '</span>' +
                '<span class="air-desk-file-size">' + formatBytes(f.size) + '</span>' +
                '<a class="btn btn-primary btn-sm" href="' + escapeAttr(f.url) + '" download>Download</a>' +
                '<button type="button" class="btn btn-secondary btn-sm air-remove-file" data-token="' + escapeAttr(f.token) + '">Remove</button>' +
                '</li>';
            $list.append(li);
        });
    }

    function applyDeskData(data, fromRemote) {
        desk = data.desk;
        lastUpdated = data.updated_at || 0;

        if (data.board_type === 'lan') {
            shareMode = 'lan';
            setMode('lan');
            if (data.scope_hint) {
                setLanHint(data.scope_hint);
            }
        } else {
            shareMode = 'link';
            setMode('link');
            setDeskUrl(desk);
        }

        updateUrlState();

        if (fromRemote && dirty) {
            setSyncState('Someone else updated this board. Reload text or save yours to overwrite.');
        } else {
            $('#air-desk-text').val(data.text || '');
            dirty = false;
            var when = data.updated_at ? new Date(data.updated_at * 1000) : null;
            if (shareMode === 'lan') {
                setSyncState(when
                    ? 'Network board · last saved ' + when.toLocaleString()
                    : 'Network board ready — others on your Wi‑Fi can open Air Share');
            } else {
                setSyncState(when
                    ? 'Last saved ' + when.toLocaleString() + ' · expires in ' + data.expires_in_days + ' days'
                    : 'Link board ready — copy the URL above');
            }
        }

        renderFiles(data.files || []);
    }

    async function loadDesk(id) {
        hideError();
        desk = id;
        var data = await apiDesk('GET');
        applyDeskData(data, false);
    }

    async function joinLanDesk() {
        hideError();
        setSyncState('Joining network board…');
        var data = await apiDesk('GET', null, false, 'mode=lan');
        applyDeskData(data, false);
        setSaveState('');
    }

    async function createDesk() {
        hideError();
        setSyncState('Creating link board…');
        var data = await apiDesk('POST', { action: 'create' });
        applyDeskData(data, false);
        setSaveState('');
    }

    async function saveText() {
        if (!desk || saving) return;
        saving = true;
        setSaveState('Saving…');
        hideError();
        try {
            var text = $('#air-desk-text').val();
            var data = await apiDesk('POST', { action: 'save', desk: desk, text: text });
            applyDeskData(data, false);
            setSaveState('Saved');
        } catch (err) {
            showError(err.message);
            setSaveState('');
        } finally {
            saving = false;
        }
    }

    async function uploadFile(file) {
        if (!desk) return;
        hideError();
        setSyncState('Uploading ' + file.name + '…');
        try {
            var fd = new FormData();
            fd.append('desk', desk);
            fd.append('file', file);
            var data = await apiDesk('POST', fd, true);
            applyDeskData(data, false);
            setSyncState('File added.');
        } catch (err) {
            showError(err.message);
        }
    }

    async function removeFile(token) {
        if (!desk) return;
        hideError();
        try {
            var data = await apiDesk('POST', { action: 'remove-file', desk: desk, token: token });
            applyDeskData(data, false);
        } catch (err) {
            showError(err.message);
        }
    }

    async function pollDesk() {
        if (!desk || dirty || saving) return;
        try {
            var data;
            if (shareMode === 'lan') {
                data = await apiDesk('GET', null, false, 'mode=lan');
            } else {
                data = await apiDesk('GET');
            }
            if ((data.updated_at || 0) > lastUpdated) {
                applyDeskData(data, true);
            }
        } catch (e) {
            /* board expired */
        }
    }

    function startPolling() {
        stopPolling();
        pollTimer = setInterval(pollDesk, POLL_MS);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function escapeAttr(s) {
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;');
    }

    async function switchMode(mode) {
        if (mode === shareMode) {
            return;
        }
        if (dirty && !window.confirm('You have unsaved text. Switch mode anyway?')) {
            return;
        }

        dirty = false;
        setSaveState('');
        hideError();
        setMode(mode);

        if (mode === 'lan') {
            await joinLanDesk();
        } else {
            var existing = deskFromUrl();
            if (existing.length === 8) {
                try {
                    await loadDesk(existing);
                } catch (e) {
                    await createDesk();
                }
            } else {
                await createDesk();
            }
        }

        startPolling();
    }

    async function init() {
        shareMode = modeFromUrl();
        setMode(shareMode);

        if (shareMode === 'link') {
            var existing = deskFromUrl();
            if (existing.length === 8) {
                try {
                    await loadDesk(existing);
                    startPolling();
                    return;
                } catch (e) {
                    /* fall through */
                }
            }
            await createDesk();
            startPolling();
            return;
        }

        await joinLanDesk();
        startPolling();
    }

    $(function () {
        init();

        $('#air-desk-text').on('input', function () {
            dirty = true;
            setSaveState('Unsaved changes');
        });

        $('#btn-save-text').on('click', saveText);

        $('#btn-clear-text').on('click', function () {
            $('#air-desk-text').val('');
            dirty = true;
            setSaveState('Unsaved changes');
        });

        $('.air-mode-btn').on('click', function () {
            switchMode($(this).data('mode'));
        });

        $('#btn-copy-desk-url').on('click', function () {
            var url = $('#air-desk-url').val();
            if (url) {
                navigator.clipboard.writeText(url).then(function () {
                    setSyncState('Link copied — send it to anyone outside your network.');
                });
            }
        });

        $('#btn-new-desk').on('click', function () {
            if (dirty && !window.confirm('You have unsaved text. Create a new link board anyway?')) {
                return;
            }
            createDesk();
        });

        $('#air-desk-drop').on('click', function () {
            $('#air-desk-file').trigger('click');
        }).on('dragover', function (e) {
            e.preventDefault();
            $(this).addClass('dragover');
        }).on('dragleave drop', function (e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            if (e.type === 'drop') {
                var files = e.originalEvent.dataTransfer.files;
                for (var i = 0; i < files.length; i++) {
                    uploadFile(files[i]);
                }
            }
        });

        $('#air-desk-file').on('change', function () {
            if (!this.files) return;
            for (var i = 0; i < this.files.length; i++) {
                uploadFile(this.files[i]);
            }
            this.value = '';
        });

        $(document).on('click', '.air-remove-file', function () {
            var token = $(this).data('token');
            if (token) removeFile(token);
        });
    });
})();
