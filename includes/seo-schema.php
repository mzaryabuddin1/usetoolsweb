<?php
/**
 * JSON-LD structured data for SEO.
 * Included from header.php when $meta is set.
 */

if (!isset($meta)) {
    return;
}

$baseUrl = rtrim(SITE_URL, '/');
$path    = trim(parse_url($meta['canonical'] ?? '/', PHP_URL_PATH) ?? '/', '/');

$websiteSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => SITE_NAME,
    'url'      => $baseUrl . '/',
    'description' => SITE_DESCRIPTION,
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => $baseUrl . '/?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
];

$orgSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => SITE_NAME,
    'url'      => $baseUrl . '/',
    'email'    => SITE_EMAIL,
];

$schemas = [$websiteSchema, $orgSchema];

if ($path !== '' && $path !== 'index.php') {
    $tool = function_exists('get_tool_by_slug') ? get_tool_by_slug($path) : null;

    if ($tool) {
        $schemas[] = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebApplication',
            'name'        => $tool['title'],
            'url'         => tool_url($tool['slug']),
            'description' => $tool['description'],
            'applicationCategory' => 'UtilityApplication',
            'operatingSystem'   => 'Any',
            'browserRequirements' => 'Requires JavaScript',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ],
            'provider' => [
                '@type' => 'Organization',
                'name'  => SITE_NAME,
                'url'   => $baseUrl . '/',
            ],
        ];
    } else {
        $schemas[] = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => $meta['title'],
            'url'         => $meta['canonical'],
            'description' => $meta['description'],
        ];
    }
}

foreach ($schemas as $schema) {
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n    ";
}
