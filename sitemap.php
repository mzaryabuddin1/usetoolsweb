<?php
/**
 * XML sitemap — auto-generated from config.php tools list.
 * Access: /sitemap.xml
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=UTF-8');

$lastmod = date('Y-m-d');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach (all_site_urls() as $url): ?>
    <url>
        <loc><?= htmlspecialchars($url['loc']) ?></loc>
        <lastmod><?= $lastmod ?></lastmod>
        <changefreq><?= htmlspecialchars($url['changefreq']) ?></changefreq>
        <priority><?= htmlspecialchars($url['priority']) ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
