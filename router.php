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
if ($uri === '/site.webmanifest') {
    header('Content-Type: application/manifest+json; charset=utf-8');
    readfile(__DIR__ . '/site.webmanifest');
    return true;
}
if ($uri === '/favicon.ico') {
    header('Content-Type: image/png');
    readfile(__DIR__ . '/assets/images/favicon-32x32.png');
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
if ($uri === '/api/curl-proxy' || $uri === '/api/curl-proxy.php') {
    require __DIR__ . '/api/curl-proxy.php';
    return true;
}
if ($uri === '/api/qr-share-upload' || $uri === '/api/qr-share-upload.php') {
    require __DIR__ . '/api/qr-share-upload.php';
    return true;
}
if ($uri === '/api/air-share-create' || $uri === '/api/air-share-create.php') {
    require __DIR__ . '/api/air-share-create.php';
    return true;
}
if ($uri === '/api/air-share-signal' || $uri === '/api/air-share-signal.php') {
    require __DIR__ . '/api/air-share-signal.php';
    return true;
}
if ($uri === '/api/air-share-desk' || $uri === '/api/air-share-desk.php') {
    require __DIR__ . '/api/air-share-desk.php';
    return true;
}
if ($uri === '/api/cron-trending' || $uri === '/api/cron-trending.php') {
    require __DIR__ . '/api/cron-trending.php';
    return true;
}
if ($uri === '/api/bg-remove' || $uri === '/api/bg-remove.php') {
    require __DIR__ . '/api/bg-remove.php';
    return true;
}
if ($uri === '/api/email-test' || $uri === '/api/email-test.php') {
    require __DIR__ . '/api/email-test.php';
    return true;
}
if ($uri === '/api/site-audit' || $uri === '/api/site-audit.php') {
    require __DIR__ . '/api/site-audit.php';
    return true;
}
if ($uri === '/api/speed-test' || $uri === '/api/speed-test.php') {
    require __DIR__ . '/api/speed-test.php';
    return true;
}
if (preg_match('#^/f/([a-f0-9]{32})$#', $uri, $shareMatch)) {
    $_GET['token'] = $shareMatch[1];
    require __DIR__ . '/share-download.php';
    return true;
}
if (preg_match('#^/s/([a-f0-9]{32})$#', $uri, $textMatch)) {
    $_GET['token'] = $textMatch[1];
    require __DIR__ . '/share-view.php';
    return true;
}

// Legacy redirects
if ($uri === '/css-generator') {
    header('Location: /box-shadow-css-generator', true, 301);
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
        $GLOBALS['tool_guide_slug'] = $slug;
        require $phpFile;
        return true;
    }
}

require __DIR__ . '/index.php';
return true;
