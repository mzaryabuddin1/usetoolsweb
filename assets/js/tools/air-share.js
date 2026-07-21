(function () {
    'use strict';

    var cfg = window.AIR_SHARE_CONFIG || {};
    var $ = window.jQuery;
    var POLL_MS = cfg.pollMs || 3000;

    var desk = '';
    var deskMode = 'network';
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

    function setDeskUrl(id) {
        var url = window.location.origin + window.location.pathname + '?d=' + id;
        $('#air-desk-url').val(url);
        history.replaceState(null, '', '?d=' + id);
    }

    function setSaveState(text) {
        $('#air-save-state').text(text || '');
    }

    function setSyncState(text) {
        $('#air-desk-sync').text(text || '');
    }

    function updateModeUi(data) {
        deskMode = data.mode === 'private' ? 'private' : 'network';
        var isNetwork = deskMode === 'network';

        $('#air-network-banner').toggleClass('hidden', !isNetwork);
        $('#air-private-link-wrap').toggleClass('hidden', isNetwork);
        $('#btn-join-network').toggleClass('active', isNetwork);
        $('#btn-new-desk').toggleClass('active', !isNetwork);

        if (data.network_label) {
            $('#air-network-label').text(data.network_label);
        }
    }

    async function apiDesk(method, body, isForm) {
        var opts = { method: method };
        if (isForm) {
            opts.body = body;
        } else if (body) {
            opts.headers = { 'Content-Type': 'application/json' };
            opts.body = JSON.stringify(body);
        }
        var url = '/api/air-share-desk.php';
        if (method === 'GET') {
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
        setDeskUrl(desk);
        updateModeUi(data);
        lastUpdated = data.updated_at || 0;

        if (fromRemote && dirty) {
            setSyncState('Someone else updated this board. Reload text or save yours to overwrite.');
        } else {
            $('#air-desk-text').val(data.text || '');
            dirty = false;
            var when = data.updated_at ? new Date(data.updated_at * 1000) : null;
            if (deskMode === 'network') {
                setSyncState(when
                    ? 'Network board · last saved ' + when.toLocaleString() + ' · auto-sync every few seconds'
                    : 'Network board ready — others on your Wi‑Fi will see this when they open Air Share');
            } else {
                setSyncState(when
                    ? 'Private board · last saved ' + when.toLocaleString() + ' · expires in ' + data.expires_in_days + ' days'
                    : 'Private board — copy the link above to share outside your network');
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

    async function joinNetworkDesk() {
        hideError();
        setSyncState('Joining network board…');
        var data = await apiDesk('POST', { action: 'join-network' });
        applyDeskData(data, false);
        setSaveState('');
    }

    async function createDesk() {
        hideError();
        setSyncState('Creating private board…');
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
            var data = await apiDesk('GET');
            if ((data.updated_at || 0) > lastUpdated) {
                applyDeskData(data, true);
            } else if ((data.updated_at || 0) !== lastUpdated && !dirty) {
                $('#air-desk-text').val(data.text || '');
                renderFiles(data.files || []);
                lastUpdated = data.updated_at || 0;
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

    async function init() {
        var existing = deskFromUrl();
        if (existing.length === 8) {
            try {
                await loadDesk(existing);
                startPolling();
                return;
            } catch (e) {
                /* fall through to network board */
            }
        }
        await joinNetworkDesk();
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

        $('#btn-copy-desk-url').on('click', function () {
            var url = $('#air-desk-url').val();
            if (url) {
                navigator.clipboard.writeText(url).then(function () {
                    setSyncState('Link copied — send it to someone outside your network.');
                });
            }
        });

        $('#btn-join-network').on('click', function () {
            if (dirty && !window.confirm('You have unsaved text. Switch to the network board anyway?')) {
                return;
            }
            joinNetworkDesk();
        });

        $('#btn-new-desk').on('click', function () {
            if (dirty && !window.confirm('You have unsaved text. Create a private board anyway?')) {
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
