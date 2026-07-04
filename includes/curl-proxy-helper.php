<?php
/**
 * cURL proxy helper — SSRF-safe server-side HTTP requests.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

function curl_proxy_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function curl_proxy_is_private_ip(string $ip): bool
{
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return true;
    }

    return false;
}

function curl_proxy_validate_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        curl_proxy_json_error(400, 'URL is required.');
    }

    $parts = parse_url($url);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
        curl_proxy_json_error(400, 'Invalid URL.');
    }

    $scheme = strtolower($parts['scheme']);
    if (!in_array($scheme, ['http', 'https'], true)) {
        curl_proxy_json_error(400, 'Only http and https URLs are allowed.');
    }

    $host = strtolower($parts['host']);

    if (in_array($host, ['localhost', 'localhost.localdomain'], true)) {
        curl_proxy_json_error(403, 'Requests to localhost are not allowed.');
    }

    if (filter_var($host, FILTER_VALIDATE_IP)) {
        if (curl_proxy_is_private_ip($host)) {
            curl_proxy_json_error(403, 'Requests to private or reserved IP addresses are not allowed.');
        }
        return $url;
    }

    $resolved = @gethostbynamel($host);
    if ($resolved === false || count($resolved) === 0) {
        curl_proxy_json_error(400, 'Could not resolve host: ' . $host);
    }

    foreach ($resolved as $ip) {
        if (curl_proxy_is_private_ip($ip)) {
            curl_proxy_json_error(403, 'Requests to private or internal hosts are not allowed.');
        }
    }

    return $url;
}

function curl_proxy_execute(string $method, string $url, array $headers, ?string $body): array
{
    if (!function_exists('curl_init')) {
        curl_proxy_json_error(503, 'PHP cURL extension is not enabled on this server.');
    }

    $url = curl_proxy_validate_url($url);

    $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
    $method = strtoupper($method);
    if (!in_array($method, $allowedMethods, true)) {
        curl_proxy_json_error(400, 'Unsupported HTTP method.');
    }

    $skipHeaders = ['host', 'content-length', 'transfer-encoding', 'connection'];
    $curlHeaders = [];
    foreach ($headers as $name => $value) {
        $lower = strtolower(trim((string) $name));
        if ($lower === '' || in_array($lower, $skipHeaders, true)) {
            continue;
        }
        $curlHeaders[] = trim($name) . ': ' . trim((string) $value);
    }

    $ch = curl_init($url);
    if ($ch === false) {
        curl_proxy_json_error(500, 'Could not initialize cURL.');
    }

    $responseHeaders = [];
    $headerFn = function ($ch, $line) use (&$responseHeaders) {
        $len = strlen($line);
        $trimmed = trim($line);
        if ($trimmed === '' || stripos($trimmed, 'HTTP/') === 0) {
            return $len;
        }
        $idx = strpos($trimmed, ':');
        if ($idx !== false) {
            $name = trim(substr($trimmed, 0, $idx));
            $value = trim(substr($trimmed, $idx + 1));
            $responseHeaders[$name] = $value;
        }
        return $len;
    };

    $timeout = defined('CURL_PROXY_TIMEOUT') ? (int) CURL_PROXY_TIMEOUT : 30;
    $maxBytes = defined('CURL_PROXY_MAX_BYTES') ? (int) CURL_PROXY_MAX_BYTES : 10485760;

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => min(15, $timeout),
        CURLOPT_HTTPHEADER     => $curlHeaders,
        CURLOPT_HEADERFUNCTION => $headerFn,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT      => 'usetoolsweb-curl-runner/1.0',
    ]);

    if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true) && $body !== null && $body !== '') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    if ($method === 'HEAD') {
        curl_setopt($ch, CURLOPT_NOBODY, true);
    }

    $start = microtime(true);
    $responseBody = curl_exec($ch);
    $elapsed = (int) round((microtime(true) - $start) * 1000);

    if ($responseBody === false) {
        $err = curl_error($ch);
        curl_close($ch);
        curl_proxy_json_error(502, 'Request failed: ' . ($err ?: 'unknown error'));
    }

    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (strlen($responseBody) > $maxBytes) {
        curl_proxy_json_error(413, 'Response exceeds maximum size of ' . (int) ($maxBytes / (1024 * 1024)) . ' MB.');
    }

    return [
        'ok'         => true,
        'status'     => $status,
        'statusText' => curl_proxy_status_text($status),
        'time_ms'    => $elapsed,
        'headers'    => $responseHeaders,
        'body'       => $responseBody,
    ];
}

function curl_proxy_status_text(int $code): string
{
    static $map = [
        200 => 'OK', 201 => 'Created', 204 => 'No Content',
        400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden',
        404 => 'Not Found', 405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity', 429 => 'Too Many Requests',
        500 => 'Internal Server Error', 502 => 'Bad Gateway', 503 => 'Service Unavailable',
    ];
    return $map[$code] ?? 'HTTP ' . $code;
}
