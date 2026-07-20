(function () {
    'use strict';

    var state = {
        offsetX: 0,
        offsetY: 12,
        blur: 24,
        spread: 0,
        color: '#0f172a',
        opacity: 20,
        inset: false,
        stageBgMode: 'pixels',
        stageBgColor: '#e8ecf1',
        boxBgMode: 'color',
        boxBgColor: '#ffffff'
    };

    var stage = document.getElementById('box-shadow-stage');
    var stageGrid = document.querySelector('.box-shadow-stage-grid');
    var previewBox = document.getElementById('shadow-preview-box');
    var handle = document.getElementById('shadow-drag-handle');
    var offsetLine = document.getElementById('shadow-offset-line');
    var output = document.getElementById('shadow-css-output');

    var dragging = false;

    var fields = {
        offsetX: {
            range: document.getElementById('shadow-offset-x'),
            num: document.getElementById('shadow-offset-x-num'),
            val: document.getElementById('shadow-offset-x-val')
        },
        offsetY: {
            range: document.getElementById('shadow-offset-y'),
            num: document.getElementById('shadow-offset-y-num'),
            val: document.getElementById('shadow-offset-y-val')
        },
        blur: {
            range: document.getElementById('shadow-blur'),
            num: document.getElementById('shadow-blur-num'),
            val: document.getElementById('shadow-blur-val')
        },
        spread: {
            range: document.getElementById('shadow-spread'),
            num: document.getElementById('shadow-spread-num'),
            val: document.getElementById('shadow-spread-val')
        }
    };

    var colorInput = document.getElementById('shadow-color');
    var opacityRange = document.getElementById('shadow-opacity');
    var opacityVal = document.getElementById('shadow-opacity-val');
    var insetCheck = document.getElementById('shadow-inset');
    var stageBgColorInput = document.getElementById('stage-bg-color');
    var boxBgColorInput = document.getElementById('box-bg-color');

    function clamp(n, min, max) {
        return Math.max(min, Math.min(max, n));
    }

    function hexToRgb(hex) {
        var h = hex.replace('#', '');
        if (h.length === 3) {
            h = h.split('').map(function (c) { return c + c; }).join('');
        }
        var n = parseInt(h, 16);
        if (isNaN(n)) {
            return { r: 0, g: 0, b: 0 };
        }
        return {
            r: (n >> 16) & 255,
            g: (n >> 8) & 255,
            b: n & 255
        };
    }

    function shadowColorRgba() {
        var rgb = hexToRgb(state.color);
        var a = (state.opacity / 100).toFixed(2);
        return 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + a + ')';
    }

    function buildBoxShadow() {
        var parts = [];
        if (state.inset) {
            parts.push('inset');
        }
        parts.push(state.offsetX + 'px');
        parts.push(state.offsetY + 'px');
        parts.push(state.blur + 'px');
        parts.push(state.spread + 'px');
        parts.push(shadowColorRgba());
        return parts.join(' ');
    }

    function getStageCenter() {
        var rect = stage.getBoundingClientRect();
        return {
            x: rect.width / 2,
            y: rect.height / 2,
            rect: rect
        };
    }

    function updateHandleVisuals() {
        handle.style.transform = 'translate(calc(-50% + ' + state.offsetX + 'px), calc(-50% + ' + state.offsetY + 'px))';
        handle.setAttribute('aria-valuenow', String(state.offsetX));
        handle.setAttribute('aria-valuetext', state.offsetX + 'px, ' + state.offsetY + 'px');

        var length = Math.sqrt(state.offsetX * state.offsetX + state.offsetY * state.offsetY);
        var angle = Math.atan2(state.offsetY, state.offsetX) * (180 / Math.PI);
        offsetLine.style.width = Math.max(length, 0) + 'px';
        offsetLine.style.transform = 'translate(-50%, -50%) rotate(' + angle + 'deg)';
        offsetLine.style.opacity = length > 2 ? '1' : '0';
    }

    function syncInputs() {
        fields.offsetX.range.value = state.offsetX;
        fields.offsetX.num.value = state.offsetX;
        fields.offsetX.val.textContent = state.offsetX;

        fields.offsetY.range.value = state.offsetY;
        fields.offsetY.num.value = state.offsetY;
        fields.offsetY.val.textContent = state.offsetY;

        fields.blur.range.value = state.blur;
        fields.blur.num.value = state.blur;
        fields.blur.val.textContent = state.blur;

        fields.spread.range.value = state.spread;
        fields.spread.num.value = state.spread;
        fields.spread.val.textContent = state.spread;

        colorInput.value = state.color;
        opacityRange.value = state.opacity;
        opacityVal.textContent = state.opacity;
        insetCheck.checked = state.inset;
        stageBgColorInput.value = state.stageBgColor;
        boxBgColorInput.value = state.boxBgColor;
    }

    function setFillMode(target, mode) {
        if (target === 'stage') {
            state.stageBgMode = mode;
        } else {
            state.boxBgMode = mode;
        }

        document.querySelectorAll('.css-gen-fill-btn[data-target="' + target + '"]').forEach(function (btn) {
            var active = btn.getAttribute('data-mode') === mode;
            btn.classList.toggle('active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        var colorInputEl = target === 'stage' ? stageBgColorInput : boxBgColorInput;
        colorInputEl.classList.toggle('hidden', mode !== 'color');
        render();
    }

    function applyPreviewFills() {
        if (state.stageBgMode === 'pixels') {
            stageGrid.classList.remove('hidden');
            stage.style.background = '#e8ecf1';
        } else {
            stageGrid.classList.add('hidden');
            stage.style.background = state.stageBgColor;
        }

        if (state.boxBgMode === 'pixels') {
            previewBox.style.background = 'transparent';
            previewBox.classList.add('shadow-preview-box--pixels');
        } else {
            previewBox.style.background = state.boxBgColor;
            previewBox.classList.remove('shadow-preview-box--pixels');
        }
    }

    function buildCssOutput() {
        var lines = ['box-shadow: ' + buildBoxShadow() + ';'];
        if (state.boxBgMode === 'color') {
            lines.push('background-color: ' + state.boxBgColor + ';');
        } else {
            lines.push('background-color: transparent;');
        }
        return lines.join('\n');
    }

    function render() {
        var shadow = buildBoxShadow();
        previewBox.style.boxShadow = shadow;
        output.value = buildCssOutput();
        applyPreviewFills();
        updateHandleVisuals();
        syncInputs();
    }

    function setOffset(x, y) {
        state.offsetX = clamp(Math.round(x), -200, 200);
        state.offsetY = clamp(Math.round(y), -200, 200);
        render();
    }

    function setFromPointer(clientX, clientY) {
        var center = getStageCenter();
        setOffset(clientX - center.rect.left - center.x, clientY - center.rect.top - center.y);
    }

    function bindRangePair(key, prop, min, max) {
        var f = fields[key];

        function apply(val) {
            state[prop] = clamp(parseInt(val, 10) || 0, min, max);
            render();
        }

        f.range.addEventListener('input', function () {
            apply(f.range.value);
        });
        f.num.addEventListener('input', function () {
            apply(f.num.value);
        });
        f.num.addEventListener('change', function () {
            apply(f.num.value);
        });
    }

    function startDrag(e) {
        dragging = true;
        handle.classList.add('is-dragging');
        if (e.pointerId !== undefined) {
            handle.setPointerCapture(e.pointerId);
        }
        setFromPointer(e.clientX, e.clientY);
        e.preventDefault();
    }

    function moveDrag(e) {
        if (!dragging) {
            return;
        }
        setFromPointer(e.clientX, e.clientY);
        e.preventDefault();
    }

    function endDrag(e) {
        if (!dragging) {
            return;
        }
        dragging = false;
        handle.classList.remove('is-dragging');
        if (e.pointerId !== undefined && handle.hasPointerCapture(e.pointerId)) {
            handle.releasePointerCapture(e.pointerId);
        }
    }

    function nudgeOffset(dx, dy) {
        setOffset(state.offsetX + dx, state.offsetY + dy);
    }

    function onArrowKeys(e) {
        var step = e.shiftKey ? 10 : 1;
        var handled = false;

        switch (e.key) {
            case 'ArrowLeft':
                nudgeOffset(-step, 0);
                handled = true;
                break;
            case 'ArrowRight':
                nudgeOffset(step, 0);
                handled = true;
                break;
            case 'ArrowUp':
                nudgeOffset(0, -step);
                handled = true;
                break;
            case 'ArrowDown':
                nudgeOffset(0, step);
                handled = true;
                break;
            default:
                break;
        }

        if (handled) {
            e.preventDefault();
        }
    }

    handle.addEventListener('pointerdown', startDrag);
    handle.addEventListener('pointermove', moveDrag);
    handle.addEventListener('pointerup', endDrag);
    handle.addEventListener('pointercancel', endDrag);
    handle.addEventListener('keydown', onArrowKeys);

    stage.addEventListener('pointerdown', function (e) {
        if (e.target === handle) {
            return;
        }
        if (e.target === stage || e.target.classList.contains('box-shadow-stage-grid') || e.target === offsetLine) {
            stage.focus();
            dragging = true;
            handle.classList.add('is-dragging');
            setFromPointer(e.clientX, e.clientY);
            e.preventDefault();
        }
    });

    stage.addEventListener('pointermove', function (e) {
        if (dragging && !handle.hasPointerCapture(e.pointerId)) {
            setFromPointer(e.clientX, e.clientY);
            e.preventDefault();
        }
    });

    stage.addEventListener('pointerup', endDrag);
    stage.addEventListener('pointercancel', endDrag);
    stage.addEventListener('keydown', onArrowKeys);

    bindRangePair('offsetX', 'offsetX', -200, 200);
    bindRangePair('offsetY', 'offsetY', -200, 200);
    bindRangePair('blur', 'blur', 0, 200);
    bindRangePair('spread', 'spread', -100, 100);

    colorInput.addEventListener('input', function () {
        state.color = colorInput.value;
        render();
    });

    opacityRange.addEventListener('input', function () {
        state.opacity = parseInt(opacityRange.value, 10) || 0;
        render();
    });

    insetCheck.addEventListener('change', function () {
        state.inset = insetCheck.checked;
        render();
    });

    document.querySelectorAll('.css-gen-fill-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setFillMode(btn.getAttribute('data-target'), btn.getAttribute('data-mode'));
        });
    });

    stageBgColorInput.addEventListener('input', function () {
        state.stageBgColor = stageBgColorInput.value;
        if (state.stageBgMode === 'color') {
            render();
        }
    });

    boxBgColorInput.addEventListener('input', function () {
        state.boxBgColor = boxBgColorInput.value;
        if (state.boxBgMode === 'color') {
            render();
        }
    });

    document.getElementById('btn-shadow-reset').addEventListener('click', function () {
        state = {
            offsetX: 0,
            offsetY: 12,
            blur: 24,
            spread: 0,
            color: '#0f172a',
            opacity: 20,
            inset: false,
            stageBgMode: 'pixels',
            stageBgColor: '#e8ecf1',
            boxBgMode: 'color',
            boxBgColor: '#ffffff'
        };
        setFillMode('stage', 'pixels');
        setFillMode('box', 'color');
        render();
    });

    document.getElementById('btn-shadow-copy').addEventListener('click', function () {
        var text = output.value;
        if (!text) {
            return;
        }
        navigator.clipboard.writeText(text).then(function () {
            var btn = document.getElementById('btn-shadow-copy');
            var orig = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () {
                btn.textContent = orig;
            }, 1500);
        });
    });

    setFillMode('stage', state.stageBgMode);
    setFillMode('box', state.boxBgMode);
})();
