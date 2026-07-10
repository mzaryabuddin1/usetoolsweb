<?php
/**
 * XML sitemap — auto-generated from config.php tools list.
 * Access: /sitemap.xml
 */
ob_start();
require_once __DIR__ . '/config.php';
ob_end_clean();

header('Content-Type: application/xml; charset=UTF-8');

$lastmod = date('Y-m-d');
$urls    = all_site_urls();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>' . "\n";
    echo '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
    echo '    <changefreq>' . htmlspecialchars($url['changefreq'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</changefreq>' . "\n";
    echo '    <priority>' . htmlspecialchars($url['priority'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>';
