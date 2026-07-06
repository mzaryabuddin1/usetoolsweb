<?php
/**
 * Google Analytics 4 Data API client (service account, no Composer).
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

function ga4_base64url(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function ga4_get_access_token(string $credentialsFile): string
{
    if (!is_file($credentialsFile)) {
        throw new RuntimeException('GA4 credentials file not found: ' . $credentialsFile);
    }

    $creds = json_decode(file_get_contents($credentialsFile), true);
    if (!is_array($creds) || empty($creds['client_email']) || empty($creds['private_key'])) {
        throw new RuntimeException('Invalid GA4 service account JSON.');
    }

    $now = time();
    $header = ga4_base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $claim  = ga4_base64url(json_encode([
        'iss'   => $creds['client_email'],
        'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    ]));

    $input = $header . '.' . $claim;
    $key   = openssl_pkey_get_private($creds['private_key']);
    if (!$key) {
        throw new RuntimeException('Could not parse GA4 private key.');
    }

    $signature = '';
    if (!openssl_sign($input, $signature, $key, OPENSSL_ALGO_SHA256)) {
        throw new RuntimeException('Failed to sign GA4 JWT.');
    }

    $jwt = $input . '.' . ga4_base64url($signature);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]),
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $code !== 200) {
        throw new RuntimeException('GA4 token request failed (HTTP ' . $code . ').');
    }

    $data = json_decode($response, true);
    if (empty($data['access_token'])) {
        throw new RuntimeException('GA4 token response missing access_token.');
    }

    return $data['access_token'];
}

/**
 * Fetch page views grouped by page path for the lookback window.
 *
 * @return array<int, array{path: string, views: int}>
 */
function ga4_fetch_page_views(string $propertyId, string $credentialsFile, int $days = 7, int $limit = 50): array
{
    $propertyId = preg_replace('/\D/', '', $propertyId);
    if ($propertyId === '') {
        throw new RuntimeException('GA4_PROPERTY_ID is not set in config.php.');
    }

    $token = ga4_get_access_token($credentialsFile);
    $days  = max(1, min(90, $days));

    $body = [
        'dateRanges' => [[
            'startDate' => $days . 'daysAgo',
            'endDate'   => 'today',
        ]],
        'dimensions' => [['name' => 'pagePath']],
        'metrics'    => [['name' => 'screenPageViews']],
        'orderBys'   => [[
            'metric' => ['metricName' => 'screenPageViews'],
            'desc'   => true,
        ]],
        'limit' => $limit,
    ];

    $url = 'https://analyticsdata.googleapis.com/v1beta/properties/' . $propertyId . ':runReport';
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_TIMEOUT        => 45,
    ]);

    $response = curl_exec($ch);
    $code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('GA4 runReport request failed.');
    }

    $data = json_decode($response, true);
    if ($code !== 200) {
        $msg = $data['error']['message'] ?? ('HTTP ' . $code);
        throw new RuntimeException('GA4 API error: ' . $msg);
    }

    $rows = [];
    foreach ($data['rows'] ?? [] as $row) {
        $path  = $row['dimensionValues'][0]['value'] ?? '';
        $views = (int) ($row['metricValues'][0]['value'] ?? 0);
        if ($path !== '' && $views > 0) {
            $rows[] = ['path' => $path, 'views' => $views];
        }
    }

    return $rows;
}
