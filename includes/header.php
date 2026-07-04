<?php
require_once __DIR__ . '/../config.php';

if (!isset($meta)) {
    $meta = page_meta(SITE_NAME, SITE_DESCRIPTION);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
    (function () {
        var key = 'qt-theme';
        var saved = localStorage.getItem(key);
        var dark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
        var root = document.documentElement;
        if (dark) root.setAttribute('data-theme', 'dark');
        root.style.colorScheme = dark ? 'dark' : 'light';
    })();
    </script>
    <meta name="color-scheme" content="light dark">
    <?php if (GA_MEASUREMENT_ID !== ''): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars(GA_MEASUREMENT_ID) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= htmlspecialchars(GA_MEASUREMENT_ID) ?>');
    </script>
    <?php endif; ?>
    <title><?= htmlspecialchars($meta['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta['keywords'] ?? SITE_KEYWORDS) ?>">
    <meta name="author" content="<?= htmlspecialchars(SITE_AUTHOR) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($meta['canonical'] ?? SITE_URL) ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="<?= htmlspecialchars($meta['og_type'] ?? 'website') ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars(SITE_NAME) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($meta['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta['description']) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($meta['canonical'] ?? SITE_URL) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($meta['og_image'] ?? rtrim(SITE_URL, '/') . SITE_OG_IMAGE) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($meta['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($meta['description']) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($meta['og_image'] ?? rtrim(SITE_URL, '/') . SITE_OG_IMAGE) ?>">
    <?php require __DIR__ . '/seo-schema.php'; ?>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= rtrim(SITE_URL, '/') ?>/" class="logo">
                <span class="logo-icon">⚡</span>
                <span class="logo-text"><?= htmlspecialchars(SITE_NAME) ?></span>
            </a>
            <nav class="main-nav" aria-label="Main navigation">
                <a href="<?= rtrim(SITE_URL, '/') ?>/">Home</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/#tools">Tools</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/about">About</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/contact">Contact</a>
            </nav>
            <div class="header-actions">
                <button
                    type="button"
                    id="theme-toggle"
                    class="theme-toggle"
                    aria-label="Switch to dark mode"
                    aria-pressed="false"
                    title="Toggle dark mode"
                >
                    <svg class="theme-icon theme-icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                        <path d="M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 0 1-4.4 2.26 5.403 5.403 0 0 1-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/>
                    </svg>
                    <svg class="theme-icon theme-icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                        <path d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0-5a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1zm0 18a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-2a1 1 0 0 1 1-1zM4.22 4.22a1 1 0 0 1 1.42 0l1.42 1.42a1 1 0 1 1-1.42 1.42L4.22 5.64a1 1 0 0 1 0-1.42zm14.14 14.14a1 1 0 0 1 1.42 0l1.42 1.42a1 1 0 0 1-1.42 1.42l-1.42-1.42a1 1 0 0 1 0-1.42zM3 12a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1zm16 0a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2h-2a1 1 0 0 1-1-1zM4.22 19.78a1 1 0 0 1 0-1.42l1.42-1.42a1 1 0 0 1 1.42 1.42l-1.42 1.42a1 1 0 0 1-1.42 0zm14.14-14.14a1 1 0 0 1 0-1.42l1.42-1.42a1 1 0 1 1 1.42 1.42l-1.42 1.42a1 1 0 0 1-1.42 0z"/>
                    </svg>
                </button>
                <button class="nav-toggle" aria-label="Toggle menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>
    <main class="site-main">
