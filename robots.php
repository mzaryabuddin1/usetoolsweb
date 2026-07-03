<?php
/**
 * robots.txt — dynamic so sitemap URL uses correct domain.
 * Access: /robots.txt
 */
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=UTF-8');

echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /router.php\n\n";
echo "Sitemap: " . rtrim(SITE_URL, '/') . "/sitemap.xml\n";
