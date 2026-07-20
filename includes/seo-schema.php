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
if (str_ends_with($path, '.php')) {
    $path = substr($path, 0, -4);
}

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
    'logo'     => $baseUrl . SITE_LOGO,
    'email'    => SITE_EMAIL,
];

$schemas = [$websiteSchema, $orgSchema];

if ($path !== '' && $path !== 'index') {
    $tool = function_exists('get_tool_by_slug') ? get_tool_by_slug($path) : null;

    if ($tool) {
        global $TOOL_CATEGORIES;
        $catLabel = $TOOL_CATEGORIES[$tool['category'] ?? 'utility'] ?? 'Tools';

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

        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => $baseUrl . '/',
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => $catLabel,
                    'item'     => $baseUrl . '/#cat-' . ($tool['category'] ?? 'utility'),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 3,
                    'name'     => $tool['title'],
                    'item'     => tool_url($tool['slug']),
                ],
            ],
        ];

        if (!function_exists('tool_guide_build')) {
            require_once __DIR__ . '/tool-guide.php';
        }
        $guide = tool_guide_build($tool);
        if (!empty($guide['faqs'])) {
            $faqEntities = [];
            foreach ($guide['faqs'] as $faq) {
                if (empty($faq[0]) || empty($faq[1])) {
                    continue;
                }
                $faqEntities[] = [
                    '@type'          => 'Question',
                    'name'           => $faq[0],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => $faq[1],
                    ],
                ];
            }
            if ($faqEntities) {
                $schemas[] = [
                    '@context'   => 'https://schema.org',
                    '@type'      => 'FAQPage',
                    'mainEntity' => $faqEntities,
                ];
            }
        }
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
