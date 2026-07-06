<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/trending-tools.php';

$meta = page_meta(SITE_NAME, SITE_DESCRIPTION);
$trendingTools = trending_tools_for_home();
$trendingUpdated = trending_last_updated_label();

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <span class="hero-badge"><?= htmlspecialchars(SITE_DOMAIN) ?></span>
        <h1>Free Online Tools for Everyone</h1>
        <p><?= htmlspecialchars(SITE_TAGLINE) ?> No sign-up required. Your files stay in your browser.</p>
        <p class="hero-count">
            <span id="tools-visible-count"><?= count($TOOLS) ?></span> of <?= count($TOOLS) ?> tools<span id="tools-search-label"></span>
        </p>

        <div class="tool-search-wrap">
            <div class="tool-search-bar" id="tool-search-bar">
                <label for="tool-search" class="sr-only">Search tools</label>
                <span class="tool-search-icon" aria-hidden="true">🔍</span>
                <input
                    type="search"
                    id="tool-search"
                    class="tool-search-input"
                    placeholder="Search tools… e.g. image, json, calculator, password"
                    autocomplete="off"
                >
                <div class="tool-search-actions">
                    <button
                        type="button"
                        id="tool-search-voice"
                        class="tool-search-voice"
                        aria-label="Search by voice"
                        title="Search by voice"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.91-3c-.49 0-.9.36-.98.85C16.52 14.2 14.47 16 12 16s-4.52-1.8-4.93-4.15c-.08-.49-.49-.85-.98-.85-.61 0-1.09.54-1 1.14.49 3 2.89 5.35 5.91 5.78V20c0 .55.45 1 1 1s1-.45 1-1v-2.08c3.02-.43 5.42-2.78 5.91-5.78.1-.6-.39-1.14-1-1.14z"/>
                        </svg>
                    </button>
                    <button type="button" id="tool-search-clear" class="tool-search-clear hidden" aria-label="Clear search">×</button>
                </div>
            </div>
            <p id="tool-search-voice-status" class="tool-search-voice-status" aria-live="polite"></p>
        </div>
    </div>
</section>

<?php if (!empty($trendingTools)): ?>
<section id="trending-tools-section" class="trending-tools container">
    <div class="trending-tools-head">
        <h2 class="section-title trending-title">🔥 Trending tools</h2>
        <?php if ($trendingUpdated): ?>
            <p class="hint trending-updated"><?= htmlspecialchars($trendingUpdated) ?> · last <?= (int) (defined('TRENDING_LOOKBACK_DAYS') ? TRENDING_LOOKBACK_DAYS : 7) ?> days</p>
        <?php endif; ?>
    </div>
    <div class="tools-grid trending-grid">
        <?php foreach ($trendingTools as $tool): ?>
            <a href="<?= htmlspecialchars(tool_url($tool['slug'])) ?>" class="tool-card trending-card">
                <div class="tool-card-icon"><?= $tool['icon'] ?></div>
                <h3><?= htmlspecialchars($tool['title']) ?></h3>
                <p><?= htmlspecialchars($tool['description']) ?></p>
                <span class="trending-views"><?= number_format((int) $tool['views']) ?> views</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section id="tools" class="container">
    <p id="tool-search-empty" class="tool-search-empty hidden">No tools match your search. Try keywords like <em>image</em>, <em>json</em>, or <em>calculator</em>.</p>
    <?php foreach (tools_by_category() as $catKey => $group): ?>
        <div class="tool-category" id="cat-<?= htmlspecialchars($catKey) ?>" data-category="<?= htmlspecialchars(strtolower($group['label'])) ?>">
            <h2 class="section-title"><?= htmlspecialchars($group['label']) ?></h2>
            <div class="tools-grid">
                <?php foreach ($group['tools'] as $tool):
                    $searchText = strtolower(implode(' ', [
                        $tool['title'],
                        $tool['description'],
                        $group['label'],
                        $catKey,
                        str_replace('-', ' ', $tool['slug']),
                    ]));
                ?>
                    <a
                        href="<?= tool_url($tool['slug']) ?>"
                        class="tool-card"
                        data-search="<?= htmlspecialchars($searchText) ?>"
                    >
                        <div class="tool-card-icon"><?= $tool['icon'] ?></div>
                        <h3><?= htmlspecialchars($tool['title']) ?></h3>
                        <p><?= htmlspecialchars($tool['description']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<div class="container">
    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/home-search.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
