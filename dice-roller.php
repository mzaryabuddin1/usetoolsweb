<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Dice Roller',
    'Free virtual dice roller. Roll d4, d6, d8, d10, d12, or d20 dice online.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Dice Roller</h1>
        <p>Roll virtual dice — choose die type and number of rolls.</p>
    </div>

    <div class="tool-panel">
        <div class="form-row">
            <div>
                <label for="dice-type">Die type</label>
                <select id="dice-type">
                    <option value="4">d4</option>
                    <option value="6" selected>d6</option>
                    <option value="8">d8</option>
                    <option value="10">d10</option>
                    <option value="12">d12</option>
                    <option value="20">d20</option>
                </select>
            </div>
            <div>
                <label for="dice-count">Number of rolls</label>
                <input type="number" id="dice-count" value="1" min="1" max="100">
            </div>
        </div>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-dice-roll">Roll</button>
            <button type="button" class="btn btn-secondary" id="btn-dice-clear">Clear</button>
        </div>

        <div id="dice-results" class="dice-results hidden"></div>

        <div class="stats-row hidden" id="dice-stats">
            <div class="stat-box">
                <div class="value" id="dice-total">0</div>
                <div class="label">Total</div>
            </div>
            <div class="stat-box">
                <div class="value" id="dice-avg">0</div>
                <div class="label">Average</div>
            </div>
        </div>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/dice-roller.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
