$(function () {
    'use strict';

    function decodeBase64Url(str) {
        var base64 = str.replace(/-/g, '+').replace(/_/g, '/');
        while (base64.length % 4) base64 += '=';
        return decodeURIComponent(Array.prototype.map.call(atob(base64), function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    $('#btn-jwt-decode').on('click', function () {
        var token = $.trim($('#jwt-input').val());
        if (!token) {
            $('#jwt-error').text('Paste a JWT token.').removeClass('hidden');
            $('#jwt-results').addClass('hidden');
            return;
        }

        var parts = token.split('.');
        if (parts.length < 2) {
            $('#jwt-error').text('Invalid JWT format. Expected header.payload[.signature].').removeClass('hidden');
            $('#jwt-results').addClass('hidden');
            return;
        }

        try {
            var header = JSON.parse(decodeBase64Url(parts[0]));
            var payload = JSON.parse(decodeBase64Url(parts[1]));
            $('#jwt-header').val(JSON.stringify(header, null, 2));
            $('#jwt-payload').val(JSON.stringify(payload, null, 2));
            $('#jwt-error').addClass('hidden');
            $('#jwt-results').removeClass('hidden');
        } catch (e) {
            $('#jwt-error').text('Failed to decode JWT: ' + e.message).removeClass('hidden');
            $('#jwt-results').addClass('hidden');
        }
    });

    $('#btn-jwt-clear').on('click', function () {
        $('#jwt-input, #jwt-header, #jwt-payload').val('');
        $('#jwt-error').addClass('hidden');
        $('#jwt-results').addClass('hidden');
    });
});
