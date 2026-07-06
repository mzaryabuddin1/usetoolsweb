<?php
/**
 * Trending tools — read/write JSON cache and map GA4 paths to tools.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

require_once __DIR__ . '/ga4-client.php';

/** Paths that are not tool pages */
function trending_excluded_slugs(): array
{
    return ['about', 'privacy', 'contact', 'index', 'sitemap', 'robots'];
}

function trending_json_path(): string
{
    return defined('TRENDING_JSON_FILE') ? TRENDING_JSON_FILE : (dirname(__DIR__) . '/data/trending-tools.json');
}

function trending_ensure_data_dir(): void
{
    $dir = dirname(trending_json_path());
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

function trending_path_to_slug(string $path): ?string
{
    $path = strtolower(trim($path));
    $path = strtok($path, '?') ?: $path;
    $path = trim($path, '/');

    if ($path === '') {
        return null;
    }

    if (str_ends_with($path, '.php')) {
        $path = substr($path, 0, -4);
    }

    if (in_array($path, trending_excluded_slugs(), true)) {
        return null;
    }

    $tool = get_tool_by_slug($path);
    return $tool ? $path : null;
}

/**
 * Build trending list from GA4 page view rows.
 *
 * @param array<int, array{path: string, views: int}> $pageViews
 */
function trending_build_from_page_views(array $pageViews, int $max = 6): array
{
    $max   = max(1, min(20, $max));
    $views = [];

    foreach ($pageViews as $row) {
        $slug = trending_path_to_slug($row['path']);
        if (!$slug) {
            continue;
        }
        $views[$slug] = ($views[$slug] ?? 0) + (int) $row['views'];
    }

    arsort($views);
    $tools = [];
    foreach ($views as $slug => $count) {
        if (count($tools) >= $max) {
            break;
        }
        $tool = get_tool_by_slug($slug);
        if (!$tool) {
            continue;
        }
        $tools[] = [
            'slug'        => $slug,
            'title'       => $tool['title'],
            'description' => $tool['description'],
            'icon'        => $tool['icon'],
            'category'    => $tool['category'] ?? 'utility',
            'views'       => $count,
            'url'         => tool_url($slug),
        ];
    }

    return $tools;
}

function trending_load_json(): array
{
    $path = trending_json_path();
    if (!is_file($path)) {
        return [
            'updated_at'  => null,
            'period_days' => defined('TRENDING_LOOKBACK_DAYS') ? (int) TRENDING_LOOKBACK_DAYS : 7,
            'source'      => 'google_analytics_4',
            'tools'       => [],
        ];
    }

    $data = json_decode(file_get_contents($path), true);
    if (!is_array($data)) {
        return ['updated_at' => null, 'period_days' => 7, 'source' => 'google_analytics_4', 'tools' => []];
    }

    $data['tools'] = is_array($data['tools'] ?? null) ? $data['tools'] : [];
    return $data;
}

function trending_save_json(array $payload): bool
{
    trending_ensure_data_dir();
    $payload['updated_at'] = gmdate('c');
    return (bool) file_put_contents(
        trending_json_path(),
        json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

/**
 * Pull GA4 data and refresh trending-tools.json.
 */
function trending_refresh_from_ga4(): array
{
    $propertyId = defined('GA4_PROPERTY_ID') ? GA4_PROPERTY_ID : '';
    $credsFile  = defined('GA4_CREDENTIALS_FILE') ? GA4_CREDENTIALS_FILE : '';
    $days       = defined('TRENDING_LOOKBACK_DAYS') ? (int) TRENDING_LOOKBACK_DAYS : 7;
    $max        = defined('TRENDING_MAX_TOOLS') ? (int) TRENDING_MAX_TOOLS : 6;

    $pageViews = ga4_fetch_page_views($propertyId, $credsFile, $days, 80);
    $tools     = trending_build_from_page_views($pageViews, $max);

    $payload = [
        'updated_at'  => gmdate('c'),
        'period_days' => $days,
        'source'      => 'google_analytics_4',
        'property_id' => preg_replace('/\D/', '', $propertyId),
        'tools'       => $tools,
    ];

    if (!trending_save_json($payload)) {
        throw new RuntimeException('Could not write trending JSON file.');
    }

    return $payload;
}

/**
 * Trending tools for the home page (empty array if none cached yet).
 */
function trending_tools_for_home(): array
{
    $data = trending_load_json();
    return $data['tools'] ?? [];
}

function trending_last_updated_label(): string
{
    $data = trending_load_json();
    if (empty($data['updated_at'])) {
        return '';
    }
    $ts = strtotime($data['updated_at']);
    if (!$ts) {
        return '';
    }
    return 'Updated ' . gmdate('M j, Y', $ts);
}
