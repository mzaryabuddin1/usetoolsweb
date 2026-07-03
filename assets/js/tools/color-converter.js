$(function () {
    'use strict';

    var $hex = $('#color-hex');
    var $rgb = $('#color-rgb');
    var $hsl = $('#color-hsl');
    var $picker = $('#color-picker');
    var $preview = $('#color-preview');
    var $error = $('#color-error');
    var updating = false;

    function clamp(n, min, max) {
        return Math.min(max, Math.max(min, n));
    }

    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex.split('').map(function (c) { return c + c; }).join('');
        }
        if (!/^[0-9a-fA-F]{6}$/.test(hex)) return null;
        return {
            r: parseInt(hex.slice(0, 2), 16),
            g: parseInt(hex.slice(2, 4), 16),
            b: parseInt(hex.slice(4, 6), 16)
        };
    }

    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(function (n) {
            return clamp(Math.round(n), 0, 255).toString(16).padStart(2, '0');
        }).join('');
    }

    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0;
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                default: h = ((r - g) / d + 4) / 6;
            }
        }
        return {
            h: Math.round(h * 360),
            s: Math.round(s * 100),
            l: Math.round(l * 100)
        };
    }

    function parseRgb(str) {
        var m = str.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
        if (!m) return null;
        return { r: +m[1], g: +m[2], b: +m[3] };
    }

    function parseHsl(str) {
        var m = str.match(/hsla?\(\s*(\d+)\s*,\s*(\d+)%?\s*,\s*(\d+)%?/i);
        if (!m) return null;
        var h = +m[1] / 360, s = +m[2] / 100, l = +m[3] / 100;
        var r, g, b;
        if (s === 0) {
            r = g = b = l;
        } else {
            var hue2rgb = function (p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1 / 6) return p + (q - p) * 6 * t;
                if (t < 1 / 2) return q;
                if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                return p;
            };
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1 / 3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1 / 3);
        }
        return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
    }

    function applyColor(r, g, b) {
        updating = true;
        var hex = rgbToHex(r, g, b);
        var hsl = rgbToHsl(r, g, b);
        $hex.val(hex);
        $rgb.val('rgb(' + r + ', ' + g + ', ' + b + ')');
        $hsl.val('hsl(' + hsl.h + ', ' + hsl.s + '%, ' + hsl.l + '%)');
        $picker.val(hex);
        $preview.css('background-color', hex);
        $error.addClass('hidden');
        updating = false;
    }

    $hex.on('input', function () {
        if (updating) return;
        var rgb = hexToRgb($.trim($(this).val()));
        if (!rgb) {
            $error.text('Invalid HEX color.').removeClass('hidden');
            return;
        }
        applyColor(rgb.r, rgb.g, rgb.b);
    });

    $rgb.on('change', function () {
        if (updating) return;
        var rgb = parseRgb($(this).val());
        if (!rgb) {
            $error.text('Invalid RGB format. Use rgb(255, 0, 0)').removeClass('hidden');
            return;
        }
        applyColor(rgb.r, rgb.g, rgb.b);
    });

    $hsl.on('change', function () {
        if (updating) return;
        var rgb = parseHsl($(this).val());
        if (!rgb) {
            $error.text('Invalid HSL format. Use hsl(360, 100%, 50%)').removeClass('hidden');
            return;
        }
        applyColor(rgb.r, rgb.g, rgb.b);
    });

    $picker.on('input', function () {
        if (updating) return;
        var rgb = hexToRgb($(this).val());
        if (rgb) applyColor(rgb.r, rgb.g, rgb.b);
    });

    applyColor(37, 99, 235);
});
