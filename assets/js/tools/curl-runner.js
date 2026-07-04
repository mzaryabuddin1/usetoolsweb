(function () {
    'use strict';

    var CurlParser = {
        tokenize: function (str) {
            var tokens = [];
            var i = 0;

            while (i < str.length) {
                if (/\s/.test(str[i])) {
                    i++;
                    continue;
                }

                if (str[i] === '\'' || str[i] === '"') {
                    var quote = str[i++];
                    var value = '';
                    while (i < str.length && str[i] !== quote) {
                        if (str[i] === '\\' && quote === '"') {
                            i++;
                            if (i < str.length) value += str[i++];
                            continue;
                        }
                        value += str[i++];
                    }
                    if (str[i] === quote) i++;
                    tokens.push(value);
                    continue;
                }

                var raw = '';
                while (i < str.length && !/\s/.test(str[i])) {
                    raw += str[i++];
                }
                tokens.push(raw);
            }

            return tokens;
        },

        normalize: function (command) {
            var cmd = command.trim();
            if (!cmd) {
                throw new Error('Paste a curl command first.');
            }

            cmd = cmd.replace(/^\s*curl\.exe\s+/i, 'curl ');
            cmd = cmd.replace(/^\s*curl\s+/i, '');

            cmd = cmd.replace(/\^\r?\n/g, ' ');
            cmd = cmd.replace(/\\\r?\n/g, ' ');

            return cmd.trim();
        },

        parse: function (command) {
            var cmd = this.normalize(command);
            var tokens = this.tokenize(cmd);
            var i = 0;
            var req = {
                method: 'GET',
                url: '',
                headers: {},
                body: null,
                formData: null,
                useGetQuery: false
            };

            function peek() {
                return tokens[i];
            }

            function next() {
                return tokens[i++];
            }

            function takeValue(flag) {
                if (peek() === undefined) {
                    throw new Error('Missing value for ' + flag);
                }
                return next();
            }

            function addHeader(line) {
                var idx = line.indexOf(':');
                if (idx === -1) {
                    throw new Error('Invalid header: ' + line);
                }
                var name = line.slice(0, idx).trim();
                var value = line.slice(idx + 1).trim();
                if (name) {
                    req.headers[name] = value;
                }
            }

            while (i < tokens.length) {
                var token = next();

                if (/^https?:\/\//i.test(token)) {
                    if (!req.url) req.url = token;
                    continue;
                }

                if (token === '-X' || token === '--request') {
                    req.method = takeValue(token).toUpperCase();
                    continue;
                }

                if (token === '-H' || token === '--header') {
                    addHeader(takeValue(token));
                    continue;
                }

                if (token === '-d' || token === '--data' || token === '--data-raw' ||
                    token === '--data-binary' || token === '--data-urlencode') {
                    var chunk = takeValue(token);
                    req.body = req.body === null ? chunk : req.body + '&' + chunk;
                    if (req.method === 'GET') req.method = 'POST';
                    continue;
                }

                if (token === '--json') {
                    var jsonBody = takeValue(token);
                    req.body = jsonBody;
                    req.headers['Content-Type'] = req.headers['Content-Type'] || 'application/json';
                    if (req.method === 'GET') req.method = 'POST';
                    continue;
                }

                if (token === '-G' || token === '--get') {
                    req.useGetQuery = true;
                    req.method = 'GET';
                    continue;
                }

                if (token === '--url') {
                    req.url = takeValue(token);
                    continue;
                }

                if (token === '-u' || token === '--user') {
                    var creds = takeValue(token);
                    req.headers['Authorization'] = 'Basic ' + btoa(creds);
                    continue;
                }

                if (token === '-A' || token === '--user-agent') {
                    req.headers['User-Agent'] = takeValue(token);
                    continue;
                }

                if (token === '-b' || token === '--cookie') {
                    req.headers['Cookie'] = takeValue(token);
                    continue;
                }

                if (token === '-F' || token === '--form') {
                    var part = takeValue(token);
                    if (!req.formData) req.formData = new FormData();
                    var eq = part.indexOf('=');
                    if (eq === -1) {
                        throw new Error('Invalid form field: ' + part);
                    }
                    var key = part.slice(0, eq);
                    var val = part.slice(eq + 1);
                    if (val.charAt(0) === '@') {
                        throw new Error('File uploads (@path) are not supported in the browser. Remove -F file fields or use a URL instead.');
                    }
                    req.formData.append(key, val);
                    if (req.method === 'GET') req.method = 'POST';
                    continue;
                }

                if (token === '-I' || token === '--head') {
                    req.method = 'HEAD';
                    continue;
                }

                if (token === '-L' || token === '--location' ||
                    token === '-k' || token === '--insecure' ||
                    token === '--compressed' || token === '-s' || token === '--silent' ||
                    token === '-v' || token === '--verbose' ||
                    token === '--http1.1' || token === '--http2' ||
                    token.startsWith('-#')) {
                    continue;
                }

                if (token.startsWith('-')) {
                    continue;
                }

                if (!req.url && /^https?:\/\//i.test(token)) {
                    req.url = token;
                }
            }

            if (req.useGetQuery && req.body) {
                var join = req.url.indexOf('?') >= 0 ? '&' : '?';
                req.url += join + req.body;
                req.body = null;
            }

            if (req.formData) {
                req.body = req.formData;
            }

            if (!req.url) {
                throw new Error('Could not find a URL in the curl command.');
            }

            if (req.body !== null && req.method === 'GET') {
                req.method = 'POST';
            }

            return req;
        }
    };

    function headersToText(headers) {
        return Object.keys(headers).map(function (k) {
            return k + ': ' + headers[k];
        }).join('\n');
    }

    function textToHeaders(text) {
        var headers = {};
        text.split(/\r?\n/).forEach(function (line) {
            line = line.trim();
            if (!line) return;
            var idx = line.indexOf(':');
            if (idx === -1) return;
            var name = line.slice(0, idx).trim();
            var value = line.slice(idx + 1).trim();
            if (name) headers[name] = value;
        });
        return headers;
    }

    function fillParsedForm(req) {
        $('#curl-method').val(req.method);
        $('#curl-url').val(req.url);
        $('#curl-headers').val(headersToText(req.headers));
        $('#curl-body').val(typeof req.body === 'string' ? req.body : '');
        updateMethodBar();
        $('#curl-parsed-section').removeClass('hidden');
    }

    function updateMethodBar() {
        var method = $('#curl-method').val() || 'GET';
        $('.curl-url-bar').attr('data-method', method);
    }

    function readParsedForm() {
        var method = $('#curl-method').val();
        var url = $('#curl-url').val().trim();
        if (!url) throw new Error('URL is required.');

        return {
            method: method,
            url: url,
            headers: textToHeaders($('#curl-headers').val()),
            body: $('#curl-body').val()
        };
    }

    function formatBytes(n) {
        if (n < 1024) return n + ' B';
        if (n < 1024 * 1024) return (n / 1024).toFixed(1) + ' KB';
        return (n / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function showError(msg) {
        $('#curl-error').text(msg).removeClass('hidden');
    }

    function hideError() {
        $('#curl-error').addClass('hidden');
    }

    function showParseStatus(msg, type) {
        var $el = $('#curl-parse-status');
        $el.text(msg).removeClass('hidden alert-info alert-success alert-error');
        $el.addClass(type === 'error' ? 'alert-error' : (type === 'success' ? 'alert-success' : 'alert-info'));
    }

    function displayResponse(meta, bodyText, elapsed, viaServer) {
        var status = meta.status;
        var statusClass = (status >= 200 && status < 300) ? 'curl-status-ok' : 'curl-status-err';

        $('#curl-res-status')
            .text(status + ' ' + (meta.statusText || ''))
            .removeClass('curl-status-ok curl-status-err')
            .addClass(statusClass);
        $('#curl-res-time').text(elapsed + ' ms' + (viaServer ? ' · via server' : ' · via browser'));
        $('#curl-res-size').text(formatBytes(new Blob([bodyText || '']).size));

        var headerLines = [];
        if (meta.headers instanceof Headers) {
            meta.headers.forEach(function (value, name) {
                headerLines.push(name + ': ' + value);
            });
        } else if (meta.headers && typeof meta.headers === 'object') {
            Object.keys(meta.headers).forEach(function (name) {
                headerLines.push(name + ': ' + meta.headers[name]);
            });
        }

        $('#curl-res-headers').text(headerLines.join('\n') || '(no headers)');
        $('#curl-res-body').val(bodyText || '');
    }

    async function runRequestViaServer(req) {
        var start = performance.now();
        var res = await fetch('/api/curl-proxy.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                method: req.method,
                url: req.url,
                headers: req.headers,
                body: req.body || ''
            })
        });

        var data = await res.json();
        var elapsed = Math.round(performance.now() - start);

        if (!data.ok) {
            throw new Error(data.error || 'Server proxy failed.');
        }

        displayResponse({
            status: data.status,
            statusText: data.statusText,
            headers: data.headers
        }, data.body, data.time_ms || elapsed, true);
        showParseStatus('Request completed via server.', 'success');
    }

    async function runRequestViaBrowser(req) {
        var fetchHeaders = new Headers();
        Object.keys(req.headers).forEach(function (name) {
            fetchHeaders.set(name, req.headers[name]);
        });

        var options = {
            method: req.method,
            headers: fetchHeaders,
            redirect: 'follow'
        };

        var bodyMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        if (bodyMethods.indexOf(req.method) !== -1 && req.body && req.body.length > 0) {
            options.body = req.body;
        }

        if (req.method === 'HEAD') {
            options.method = 'HEAD';
            delete options.body;
        }

        var start = performance.now();
        var res = await fetch(req.url, options);
        var elapsed = Math.round(performance.now() - start);
        var bodyText = req.method === 'HEAD' ? '' : await res.text();

        displayResponse({
            status: res.status,
            statusText: res.statusText,
            headers: res.headers
        }, bodyText, elapsed, false);
        showParseStatus('Request completed via browser.', 'success');
    }

    async function runRequest(req) {
        hideError();
        $('#curl-response-section').removeClass('hidden');
        $('#curl-res-status').text('Sending…').removeClass('curl-status-ok curl-status-err');
        $('#curl-res-time').text('');
        $('#curl-res-size').text('');
        $('#curl-res-headers').text('');
        $('#curl-res-body').val('');

        var mode = $('input[name="curl-mode"]:checked').val();

        try {
            if (mode === 'server') {
                await runRequestViaServer(req);
            } else {
                await runRequestViaBrowser(req);
            }
        } catch (e) {
            var msg = e.message || String(e);
            if (mode === 'browser' && /failed to fetch|networkerror|load failed/i.test(msg)) {
                showError('CORS blocked this request. Switch to “Send via server” and try again.');
            } else {
                showError(msg);
            }
            $('#curl-res-status').text('Failed').addClass('curl-status-err');
            showParseStatus('Request failed.', 'error');
        }
    }

    $(function () {
        updateMethodBar();

        $('#curl-method').on('change', updateMethodBar);

        $('#curl-input').on('input', function () {
            $('#curl-parsed-section').addClass('hidden');
        });

        $('#btn-curl-parse').on('click', function () {
            hideError();
            try {
                var req = CurlParser.parse($('#curl-input').val());
                fillParsedForm(req);
                showParseStatus('Parsed: ' + req.method + ' ' + req.url, 'success');
            } catch (e) {
                showError(e.message);
                showParseStatus('', 'error');
            }
        });

        $('#btn-curl-run').on('click', async function () {
            hideError();
            try {
                if ($('#curl-parsed-section').hasClass('hidden') || !$('#curl-url').val().trim()) {
                    fillParsedForm(CurlParser.parse($('#curl-input').val()));
                }
                await runRequest(readParsedForm());
            } catch (e) {
                showError(e.message);
            }
        });

        $('#btn-curl-clear').on('click', function () {
            $('#curl-input, #curl-url, #curl-headers, #curl-body, #curl-res-body').val('');
            $('#curl-method').val('GET');
            $('#curl-parsed-section, #curl-response-section').addClass('hidden');
            $('#curl-parse-status, #curl-error').addClass('hidden');
            $('#curl-res-status').text('—').removeClass('curl-status-ok curl-status-err');
        });

        $('#btn-curl-copy-body').on('click', function () {
            var text = $('#curl-res-body').val();
            if (!text) return;
            navigator.clipboard.writeText(text).then(function () {
                showParseStatus('Response body copied.', 'success');
            });
        });

        $('#btn-curl-format-json').on('click', function () {
            var raw = $('#curl-res-body').val();
            if (!raw.trim()) return;
            try {
                $('#curl-res-body').val(JSON.stringify(JSON.parse(raw), null, 2));
            } catch (e) {
                showError('Response is not valid JSON.');
            }
        });
    });
})();
