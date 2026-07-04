<?php
/**
 * Router for PHP built-in dev server (clean URLs without Apache).
 * Run: php -S localhost:8000 router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// SEO routes
if ($uri === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    return true;
}
if ($uri === '/robots.txt') {
    require __DIR__ . '/robots.php';
    return true;
}

// API routes
if ($uri === '/api/video-cut' || $uri === '/api/video-cut.php') {
    require __DIR__ . '/api/video-cut.php';
    return true;
}
if ($uri === '/api/pdf-server' || $uri === '/api/pdf-server.php') {
    require __DIR__ . '/api/pdf-server.php';
    return true;
}

// Serve static files as-is
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

// Route /tool-name -> tool-name.php
if ($uri !== '/') {
    $slug = trim($uri, '/');
    $phpFile = __DIR__ . '/' . $slug . '.php';
    if (is_file($phpFile)) {
        require $phpFile;
        return true;
    }
}

require __DIR__ . '/index.php';
return true;
