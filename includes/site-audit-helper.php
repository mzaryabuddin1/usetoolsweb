<?php
/**
 * Site audit helper — fetch a public URL and analyze SEO, security, performance signals.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

require_once __DIR__ . '/curl-proxy-helper.php';

function site_audit_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function site_audit_timeout(): int
{
    return defined('SITE_AUDIT_TIMEOUT') ? max(5, (int) SITE_AUDIT_TIMEOUT) : 25;
}

function site_audit_fetch(string $url): array
{
    $url = curl_proxy_validate_url($url);
    $start = microtime(true);

    if (!function_exists('curl_init')) {
        site_audit_json_error(503, 'PHP cURL extension is not enabled.');
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => site_audit_timeout(),
        CURLOPT_USERAGENT      => 'usetoolsweb-site-audit/1.0',
        CURLOPT_HEADER         => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $raw      = curl_exec($ch);
    $errno    = curl_errno($ch);
    $error    = curl_error($ch);
    $info     = curl_getinfo($ch);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('Could not fetch URL: ' . ($error ?: 'request failed'));
    }

    $headerSize = (int) ($info['header_size'] ?? 0);
    $headersRaw = substr($raw, 0, $headerSize);
    $body       = substr($raw, $headerSize);

    return [
        'url'          => $info['url'] ?? $url,
        'status'       => (int) ($info['http_code'] ?? 0),
        'time_ms'      => (int) round((microtime(true) - $start) * 1000),
        'size_bytes'   => strlen($body),
        'headers'      => site_audit_parse_headers($headersRaw),
        'body'         => $body,
        'ssl_verify'   => str_starts_with(strtolower($url), 'https'),
        'redirects'    => (int) ($info['redirect_count'] ?? 0),
        'content_type' => (string) ($info['content_type'] ?? ''),
    ];
}

function site_audit_parse_headers(string $raw): array
{
    $headers = [];
    foreach (preg_split('/\r\n|\n|\r/', $raw) as $line) {
        if (strpos($line, ':') === false) {
            continue;
        }
        [$name, $value] = explode(':', $line, 2);
        $name = strtolower(trim($name));
        $headers[$name][] = trim($value);
    }
    return $headers;
}

function site_audit_header(array $headers, string $name): string
{
    $values = $headers[strtolower($name)] ?? [];
    return $values[0] ?? '';
}

function site_audit_all_headers(array $headers, string $name): array
{
    return $headers[strtolower($name)] ?? [];
}

function site_audit_dom(string $html): ?DOMDocument
{
    if ($html === '') {
        return null;
    }
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    if (!$dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR)) {
        libxml_clear_errors();
        return null;
    }
    libxml_clear_errors();
    return $dom;
}

function site_audit_meta_content(?DOMDocument $dom, string $name): string
{
    if (!$dom) {
        return '';
    }
    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='" . strtolower($name) . "']");
    if ($nodes && $nodes->length > 0) {
        return trim($nodes->item(0)->getAttribute('content'));
    }
    return '';
}

function site_audit_meta_property(?DOMDocument $dom, string $property): string
{
    if (!$dom) {
        return '';
    }
    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query("//meta[@property='" . $property . "']");
    if ($nodes && $nodes->length > 0) {
        return trim($nodes->item(0)->getAttribute('content'));
    }
    return '';
}

function site_audit_text(?DOMDocument $dom, string $tag): array
{
    if (!$dom) {
        return [];
    }
    $items = [];
    foreach ($dom->getElementsByTagName($tag) as $node) {
        $items[] = trim(preg_replace('/\s+/', ' ', $node->textContent ?? ''));
    }
    return array_values(array_filter($items, fn($v) => $v !== ''));
}

function site_audit_links(?DOMDocument $dom, string $baseUrl): array
{
    if (!$dom) {
        return ['internal' => 0, 'external' => 0, 'nofollow' => 0, 'missing_href' => 0];
    }
    $baseHost = parse_url($baseUrl, PHP_URL_HOST);
    $internal = 0;
    $external = 0;
    $nofollow = 0;
    $missing  = 0;

    foreach ($dom->getElementsByTagName('a') as $a) {
        $href = trim($a->getAttribute('href'));
        if ($href === '' || str_starts_with($href, '#')) {
            $missing++;
            continue;
        }
        if (str_starts_with($href, '//')) {
            $href = 'https:' . $href;
        } elseif (str_starts_with($href, '/')) {
            $parts = parse_url($baseUrl);
            $href  = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '') . $href;
        }
        $host = parse_url($href, PHP_URL_HOST);
        if ($host && $baseHost && strcasecmp($host, $baseHost) === 0) {
            $internal++;
        } else {
            $external++;
        }
        $rel = strtolower($a->getAttribute('rel'));
        if (str_contains($rel, 'nofollow')) {
            $nofollow++;
        }
    }

    return [
        'internal'       => $internal,
        'external'       => $external,
        'nofollow'       => $nofollow,
        'missing_href'   => $missing,
    ];
}

function site_audit_images(?DOMDocument $dom): array
{
    if (!$dom) {
        return ['total' => 0, 'missing_alt' => 0];
    }
    $total = 0;
    $missingAlt = 0;
    foreach ($dom->getElementsByTagName('img') as $img) {
        $total++;
        if (trim($img->getAttribute('alt')) === '') {
            $missingAlt++;
        }
    }
    return ['total' => $total, 'missing_alt' => $missingAlt];
}

function site_audit_seo_report(array $fetch): array
{
    $dom   = site_audit_dom($fetch['body']);
    $title = site_audit_text($dom, 'title')[0] ?? '';
    $h1s   = site_audit_text($dom, 'h1');
    $h2s   = site_audit_text($dom, 'h2');
    $links = site_audit_links($dom, $fetch['url']);
    $imgs  = site_audit_images($dom);
    $text  = trim(preg_replace('/\s+/', ' ', strip_tags($fetch['body'])));

    $checks = [
        site_audit_check('HTTP status OK', $fetch['status'] >= 200 && $fetch['status'] < 400, 'Status ' . $fetch['status']),
        site_audit_check('Title tag present', $title !== '', $title !== '' ? mb_substr($title, 0, 80) : 'Missing'),
        site_audit_check('Title length (30–60 chars)', mb_strlen($title) >= 30 && mb_strlen($title) <= 60, (string) mb_strlen($title) . ' characters'),
        site_audit_check('Meta description', site_audit_meta_content($dom, 'description') !== '', mb_substr(site_audit_meta_content($dom, 'description'), 0, 100)),
        site_audit_check('Single H1 tag', count($h1s) === 1, count($h1s) . ' found'),
        site_audit_check('Canonical URL', site_audit_link_href($dom, 'canonical') !== '', site_audit_link_href($dom, 'canonical')),
        site_audit_check('Viewport meta', site_audit_meta_content($dom, 'viewport') !== '', 'Mobile-friendly signal'),
        site_audit_check('Open Graph title', site_audit_meta_property($dom, 'og:title') !== '', ''),
        site_audit_check('Images have alt text', $imgs['total'] === 0 || $imgs['missing_alt'] === 0, $imgs['missing_alt'] . ' missing alt of ' . $imgs['total']),
    ];

    $score = site_audit_score($checks);

    return [
        'type'    => 'seo',
        'url'     => $fetch['url'],
        'score'   => $score,
        'summary' => site_audit_grade($score),
        'metrics' => [
            'response_ms'   => $fetch['time_ms'],
            'page_size_kb'=> round($fetch['size_bytes'] / 1024, 1),
            'word_count'    => str_word_count($text),
            'h1_count'      => count($h1s),
            'h2_count'      => count($h2s),
            'internal_links'=> $links['internal'],
            'external_links'=> $links['external'],
        ],
        'meta' => [
            'title'       => $title,
            'description' => site_audit_meta_content($dom, 'description'),
            'canonical'   => site_audit_link_href($dom, 'canonical'),
            'robots'      => site_audit_meta_content($dom, 'robots'),
            'og_title'    => site_audit_meta_property($dom, 'og:title'),
            'og_image'    => site_audit_meta_property($dom, 'og:image'),
        ],
        'headings' => ['h1' => $h1s, 'h2' => array_slice($h2s, 0, 10)],
        'checks'   => $checks,
    ];
}

function site_audit_link_href(?DOMDocument $dom, string $rel): string
{
    if (!$dom) {
        return '';
    }
    foreach ($dom->getElementsByTagName('link') as $link) {
        if (strtolower($link->getAttribute('rel')) === strtolower($rel)) {
            return trim($link->getAttribute('href'));
        }
    }
    return '';
}

function site_audit_vapt_report(array $fetch): array
{
    $headers = $fetch['headers'];
    $dom     = site_audit_dom($fetch['body']);

    $securityHeaders = [
        'strict-transport-security' => site_audit_header($headers, 'Strict-Transport-Security'),
        'content-security-policy'   => site_audit_header($headers, 'Content-Security-Policy'),
        'x-frame-options'           => site_audit_header($headers, 'X-Frame-Options'),
        'x-content-type-options'    => site_audit_header($headers, 'X-Content-Type-Options'),
        'referrer-policy'           => site_audit_header($headers, 'Referrer-Policy'),
        'permissions-policy'        => site_audit_header($headers, 'Permissions-Policy'),
    ];

    $checks = [
        site_audit_check('HTTPS used', str_starts_with(strtolower($fetch['url']), 'https'), parse_url($fetch['url'], PHP_URL_SCHEME) ?? ''),
        site_audit_check('HSTS header', $securityHeaders['strict-transport-security'] !== '', $securityHeaders['strict-transport-security'] ?: 'Missing'),
        site_audit_check('Content-Security-Policy', $securityHeaders['content-security-policy'] !== '', $securityHeaders['content-security-policy'] ? 'Present' : 'Missing'),
        site_audit_check('X-Frame-Options', $securityHeaders['x-frame-options'] !== '', $securityHeaders['x-frame-options'] ?: 'Missing — clickjacking risk'),
        site_audit_check('X-Content-Type-Options', $securityHeaders['x-content-type-options'] !== '', $securityHeaders['x-content-type-options'] ?: 'Missing'),
        site_audit_check('Referrer-Policy', $securityHeaders['referrer-policy'] !== '', $securityHeaders['referrer-policy'] ?: 'Missing'),
        site_audit_check('Server header hidden', site_audit_header($headers, 'Server') === '', site_audit_header($headers, 'Server') ?: 'Hidden'),
        site_audit_check('X-Powered-By hidden', site_audit_header($headers, 'X-Powered-By') === '', site_audit_header($headers, 'X-Powered-By') ?: 'Hidden'),
        site_audit_check('No mixed content hints', !preg_match('/http:\/\//i', $fetch['body']), 'Scan HTML for http:// assets'),
    ];

    $cookies = site_audit_all_headers($headers, 'set-cookie');
    $secureCookies = 0;
    foreach ($cookies as $cookie) {
        if (stripos($cookie, 'secure') !== false) {
            $secureCookies++;
        }
    }
    if (count($cookies) > 0) {
        $checks[] = site_audit_check('Secure cookie flag', $secureCookies === count($cookies), $secureCookies . '/' . count($cookies) . ' cookies secure');
    }

    $forms = $dom ? $dom->getElementsByTagName('form')->length : 0;
    $score = site_audit_score($checks);

    return [
        'type'    => 'vapt',
        'url'     => $fetch['url'],
        'score'   => $score,
        'summary' => site_audit_grade($score),
        'metrics' => [
            'http_status'    => $fetch['status'],
            'response_ms'    => $fetch['time_ms'],
            'forms_found'    => $forms,
            'cookies_set'    => count($cookies),
            'redirects'      => $fetch['redirects'],
        ],
        'headers' => $securityHeaders,
        'server'  => [
            'server'         => site_audit_header($headers, 'Server'),
            'x_powered_by'   => site_audit_header($headers, 'X-Powered-By'),
        ],
        'checks'  => $checks,
        'disclaimer' => 'Automated surface scan only — not a full penetration test.',
    ];
}

function site_audit_lighthouse_report(array $fetch): array
{
    $dom   = site_audit_dom($fetch['body']);
    $title = site_audit_text($dom, 'title')[0] ?? '';
    $imgs  = site_audit_images($dom);
    $sizeKb = round($fetch['size_bytes'] / 1024, 1);

    $perfChecks = [
        site_audit_check('Response time under 1s', $fetch['time_ms'] < 1000, $fetch['time_ms'] . ' ms'),
        site_audit_check('Page size under 1.5 MB', $fetch['size_bytes'] < 1572864, $sizeKb . ' KB'),
        site_audit_check('Compression likely', str_contains(strtolower($fetch['content_type']), 'text/html'), $fetch['content_type']),
    ];
    $a11yChecks = [
        site_audit_check('Document has title', $title !== '', ''),
        site_audit_check('Images have alt text', $imgs['total'] === 0 || $imgs['missing_alt'] === 0, $imgs['missing_alt'] . ' missing'),
        site_audit_check('HTML lang attribute', preg_match('/<html[^>]+lang=/i', $fetch['body']) === 1, ''),
    ];
    $bestChecks = [
        site_audit_check('Uses HTTPS', str_starts_with(strtolower($fetch['url']), 'https'), ''),
        site_audit_check('Has viewport meta', site_audit_meta_content($dom, 'viewport') !== '', ''),
        site_audit_check('No document.write in HTML', stripos($fetch['body'], 'document.write') === false, ''),
    ];
    $seoChecks = site_audit_seo_report($fetch)['checks'];

    $categories = [
        'performance'    => site_audit_score($perfChecks),
        'accessibility'  => site_audit_score($a11yChecks),
        'best_practices' => site_audit_score($bestChecks),
        'seo'            => site_audit_score($seoChecks),
    ];

    $overall = (int) round(array_sum($categories) / count($categories));

    return [
        'type'       => 'lighthouse',
        'url'        => $fetch['url'],
        'score'      => $overall,
        'summary'    => site_audit_grade($overall),
        'categories' => $categories,
        'metrics'    => [
            'response_ms' => $fetch['time_ms'],
            'size_kb'     => $sizeKb,
            'status'      => $fetch['status'],
        ],
        'checks' => [
            'performance'    => $perfChecks,
            'accessibility'  => $a11yChecks,
            'best_practices' => $bestChecks,
            'seo'            => array_slice($seoChecks, 0, 8),
        ],
        'disclaimer' => 'Simplified audit inspired by Lighthouse — not the official Google Lighthouse engine.',
    ];
}

function site_audit_check(string $label, bool $pass, string $detail = ''): array
{
    return [
        'label'  => $label,
        'pass'   => $pass,
        'detail' => $detail,
        'status' => $pass ? 'pass' : 'fail',
    ];
}

function site_audit_score(array $checks): int
{
    if (count($checks) === 0) {
        return 0;
    }
    $pass = 0;
    foreach ($checks as $check) {
        if (!empty($check['pass'])) {
            $pass++;
        }
    }
    return (int) round(($pass / count($checks)) * 100);
}

function site_audit_grade(int $score): string
{
    if ($score >= 90) return 'Excellent';
    if ($score >= 75) return 'Good';
    if ($score >= 50) return 'Needs improvement';
    return 'Poor';
}

function site_audit_run(string $url, string $type): array
{
    $type = strtolower(trim($type));
    $allowed = ['seo', 'vapt', 'lighthouse'];
    if (!in_array($type, $allowed, true)) {
        throw new InvalidArgumentException('Report type must be seo, vapt, or lighthouse.');
    }

    $fetch = site_audit_fetch($url);

    return match ($type) {
        'seo'         => site_audit_seo_report($fetch),
        'vapt'        => site_audit_vapt_report($fetch),
        'lighthouse'  => site_audit_lighthouse_report($fetch),
    };
}
