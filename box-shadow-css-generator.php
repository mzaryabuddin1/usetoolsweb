<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Box Shadow CSS Generator',
    'Free box shadow CSS generator with live preview. Drag the shadow handle, set blur, spread, color, and copy CSS instantly.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Box Shadow CSS Generator</h1>
        <p>Build box shadows visually and copy the CSS. Adjust offset, blur, spread, and color — drag the shadow handle or use arrow keys to position it.</p>
    </div>

    <div class="tool-panel css-gen-panel">
        <h2 class="css-gen-section-title">Settings</h2>

        <div class="css-gen-layout">
            <div class="css-gen-controls">
                <div class="css-gen-grid">
                    <div>
                        <label for="shadow-offset-x">Offset X: <span id="shadow-offset-x-val">0</span>px</label>
                        <input type="range" id="shadow-offset-x" min="-200" max="200" value="0" step="1">
                        <input type="number" id="shadow-offset-x-num" min="-200" max="200" value="0" step="1" class="css-gen-num">
                    </div>
                    <div>
                        <label for="shadow-offset-y">Offset Y: <span id="shadow-offset-y-val">12</span>px</label>
                        <input type="range" id="shadow-offset-y" min="-200" max="200" value="12" step="1">
                        <input type="number" id="shadow-offset-y-num" min="-200" max="200" value="12" step="1" class="css-gen-num">
                    </div>
                    <div>
                        <label for="shadow-blur">Blur: <span id="shadow-blur-val">24</span>px</label>
                        <input type="range" id="shadow-blur" min="0" max="200" value="24" step="1">
                        <input type="number" id="shadow-blur-num" min="0" max="200" value="24" step="1" class="css-gen-num">
                    </div>
                    <div>
                        <label for="shadow-spread">Spread: <span id="shadow-spread-val">0</span>px</label>
                        <input type="range" id="shadow-spread" min="-100" max="100" value="0" step="1">
                        <input type="number" id="shadow-spread-num" min="-100" max="100" value="0" step="1" class="css-gen-num">
                    </div>
                    <div>
                        <label for="shadow-color">Color</label>
                        <input type="color" id="shadow-color" value="#0f172a">
                    </div>
                    <div>
                        <label for="shadow-opacity">Opacity: <span id="shadow-opacity-val">20</span>%</label>
                        <input type="range" id="shadow-opacity" min="0" max="100" value="20" step="1">
                    </div>
                </div>

                <label class="css-gen-check">
                    <input type="checkbox" id="shadow-inset">
                    Inset shadow
                </label>

                <div class="btn-row">
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-shadow-reset">Reset</button>
                </div>
            </div>

            <div class="css-gen-preview-col">
                <div class="box-shadow-stage" id="box-shadow-stage" tabindex="0" aria-label="Shadow preview — drag handle or use arrow keys">
                    <div class="box-shadow-stage-grid" aria-hidden="true"></div>
                    <div id="shadow-preview-box" class="shadow-preview-box">Box</div>
                    <div id="shadow-drag-handle" class="shadow-drag-handle" role="slider" aria-label="Shadow offset" aria-valuemin="-200" aria-valuemax="200" tabindex="0"></div>
                    <div id="shadow-offset-line" class="shadow-offset-line" aria-hidden="true"></div>
                </div>
                <div class="css-gen-fill-rows">
                    <div class="css-gen-fill-row">
                        <span class="css-gen-fill-label">Stage</span>
                        <div class="css-gen-fill-toggle" role="tablist" aria-label="Stage background">
                            <button type="button" class="css-gen-fill-btn active" data-target="stage" data-mode="pixels" role="tab" aria-selected="true">Pixels</button>
                            <button type="button" class="css-gen-fill-btn" data-target="stage" data-mode="color" role="tab" aria-selected="false">Color</button>
                        </div>
                        <input type="color" id="stage-bg-color" class="css-gen-fill-color hidden" value="#e8ecf1" title="Stage background color">
                    </div>
                    <div class="css-gen-fill-row">
                        <span class="css-gen-fill-label">Box</span>
                        <div class="css-gen-fill-toggle" role="tablist" aria-label="Box background">
                            <button type="button" class="css-gen-fill-btn" data-target="box" data-mode="pixels" role="tab" aria-selected="false">Pixels</button>
                            <button type="button" class="css-gen-fill-btn active" data-target="box" data-mode="color" role="tab" aria-selected="true">Color</button>
                        </div>
                        <input type="color" id="box-bg-color" class="css-gen-fill-color" value="#ffffff" title="Box background color">
                    </div>
                </div>
                <p class="hint css-gen-hint">Drag the orange handle to move the shadow. Click the stage and use <kbd>←</kbd> <kbd>↑</kbd> <kbd>→</kbd> <kbd>↓</kbd> (hold Shift for 10px steps).</p>
            </div>
        </div>

        <label for="shadow-css-output" style="margin-top:1.5rem;">Generated CSS</label>
        <textarea id="shadow-css-output" readonly rows="3"></textarea>
        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-shadow-copy">Copy CSS</button>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/box-shadow-css-generator.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
