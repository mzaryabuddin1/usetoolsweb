<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Unit Converter',
    'Free unit converter for length, weight, and temperature. Convert m, km, ft, mi, kg, lb, C, F, and more.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Unit Converter</h1>
        <p>Convert between common length, weight, and temperature units instantly.</p>
    </div>

    <div class="tool-panel">
        <div class="btn-row" style="margin-top:0;">
            <button type="button" class="btn btn-secondary unit-tab active" data-tab="length">Length</button>
            <button type="button" class="btn btn-secondary unit-tab" data-tab="weight">Weight</button>
            <button type="button" class="btn btn-secondary unit-tab" data-tab="temp">Temperature</button>
        </div>

        <div id="unit-length" class="unit-panel">
            <div class="form-row">
                <div>
                    <label for="len-value">Value</label>
                    <input type="number" id="len-value" placeholder="Enter value">
                </div>
                <div>
                    <label for="len-from">From</label>
                    <select id="len-from">
                        <option value="m">Meters (m)</option>
                        <option value="km">Kilometers (km)</option>
                        <option value="ft">Feet (ft)</option>
                        <option value="mi">Miles (mi)</option>
                        <option value="in">Inches (in)</option>
                    </select>
                </div>
            </div>
            <label for="len-to" style="margin-top:1rem;">To</label>
            <select id="len-to">
                <option value="m">Meters (m)</option>
                <option value="km">Kilometers (km)</option>
                <option value="ft">Feet (ft)</option>
                <option value="mi">Miles (mi)</option>
                <option value="in">Inches (in)</option>
            </select>
            <div class="stat-box" style="margin-top:1rem;">
                <div class="value" id="len-result">—</div>
                <div class="label">Result</div>
            </div>
        </div>

        <div id="unit-weight" class="unit-panel hidden">
            <div class="form-row">
                <div>
                    <label for="wt-value">Value</label>
                    <input type="number" id="wt-value" placeholder="Enter value">
                </div>
                <div>
                    <label for="wt-from">From</label>
                    <select id="wt-from">
                        <option value="kg">Kilograms (kg)</option>
                        <option value="lb">Pounds (lb)</option>
                        <option value="oz">Ounces (oz)</option>
                    </select>
                </div>
            </div>
            <label for="wt-to" style="margin-top:1rem;">To</label>
            <select id="wt-to">
                <option value="kg">Kilograms (kg)</option>
                <option value="lb">Pounds (lb)</option>
                <option value="oz">Ounces (oz)</option>
            </select>
            <div class="stat-box" style="margin-top:1rem;">
                <div class="value" id="wt-result">—</div>
                <div class="label">Result</div>
            </div>
        </div>

        <div id="unit-temp" class="unit-panel hidden">
            <div class="form-row">
                <div>
                    <label for="temp-value">Value</label>
                    <input type="number" id="temp-value" placeholder="Enter value">
                </div>
                <div>
                    <label for="temp-from">From</label>
                    <select id="temp-from">
                        <option value="C">Celsius (°C)</option>
                        <option value="F">Fahrenheit (°F)</option>
                        <option value="K">Kelvin (K)</option>
                    </select>
                </div>
            </div>
            <label for="temp-to" style="margin-top:1rem;">To</label>
            <select id="temp-to">
                <option value="C">Celsius (°C)</option>
                <option value="F">Fahrenheit (°F)</option>
                <option value="K">Kelvin (K)</option>
            </select>
            <div class="stat-box" style="margin-top:1rem;">
                <div class="value" id="temp-result">—</div>
                <div class="label">Result</div>
            </div>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/unit-converter.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
