(function () {
    'use strict';

    var $ = window.jQuery;

    function postmanUrlToPath(url) {
        if (!url || typeof url !== 'object') return '/';
        var path = url.path || '/';
        if (Array.isArray(url.path)) path = '/' + url.path.join('/');
        if (url.query && url.query.length) {
            path += '?' + url.query.map(function (q) { return encodeURIComponent(q.key) + '=' + encodeURIComponent(q.value || ''); }).join('&');
        }
        return path || '/';
    }

    function convertPostmanToOpenApi(collection) {
        if (!collection || !collection.info) {
            throw new Error('Invalid Postman collection — missing info block.');
        }

        var paths = {};
        var items = collection.item || [];

        function walk(items) {
            items.forEach(function (item) {
                if (item.item) {
                    walk(item.item);
                    return;
                }
                var req = item.request;
                if (!req) return;
                var method = (req.method || 'GET').toLowerCase();
                var path = postmanUrlToPath(req.url);
                paths[path] = paths[path] || {};
                paths[path][method] = {
                    summary: item.name || method.toUpperCase() + ' ' + path,
                    operationId: (item.name || 'op').replace(/\W+/g, '_').toLowerCase(),
                    responses: { '200': { description: 'Successful response' } }
                };
                if (req.body && req.body.raw) {
                    paths[path][method].requestBody = {
                        content: { 'application/json': { schema: { type: 'object' } } }
                    };
                }
            });
        }

        walk(items);

        return {
            openapi: '3.0.3',
            info: {
                title: collection.info.name || 'API',
                description: collection.info.description || '',
                version: '1.0.0'
            },
            servers: [{ url: '/' }],
            paths: paths
        };
    }

    function showError(msg) {
        $('#postman-error').text(msg).removeClass('hidden');
    }

    $(function () {
        $('#btn-postman-convert').on('click', function () {
            $('#postman-error').addClass('hidden');
            try {
                var input = JSON.parse($('#postman-input').val());
                var out = convertPostmanToOpenApi(input);
                $('#postman-output').val(JSON.stringify(out, null, 2));
            } catch (e) {
                showError(e.message || 'Could not parse Postman JSON.');
            }
        });

        $('#btn-postman-copy').on('click', function () {
            var text = $('#postman-output').val();
            if (text) navigator.clipboard.writeText(text);
        });

        $('#btn-postman-download').on('click', function () {
            var text = $('#postman-output').val();
            if (!text) return;
            var blob = new Blob([text], { type: 'application/json' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'openapi.json';
            a.click();
        });

        $('#btn-postman-clear').on('click', function () {
            $('#postman-input, #postman-output').val('');
            $('#postman-error').addClass('hidden');
        });
    });
})();
