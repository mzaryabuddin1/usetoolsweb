import QRCodeStyling from 'https://esm.sh/qr-code-styling@1.6.0-rc.1';

(function () {
    'use strict';

    var shareCfg = window.QR_SHARE_CONFIG || { retentionDays: 10, maxMb: 25 };
    var currentType = 'url';
    var qrCode = null;
    var logoDataUrl = '';
    var shareUrl = '';
    var uploading = false;
    var hasQr = false;
    var parsedGradientCss = null;

    var $ = window.jQuery;

    var QR_TYPES = [
        { id: 'url', label: 'URL', icon: '🔗' },
        { id: 'text', label: 'Text', icon: '📝' },
        { id: 'email', label: 'Email', icon: '✉️' },
        { id: 'phone', label: 'Phone', icon: '📞' },
        { id: 'sms', label: 'SMS', icon: '💬' },
        { id: 'wifi', label: 'WiFi', icon: '📶' },
        { id: 'vcard', label: 'vCard', icon: '👤' },
        { id: 'mecard', label: 'MeCard', icon: '🪪' },
        { id: 'location', label: 'Location', icon: '📍' },
        { id: 'event', label: 'Event', icon: '📅' },
        { id: 'facebook', label: 'Facebook', icon: '👍' },
        { id: 'twitter', label: 'Twitter', icon: '🐦' },
        { id: 'youtube', label: 'YouTube', icon: '▶️' },
        { id: 'bitcoin', label: 'Bitcoin', icon: '₿' },
        { id: 'pdf', label: 'PDF link', icon: '📄' },
        { id: 'mp3', label: 'MP3 link', icon: '🎵' },
        { id: 'video', label: 'Video link', icon: '🎬' },
        { id: 'file', label: 'Upload file', icon: '📤' }
    ];

    var FORM_HTML = {
        url: '<label>Website URL</label><input type="url" data-field="url" placeholder="https://example.com">',
        text: '<label>Your text</label><textarea data-field="text" rows="4" placeholder="Any text message"></textarea>',
        email: '<label>Email</label><input type="email" data-field="to" placeholder="name@example.com">' +
            '<label>Subject</label><input type="text" data-field="subject" placeholder="Optional">' +
            '<label>Message</label><textarea data-field="body" rows="3" placeholder="Optional"></textarea>',
        phone: '<label>Phone number</label><input type="tel" data-field="phone" placeholder="+1 555 123 4567">',
        sms: '<label>Phone number</label><input type="tel" data-field="phone" placeholder="+1 555 123 4567">' +
            '<label>Message</label><textarea data-field="message" rows="3" placeholder="SMS text"></textarea>',
        wifi: '<label>Network name (SSID)</label><input type="text" data-field="ssid" placeholder="MyWiFi">' +
            '<label>Password</label><input type="text" data-field="password" placeholder="WiFi password">' +
            '<label>Encryption</label><select data-field="encryption"><option value="WPA">WPA/WPA2</option><option value="WEP">WEP</option><option value="nopass">None</option></select>',
        vcard: '<div class="qr-form-grid">' +
            '<div><label>First name</label><input data-field="first" placeholder="John"></div>' +
            '<div><label>Last name</label><input data-field="last" placeholder="Doe"></div>' +
            '<div><label>Organization</label><input data-field="org" placeholder="Company"></div>' +
            '<div><label>Phone</label><input data-field="phone" placeholder="+1 555 123 4567"></div>' +
            '<div><label>Email</label><input type="email" data-field="email" placeholder="john@example.com"></div>' +
            '<div><label>Website</label><input data-field="website" placeholder="https://example.com"></div>' +
            '</div>',
        mecard: '<label>Name</label><input data-field="name" placeholder="John Doe">' +
            '<label>Phone</label><input data-field="phone" placeholder="+1 555 123 4567">' +
            '<label>Email</label><input type="email" data-field="email" placeholder="john@example.com">',
        location: '<label>Latitude</label><input type="number" step="any" data-field="lat" placeholder="40.7128">' +
            '<label>Longitude</label><input type="number" step="any" data-field="lng" placeholder="-74.0060">',
        event: '<label>Event title</label><input data-field="title" placeholder="Meeting">' +
            '<label>Location</label><input data-field="location" placeholder="Office">' +
            '<label>Start</label><input type="datetime-local" data-field="start">' +
            '<label>End</label><input type="datetime-local" data-field="end">',
        facebook: '<label>Facebook profile or page URL</label><input type="url" data-field="url" placeholder="https://facebook.com/yourpage">',
        twitter: '<label>Twitter / X profile URL</label><input type="url" data-field="url" placeholder="https://twitter.com/username">' +
            '<label>Or tweet text</label><textarea data-field="tweet" rows="2" placeholder="Optional tweet to share"></textarea>',
        youtube: '<label>YouTube video or channel URL</label><input type="url" data-field="url" placeholder="https://youtube.com/watch?v=...">',
        bitcoin: '<label>Bitcoin address</label><input data-field="address" placeholder="bc1q...">' +
            '<label>Amount (optional)</label><input type="number" step="any" data-field="amount" placeholder="0.001">',
        pdf: '<label>Link to PDF file</label><input type="url" data-field="url" placeholder="https://example.com/file.pdf">' +
            '<p class="hint">Tip: use <strong>Upload file</strong> to host a PDF on our server and get a QR link automatically.</p>',
        mp3: '<label>Link to MP3 / audio file</label><input type="url" data-field="url" placeholder="https://example.com/audio.mp3">' +
            '<p class="hint">Or use <strong>Upload file</strong> to host audio temporarily.</p>',
        video: '<label>Link to video</label><input type="url" data-field="url" placeholder="https://example.com/video.mp4">' +
            '<p class="hint">Or use <strong>Upload file</strong> to host video temporarily.</p>',
        file: '<div class="alert alert-info qr-share-disclaimer">' +
            '<strong>Temporary file hosting</strong> — Upload a file and we generate a QR code for the download link. ' +
            'Files are <strong>automatically deleted after ' + shareCfg.retentionDays + ' days</strong>. ' +
            'Max ' + shareCfg.maxMb + ' MB. Do not upload sensitive data.</div>' +
            '<div class="drop-zone" id="qr-file-drop">' +
            '<p><strong>Drop a file here</strong> or click to browse</p>' +
            '<p>Uploads automatically — PDF, images (PNG, JPG, SVG…), docs, audio, video, ZIP</p></div>' +
            '<input type="file" id="qr-file-input" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.jpg,.jpeg,.png,.gif,.webp,.bmp,.svg,.zip,.rar,.7z,.mp3,.wav,.mp4,.webm,.mov,.json,.xml,.md,image/*,audio/*,video/*">' +
            '<div id="qr-file-selected" class="qr-file-selected hidden">' +
            '<span id="qr-file-name"></span>' +
            '<button type="button" class="btn btn-secondary btn-sm" id="qr-file-clear">Remove</button></div>' +
            '<div id="qr-share-result" class="qr-share-result hidden">' +
            '<label>Share link</label><div class="qr-share-url-row">' +
            '<input type="text" id="qr-share-url" readonly>' +
            '<button type="button" class="btn btn-secondary btn-sm" id="btn-copy-share-url">Copy</button></div>' +
            '<p id="qr-share-expiry" class="hint"></p></div>'
    };

    function field(name) {
        var el = document.querySelector('#qr-type-form [data-field="' + name + '"]');
        return el ? String(el.value || '').trim() : '';
    }

    function escVcard(s) {
        return String(s).replace(/[\\;,]/g, '\\$&').replace(/\n/g, '\\n');
    }

    function toIcalDate(val) {
        if (!val) return '';
        var d = new Date(val);
        if (isNaN(d.getTime())) return '';
        return d.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
    }

    function buildPayload(type) {
        switch (type) {
            case 'url':
                return field('url');
            case 'text':
                return field('text');
            case 'email': {
                var to = field('to');
                if (!to) throw new Error('Enter an email address.');
                var q = [];
                if (field('subject')) q.push('subject=' + encodeURIComponent(field('subject')));
                if (field('body')) q.push('body=' + encodeURIComponent(field('body')));
                return 'mailto:' + to + (q.length ? '?' + q.join('&') : '');
            }
            case 'phone':
                if (!field('phone')) throw new Error('Enter a phone number.');
                return 'tel:' + field('phone').replace(/\s/g, '');
            case 'sms': {
                var ph = field('phone').replace(/\s/g, '');
                if (!ph) throw new Error('Enter a phone number.');
                var msg = field('message');
                return 'sms:' + ph + (msg ? '?body=' + encodeURIComponent(msg) : '');
            }
            case 'wifi': {
                var ssid = field('ssid');
                if (!ssid) throw new Error('Enter WiFi network name.');
                var enc = field('encryption') || 'WPA';
                var pwd = field('password');
                if (enc === 'nopass') return 'WIFI:T:nopass;S:' + ssid + ';;';
                return 'WIFI:T:' + enc + ';S:' + ssid + ';P:' + pwd + ';;';
            }
            case 'vcard': {
                var fn = (field('first') + ' ' + field('last')).trim();
                if (!fn) throw new Error('Enter at least a first or last name.');
                var lines = ['BEGIN:VCARD', 'VERSION:3.0', 'FN:' + escVcard(fn),
                    'N:' + escVcard(field('last')) + ';' + escVcard(field('first')) + ';;;'];
                if (field('org')) lines.push('ORG:' + escVcard(field('org')));
                if (field('phone')) lines.push('TEL;TYPE=CELL:' + escVcard(field('phone')));
                if (field('email')) lines.push('EMAIL:' + escVcard(field('email')));
                if (field('website')) lines.push('URL:' + escVcard(field('website')));
                lines.push('END:VCARD');
                return lines.join('\n');
            }
            case 'mecard': {
                if (!field('name')) throw new Error('Enter a name.');
                var parts = ['MECARD:N:' + field('name')];
                if (field('phone')) parts.push('TEL:' + field('phone'));
                if (field('email')) parts.push('EMAIL:' + field('email'));
                return parts.join(';') + ';;';
            }
            case 'location': {
                var lat = field('lat');
                var lng = field('lng');
                if (!lat || !lng) throw new Error('Enter latitude and longitude.');
                return 'geo:' + lat + ',' + lng;
            }
            case 'event': {
                if (!field('title')) throw new Error('Enter event title.');
                var ev = ['BEGIN:VEVENT', 'SUMMARY:' + escVcard(field('title'))];
                if (field('location')) ev.push('LOCATION:' + escVcard(field('location')));
                var st = toIcalDate(field('start'));
                var en = toIcalDate(field('end'));
                if (st) ev.push('DTSTART:' + st);
                if (en) ev.push('DTEND:' + en);
                ev.push('END:VEVENT');
                return ev.join('\n');
            }
            case 'facebook':
            case 'youtube':
            case 'pdf':
            case 'mp3':
            case 'video':
                if (!field('url')) throw new Error('Enter a URL.');
                return field('url');
            case 'twitter': {
                if (field('url')) return field('url');
                if (field('tweet')) return 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(field('tweet'));
                throw new Error('Enter a Twitter URL or tweet text.');
            }
            case 'bitcoin': {
                if (!field('address')) throw new Error('Enter a Bitcoin address.');
                var amt = field('amount');
                return 'bitcoin:' + field('address') + (amt ? '?amount=' + amt : '');
            }
            case 'file':
                if (!shareUrl) throw new Error('Upload a file first.');
                return shareUrl;
            default:
                throw new Error('Unknown QR type.');
        }
    }

    function showError(msg) {
        $('#qr-error').text(msg).removeClass('hidden');
    }

    function hideError() {
        $('#qr-error').addClass('hidden');
    }

    function showStatus(msg, type) {
        var $s = $('#qr-status');
        $s.text(msg).removeClass('hidden alert-info alert-success alert-error')
            .addClass(type === 'error' ? 'alert-error' : (type === 'success' ? 'alert-success' : 'alert-info'));
    }

    function hideStatus() {
        $('#qr-status').addClass('hidden');
    }

    function getDesignOptions() {
        var size = parseInt($('#qr-size').val(), 10) || 400;
        var fgMode = $('#qr-fg-mode').val() || 'solid';
        return {
            size: size,
            fgMode: fgMode,
            fg: fgMode === 'solid' ? $('#qr-fg-color').val() : $('#qr-fg-gradient-start').val(),
            fg2: $('#qr-fg-gradient-end').val(),
            gradientType: $('#qr-gradient-type').val() || 'linear',
            gradientRotation: parseInt($('#qr-gradient-rotation').val(), 10) || 0,
            bg: $('#qr-bg-color').val(),
            dotsType: $('#qr-dot-style').val(),
            cornersType: $('#qr-corner-style').val()
        };
    }

    function buildForegroundStyle(d) {
        if (d.fgMode === 'gradient') {
            if (parsedGradientCss) {
                var rotation = parsedGradientCss.type === 'linear'
                    ? (parseInt($('#qr-gradient-rotation').val(), 10) || parsedGradientCss.rotation || 0)
                    : 0;
                return {
                    gradient: {
                        type: parsedGradientCss.type,
                        rotation: (rotation * Math.PI) / 180,
                        colorStops: parsedGradientCss.colorStops
                    }
                };
            }
            return {
                gradient: {
                    type: d.gradientType,
                    rotation: (d.gradientRotation * Math.PI) / 180,
                    colorStops: [
                        { offset: 0, color: d.fg },
                        { offset: 1, color: d.fg2 }
                    ]
                }
            };
        }
        return { color: d.fg };
    }

    function cssAngleToDegrees(value, unit) {
        var n = parseFloat(value);
        switch ((unit || 'deg').toLowerCase()) {
            case 'grad': return n * 0.9;
            case 'rad': return n * (180 / Math.PI);
            case 'turn': return n * 360;
            default: return n;
        }
    }

    function cssDirectionToDegrees(dir) {
        var key = dir.replace(/\s+/g, ' ').trim().toLowerCase();
        var map = {
            'to top': 0,
            'to right': 90,
            'to bottom': 180,
            'to left': 270,
            'to top right': 45,
            'to right top': 45,
            'to bottom right': 135,
            'to right bottom': 135,
            'to bottom left': 225,
            'to left bottom': 225,
            'to top left': 315,
            'to left top': 315
        };
        return Object.prototype.hasOwnProperty.call(map, key) ? map[key] : 0;
    }

    function colorToPickerHex(color) {
        if (/^#[0-9a-f]{6}$/i.test(color)) {
            return color;
        }
        var el = document.createElement('div');
        el.style.color = color;
        document.body.appendChild(el);
        var rgb = window.getComputedStyle(el).color;
        document.body.removeChild(el);
        var m = rgb.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
        if (!m) {
            return '#000000';
        }
        return '#' + [m[1], m[2], m[3]].map(function (n) {
            return parseInt(n, 10).toString(16).padStart(2, '0');
        }).join('');
    }

    function parseCssGradient(css) {
        var trimmed = css.trim();
        if (!trimmed) {
            return null;
        }

        var isLinear = /^linear-gradient\s*\(/i.test(trimmed);
        var isRadial = /^radial-gradient\s*\(/i.test(trimmed);
        if (!isLinear && !isRadial) {
            return null;
        }

        var open = trimmed.indexOf('(');
        var close = trimmed.lastIndexOf(')');
        if (open === -1 || close === -1 || close <= open) {
            return null;
        }

        var inner = trimmed.slice(open + 1, close).trim();
        var rotation = isLinear ? 180 : 0;
        var rest = inner;

        if (isLinear) {
            var angleMatch = rest.match(/^(-?\d+(?:\.\d+)?)(deg|grad|rad|turn)\s*,\s*/i);
            if (angleMatch) {
                rotation = cssAngleToDegrees(angleMatch[1], angleMatch[2]);
                rest = rest.slice(angleMatch[0].length);
            } else {
                var dirMatch = rest.match(/^to\s+(?:left|right|top|bottom)(?:\s+(?:left|right|top|bottom))?\s*,\s*/i);
                if (dirMatch) {
                    rotation = cssDirectionToDegrees(dirMatch[0].replace(/,\s*$/, ''));
                    rest = rest.slice(dirMatch[0].length);
                }
            }
        }

        var stopPattern = /(rgba?\s*\([^)]+\)|hsla?\s*\([^)]+\)|#[0-9a-fA-F]{3,8})\s*(\d+(?:\.\d+)?%)?/gi;
        var stops = [];
        var match;
        while ((match = stopPattern.exec(rest)) !== null) {
            stops.push({
                color: match[1].trim(),
                offset: match[2] ? parseFloat(match[2]) / 100 : null
            });
        }

        if (stops.length === 0) {
            return null;
        }

        if (stops.length === 1) {
            stops[0].offset = 0;
        } else {
            stops.forEach(function (stop, index) {
                if (stop.offset === null) {
                    stop.offset = index / (stops.length - 1);
                }
            });
        }

        return {
            type: isLinear ? 'linear' : 'radial',
            rotation: rotation,
            colorStops: stops.map(function (stop) {
                return { offset: stop.offset, color: stop.color };
            })
        };
    }

    function buildCssGradientPreview(parsed) {
        var stops = parsed.colorStops.map(function (stop) {
            return stop.color + ' ' + Math.round(stop.offset * 100) + '%';
        }).join(', ');

        if (parsed.type === 'radial') {
            return 'radial-gradient(circle, ' + stops + ')';
        }

        return 'linear-gradient(' + Math.round(parsed.rotation) + 'deg, ' + stops + ')';
    }

    function updateCssGradientPreview() {
        if (!parsedGradientCss) {
            return;
        }
        $('#qr-gradient-css-preview').css('background', buildCssGradientPreview(parsedGradientCss)).removeClass('hidden');
    }

    function setGradientRotation(angle) {
        var value = Math.max(0, Math.min(360, parseInt(angle, 10) || 0));
        $('#qr-gradient-rotation').val(value);
        $('#qr-gradient-rotation-value').text(value);

        if (parsedGradientCss && parsedGradientCss.type === 'linear') {
            parsedGradientCss.rotation = value;
            updateCssGradientPreview();
        }
    }

    function clearCssGradientOverride() {
        parsedGradientCss = null;
        $('#qr-gradient-css-preview').addClass('hidden').css('background', '');
        $('#qr-gradient-css-error').addClass('hidden').text('');
    }

    function clearManualGradientOverride() {
        clearCssGradientOverride();
        $('#qr-gradient-css').val('');
    }

    function syncGradientControlsFromParsed(parsed) {
        setGradientType(parsed.type);
        if (parsed.type === 'linear') {
            setGradientRotation(parsed.rotation);
        }
        if (parsed.colorStops.length >= 1) {
            $('#qr-fg-gradient-start').val(colorToPickerHex(parsed.colorStops[0].color));
        }
        if (parsed.colorStops.length >= 2) {
            $('#qr-fg-gradient-end').val(colorToPickerHex(parsed.colorStops[parsed.colorStops.length - 1].color));
        }
    }

    function applyCssGradientFromInput() {
        var css = $('#qr-gradient-css').val().trim();
        if (!css) {
            clearCssGradientOverride();
            refreshForegroundQr();
            return true;
        }

        var parsed = parseCssGradient(css);
        if (!parsed) {
            parsedGradientCss = null;
            $('#qr-gradient-css-preview').addClass('hidden');
            $('#qr-gradient-css-error').text('Could not parse gradient. Use linear-gradient(...) or radial-gradient(...) with color stops.').removeClass('hidden');
            refreshForegroundQr();
            return false;
        }

        parsedGradientCss = parsed;
        updateCssGradientPreview();
        $('#qr-gradient-css-error').addClass('hidden');
        syncGradientControlsFromParsed(parsed);
        refreshForegroundQr();
        return true;
    }

    function setGradientType(type) {
        $('#qr-gradient-type').val(type);
        $('.qr-gradient-type-btn').removeClass('active').attr('aria-selected', 'false');
        $('.qr-gradient-type-btn[data-type="' + type + '"]').addClass('active').attr('aria-selected', 'true');
        $('#qr-gradient-rotation-wrap').toggleClass('hidden', type === 'radial');
    }

    function setForegroundMode(mode) {
        var prev = $('#qr-fg-mode').val();
        if (mode === 'gradient' && prev === 'solid') {
            $('#qr-fg-gradient-start').val($('#qr-fg-color').val());
        } else if (mode === 'solid' && prev === 'gradient') {
            $('#qr-fg-color').val($('#qr-fg-gradient-start').val());
        }

        $('#qr-fg-mode').val(mode);
        $('.qr-fg-mode-btn').removeClass('active').attr('aria-selected', 'false');
        $('.qr-fg-mode-btn[data-mode="' + mode + '"]').addClass('active').attr('aria-selected', 'true');
        $('#qr-fg-solid-panel').toggleClass('hidden', mode !== 'solid');
        $('#qr-fg-gradient-panel').toggleClass('hidden', mode !== 'gradient');
        if (mode === 'gradient') {
            applyCssGradientFromInput();
        }
    }

    function refreshForegroundQr() {
        if (hasQr) {
            try {
                renderQr(buildPayload(currentType));
            } catch (e) { /* ignore */ }
        }
    }

    function renderQr(data) {
        var d = getDesignOptions();
        var fgStyle = buildForegroundStyle(d);
        var cornerDotType = d.cornersType === 'dot' ? 'dot' : 'square';
        var opts = {
            width: d.size,
            height: d.size,
            type: 'canvas',
            data: data,
            image: logoDataUrl || undefined,
            margin: 8,
            qrOptions: { errorCorrectionLevel: logoDataUrl ? 'H' : 'M' },
            dotsOptions: Object.assign({ type: d.dotsType }, fgStyle),
            cornersSquareOptions: Object.assign({ type: d.cornersType }, fgStyle),
            cornersDotOptions: Object.assign({ type: cornerDotType }, fgStyle),
            backgroundOptions: { color: d.bg },
            imageOptions: { crossOrigin: 'anonymous', margin: 8, imageSize: 0.35 }
        };

        if (!qrCode) {
            qrCode = new QRCodeStyling(opts);
            var container = document.getElementById('qrcode');
            container.innerHTML = '';
            qrCode.append(container);
        } else {
            qrCode.update(opts);
        }

        hasQr = true;
        $('#btn-download-png, #btn-download-svg').prop('disabled', false);
        $('#qr-preview-hint').text('Scan the preview to test before downloading.');
    }

    function generateQR() {
        try {
            if ($('#qr-fg-mode').val() === 'gradient') {
                applyCssGradientFromInput();
            }
            var payload = buildPayload(currentType);
            if (!payload) throw new Error('Please fill in the required fields.');
            hideError();
            renderQr(payload);
            showStatus('QR code created!', 'success');
        } catch (e) {
            showError(e.message);
        }
    }

    function renderTypeGrid() {
        var html = QR_TYPES.map(function (t) {
            var active = t.id === currentType ? ' active' : '';
            return '<button type="button" class="qr-type-chip' + active + '" data-type="' + t.id + '" role="tab">' +
                '<span class="qr-type-icon">' + t.icon + '</span><span>' + t.label + '</span></button>';
        }).join('');
        $('#qr-type-grid').html(html);
    }

    function processLogoFile(file) {
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            showError('Logo max size is 2 MB.');
            $('#qr-logo-input').val('');
            updateLogoPickerUi(null, '');
            return;
        }
        hideError();
        var reader = new FileReader();
        reader.onload = function (e) {
            logoDataUrl = e.target.result;
            updateLogoPickerUi(file, logoDataUrl);
            if (hasQr) {
                try { renderQr(buildPayload(currentType)); } catch (err) { /* ignore */ }
            }
        };
        reader.readAsDataURL(file);
    }

    function updateLogoPickerUi(file, previewUrl) {
        var $filename = $('#qr-logo-filename');
        var $thumb = $('#qr-logo-thumb');
        var $picker = $('#qr-logo-picker');
        var $clear = $('#qr-logo-clear');

        if (file && previewUrl) {
            $filename.text(file.name);
            $thumb.attr('src', previewUrl).removeClass('hidden');
            $picker.addClass('has-file');
            $clear.removeClass('hidden');
            return;
        }

        $filename.text('PNG, JPG, GIF, or SVG');
        $thumb.attr('src', '').addClass('hidden');
        $picker.removeClass('has-file');
        $clear.addClass('hidden');
    }

    function clearLogo() {
        logoDataUrl = '';
        $('#qr-logo-input').val('');
        updateLogoPickerUi(null, '');
        if (hasQr) {
            try { renderQr(buildPayload(currentType)); } catch (e) { /* ignore */ }
        }
    }

    function renderTypeForm(type) {
        currentType = type;
        shareUrl = '';
        $('#qr-type-form').html(FORM_HTML[type] || '');
        renderTypeGrid();

        var isFile = type === 'file';
        $('#btn-generate-qr').toggleClass('hidden', isFile);
        $('.qr-design-section, .qr-action-row').toggleClass('hidden', false);

        if (isFile) {
            bindFileUpload();
        }
    }

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function bindFileUpload() {
        var $drop = $('#qr-file-drop');
        var $input = $('#qr-file-input');

        $drop.off('click').on('click', function () {
            if (!uploading) $input.trigger('click');
        });

        $input.off('change').on('change', function () {
            if (this.files && this.files[0]) uploadAndGenerate(this.files[0]);
        });

        $drop.off('dragover dragleave drop').on('dragover', function (e) {
            e.preventDefault();
            if (!uploading) $(this).addClass('dragover');
        }).on('dragleave drop', function (e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        }).on('drop', function (e) {
            var files = e.originalEvent.dataTransfer.files;
            if (files && files[0]) uploadAndGenerate(files[0]);
        });

        $('#qr-file-clear').off('click').on('click', function () {
            shareUrl = '';
            $input.val('');
            $('#qr-file-selected, #qr-share-result').addClass('hidden');
            $('#qrcode').empty();
            qrCode = null;
            hasQr = false;
            $('#btn-download-png, #btn-download-svg').prop('disabled', true);
            hideError();
            hideStatus();
        });

        $('#btn-copy-share-url').off('click').on('click', function () {
            var url = $('#qr-share-url').val();
            if (url) navigator.clipboard.writeText(url).then(function () {
                showStatus('Link copied.', 'success');
            });
        });
    }

    async function uploadAndGenerate(file) {
        if (uploading) return;

        uploading = true;
        var $drop = $('#qr-file-drop');
        $drop.addClass('qr-drop-busy');
        hideError();
        showStatus('Uploading and generating QR code…', 'info');

        $('#qr-file-name').text(file.name + ' (' + formatBytes(file.size) + ')');
        $('#qr-file-selected').removeClass('hidden');

        try {
            var fd = new FormData();
            fd.append('file', file);
            var res = await fetch('/api/qr-share-upload.php', { method: 'POST', body: fd });
            var data = await res.json();
            if (!data.ok) throw new Error(data.error || 'Upload failed.');

            shareUrl = data.url;
            $('#qr-share-url').val(data.url);
            $('#qr-share-result').removeClass('hidden');
            var expiry = new Date(data.expires_at);
            $('#qr-share-expiry').text(
                'Deleted after ' + data.expires_in_days + ' days — expires ' + expiry.toLocaleDateString()
            );

            renderQr(shareUrl);
            showStatus('QR code ready! Scan to download the file.', 'success');
        } catch (err) {
            showError(err.message || 'Upload failed.');
            hideStatus();
        } finally {
            uploading = false;
            $drop.removeClass('qr-drop-busy');
        }
    }

    $(function () {
        renderTypeForm('url');
        setForegroundMode('solid');
        setGradientType('linear');

        $('#qr-type-grid').on('click', '.qr-type-chip', function () {
            var type = $(this).data('type');
            if (type === currentType) return;
            renderTypeForm(type);
            hideError();
            hideStatus();
        });

        $('#btn-generate-qr').on('click', generateQR);

        $('#qr-size').on('input', function () {
            $('#qr-size-value').text($(this).val());
            if (hasQr) {
                try {
                    renderQr(buildPayload(currentType));
                } catch (e) { /* ignore while editing */ }
            }
        });

        $('.qr-fg-mode-toggle').on('click', '.qr-fg-mode-btn', function () {
            var mode = $(this).data('mode');
            if (mode === $('#qr-fg-mode').val()) return;
            setForegroundMode(mode);
            refreshForegroundQr();
        });

        $('.qr-gradient-type-toggle').on('click', '.qr-gradient-type-btn', function () {
            var type = $(this).data('type');
            if (type === $('#qr-gradient-type').val()) return;
            clearManualGradientOverride();
            setGradientType(type);
            refreshForegroundQr();
        });

        $('#qr-gradient-rotation').on('input', function () {
            setGradientRotation($(this).val());
            refreshForegroundQr();
        });

        $('#qr-fg-color, #qr-bg-color, #qr-dot-style, #qr-corner-style').on('change', refreshForegroundQr);

        $('#qr-fg-gradient-start, #qr-fg-gradient-end').on('change', function () {
            clearManualGradientOverride();
            refreshForegroundQr();
        });

        var cssGradientTimer = null;
        $('#qr-gradient-css').on('input', function () {
            clearTimeout(cssGradientTimer);
            cssGradientTimer = setTimeout(applyCssGradientFromInput, 350);
        });

        $('#qr-gradient-css').on('paste', function () {
            clearTimeout(cssGradientTimer);
            cssGradientTimer = setTimeout(applyCssGradientFromInput, 100);
        });

        $('#qr-logo-input').on('change', function () {
            processLogoFile(this.files && this.files[0]);
        });

        $('#qr-logo-clear').on('click', clearLogo);

        $('#qr-logo-picker').on('dragover', function (e) {
            e.preventDefault();
            $(this).addClass('dragover');
        }).on('dragleave drop', function (e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        }).on('drop', function (e) {
            var files = e.originalEvent.dataTransfer.files;
            if (files && files[0]) processLogoFile(files[0]);
        });

        $('#btn-download-png').on('click', function () {
            if (qrCode) qrCode.download({ name: 'qrcode', extension: 'png' });
        });

        $('#btn-download-svg').on('click', function () {
            if (qrCode) qrCode.download({ name: 'qrcode', extension: 'svg' });
        });
    });
})();
