<?php
/**
 * Favicon and app icon link tags — included from header.php
 */
?>
<link rel="icon" href="<?= SITE_FAVICON_SVG ?>" type="image/svg+xml">
<link rel="icon" href="<?= SITE_FAVICON_32 ?>" sizes="32x32" type="image/png">
<link rel="icon" href="<?= SITE_FAVICON_16 ?>" sizes="16x16" type="image/png">
<link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_APPLE_TOUCH_ICON ?>">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="<?= htmlspecialchars(SITE_THEME_COLOR) ?>">
<meta name="msapplication-TileColor" content="<?= htmlspecialchars(SITE_THEME_COLOR) ?>">
