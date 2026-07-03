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
    <meta name="color-scheme" content="light dark">
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
                <span class="logo-text"><?= htmlspecialchars(SITE_NAME) ?><small><?= htmlspecialchars(SITE_DOMAIN) ?></small></span>
            </a>
            <nav class="main-nav" aria-label="Main navigation">
                <a href="<?= rtrim(SITE_URL, '/') ?>/">Home</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/#tools">Tools</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/about">About</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/contact">Contact</a>
            </nav>
            <button class="nav-toggle" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>
    <main class="site-main">
