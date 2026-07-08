<?php
/**
 * Email configuration tester — send one test message; credentials are never stored.
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

function email_test_json_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function email_test_timeout(): int
{
    return defined('EMAIL_TEST_TIMEOUT') ? max(5, (int) EMAIL_TEST_TIMEOUT) : 30;
}

function email_test_max_body(): int
{
    return defined('EMAIL_TEST_MAX_BODY_BYTES') ? max(1024, (int) EMAIL_TEST_MAX_BODY_BYTES) : 65536;
}

function email_test_validate_address(string $email, string $label): string
{
    $email = trim($email);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Invalid ' . $label . ' address.');
    }
    return $email;
}

/**
 * @param array<string, mixed> $data
 * @return array{ok: bool, transport: string, message: string, log: string[], time_ms: int}
 */
function email_test_execute(array $data): array
{
    $start     = microtime(true);
    $transport = strtolower(trim((string) ($data['transport'] ?? 'smtp')));
    $fromEmail = email_test_validate_address((string) ($data['from_email'] ?? ''), 'From');
    $toEmail   = email_test_validate_address((string) ($data['to_email'] ?? ''), 'To');
    $fromName  = trim((string) ($data['from_name'] ?? ''));
    $subject   = trim((string) ($data['subject'] ?? 'Test email'));
    $body      = (string) ($data['body'] ?? '');
    $isHtml    = !empty($data['is_html']);

    if ($subject === '') {
        $subject = 'Email configuration test';
    }

    if (strlen($body) > email_test_max_body()) {
        throw new InvalidArgumentException('Message body is too large.');
    }

    if ($body === '') {
        $body = 'This is a test email from ' . (defined('SITE_DOMAIN') ? SITE_DOMAIN : 'usetoolsweb') . ' Email Config Tester.';
    }

    if ($transport === 'mail') {
        $result = email_test_send_mail($fromEmail, $fromName, $toEmail, $subject, $body, $isHtml);
    } elseif ($transport === 'smtp') {
        $result = email_test_send_smtp($data, $fromEmail, $fromName, $toEmail, $subject, $body, $isHtml);
    } else {
        throw new InvalidArgumentException('Unknown transport. Use smtp or mail.');
    }

    $result['time_ms'] = (int) round((microtime(true) - $start) * 1000);
    return $result;
}

/**
 * @return array{ok: bool, transport: string, message: string, log: string[]}
 */
function email_test_send_mail(
    string $fromEmail,
    string $fromName,
    string $toEmail,
    string $subject,
    string $body,
    bool $isHtml
): array {
    $log = ['Using PHP mail() on this server (not your SMTP settings).'];

    $fromHeader = $fromName !== ''
        ? $fromName . ' <' . $fromEmail . '>'
        : $fromEmail;

    $headers = [
        'From: ' . $fromHeader,
        'Reply-To: ' . $fromEmail,
        'MIME-Version: 1.0',
        'X-Mailer: PHP/' . PHP_VERSION,
    ];

    if ($isHtml) {
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
    } else {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    }

    $ok = @mail($toEmail, $subject, $body, implode("\r\n", $headers));

    if (!$ok) {
        $log[] = 'mail() returned false. Check server sendmail/postfix configuration.';
        return [
            'ok'        => false,
            'transport' => 'mail',
            'message'   => 'PHP mail() failed. Your host may block mail() or require SMTP instead.',
            'log'       => $log,
        ];
    }

    $log[] = 'mail() accepted the message for delivery.';
    return [
        'ok'        => true,
        'transport' => 'mail',
        'message'   => 'Test email queued via PHP mail(). Check the inbox (and spam folder).',
        'log'       => $log,
    ];
}

/**
 * @param array<string, mixed> $data
 * @return array{ok: bool, transport: string, message: string, log: string[]}
 */
function email_test_send_smtp(
    array $data,
    string $fromEmail,
    string $fromName,
    string $toEmail,
    string $subject,
    string $body,
    bool $isHtml
): array {
    $host        = trim((string) ($data['smtp_host'] ?? ''));
    $port        = (int) ($data['smtp_port'] ?? 587);
    $encryption  = strtolower(trim((string) ($data['smtp_encryption'] ?? 'tls')));
    $username    = (string) ($data['smtp_username'] ?? '');
    $password    = (string) ($data['smtp_password'] ?? '');
    $auth        = array_key_exists('smtp_auth', $data) ? (bool) $data['smtp_auth'] : true;

    if ($host === '') {
        throw new InvalidArgumentException('SMTP host is required.');
    }

    if ($port < 1 || $port > 65535) {
        throw new InvalidArgumentException('SMTP port must be between 1 and 65535.');
    }

    if (!in_array($encryption, ['none', 'tls', 'ssl'], true)) {
        throw new InvalidArgumentException('Encryption must be none, tls, or ssl.');
    }

    if ($auth && ($username === '' || $password === '')) {
        throw new InvalidArgumentException('SMTP username and password are required when authentication is enabled.');
    }

    $client = new EmailTestSmtpClient($host, $port, $encryption, email_test_timeout());

    try {
        $client->connect();
        $client->ehlo();

        if ($encryption === 'tls') {
            $client->startTls();
            $client->ehlo();
        }

        if ($auth) {
            $client->authLogin($username, $password);
        }

        $client->mailFrom($fromEmail);
        $client->rcptTo($toEmail);
        $client->data($fromEmail, $fromName, $toEmail, $subject, $body, $isHtml);
        $client->quit();

        return [
            'ok'        => true,
            'transport' => 'smtp',
            'message'   => 'SMTP server accepted the test email. Check the inbox (and spam folder).',
            'log'       => $client->getLog(),
        ];
    } catch (Throwable $e) {
        try {
            $client->quit();
        } catch (Throwable $ignored) {
        }

        return [
            'ok'        => false,
            'transport' => 'smtp',
            'message'   => $e->getMessage(),
            'log'       => $client->getLog(),
        ];
    }
}

final class EmailTestSmtpClient
{
    private string $host;
    private int $port;
    private string $encryption;
    private int $timeout;
    /** @var resource|null */
    private $socket = null;
    /** @var string[] */
    private array $log = [];

    public function __construct(string $host, int $port, string $encryption, int $timeout)
    {
        $this->host        = $host;
        $this->port        = $port;
        $this->encryption  = $encryption;
        $this->timeout     = $timeout;
    }

    /** @return string[] */
    public function getLog(): array
    {
        return $this->log;
    }

    public function connect(): void
    {
        $remote = $this->encryption === 'ssl'
            ? 'ssl://' . $this->host . ':' . $this->port
            : 'tcp://' . $this->host . ':' . $this->port;

        $errno  = 0;
        $errstr = '';
        $socket = @stream_socket_client(
            $remote,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]])
        );

        if ($socket === false) {
            throw new RuntimeException('Could not connect to SMTP server: ' . ($errstr ?: 'connection failed'));
        }

        stream_set_timeout($socket, $this->timeout);
        $this->socket = $socket;
        $this->readResponse([220]);
        $this->log[] = 'Connected to ' . $this->host . ':' . $this->port;
    }

    public function ehlo(): void
    {
        $name = defined('SITE_DOMAIN') ? SITE_DOMAIN : 'localhost';
        $this->sendCommand('EHLO ' . $name, [250]);
    }

    public function startTls(): void
    {
        $this->sendCommand('STARTTLS', [220]);
        if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('STARTTLS negotiation failed.');
        }
        $this->log[] = 'TLS enabled.';
    }

    public function authLogin(string $username, string $password): void
    {
        $this->sendCommand('AUTH LOGIN', [334]);
        $this->sendCommand(base64_encode($username), [334], true);
        $this->sendCommand(base64_encode($password), [235], true);
        $this->log[] = 'Authentication successful.';
    }

    public function mailFrom(string $fromEmail): void
    {
        $this->sendCommand('MAIL FROM:<' . $fromEmail . '>', [250]);
    }

    public function rcptTo(string $toEmail): void
    {
        $this->sendCommand('RCPT TO:<' . $toEmail . '>', [250, 251]);
    }

    public function data(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $subject,
        string $body,
        bool $isHtml
    ): void {
        $this->sendCommand('DATA', [354]);

        $fromHeader = $fromName !== ''
            ? $fromName . ' <' . $fromEmail . '>'
            : $fromEmail;

        $lines = [
            'Date: ' . gmdate('D, d M Y H:i:s') . ' +0000',
            'From: ' . $fromHeader,
            'To: <' . $toEmail . '>',
            'Subject: ' . email_test_encode_header($subject),
            'MIME-Version: 1.0',
            $isHtml
                ? 'Content-Type: text/html; charset=UTF-8'
                : 'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            $body,
        ];

        $payload = implode("\r\n", $lines);
        $payload = preg_replace('/^\./m', '..', $payload) ?? $payload;
        $this->write($payload . "\r\n.\r\n");
        $this->readResponse([250]);
        $this->log[] = 'Message body sent.';
    }

    public function quit(): void
    {
        try {
            $this->sendCommand('QUIT', [221]);
        } catch (Throwable $e) {
            // Connection may already be closing.
        }
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
        $this->socket = null;
    }

    private function sendCommand(string $command, array $okCodes, bool $redact = false): void
    {
        $this->log[] = 'C: ' . ($redact ? '[credentials hidden]' : $command);
        $this->write($command . "\r\n");
        $this->readResponse($okCodes);
    }

    /** @param int[] $okCodes */
    private function readResponse(array $okCodes): void
    {
        $response = '';
        while (!feof($this->socket)) {
            $line = fgets($this->socket);
            if ($line === false) {
                break;
            }
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        $response = trim($response);
        if ($response === '') {
            throw new RuntimeException('Empty SMTP response.');
        }

        $this->log[] = 'S: ' . $response;
        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $okCodes, true)) {
            throw new RuntimeException('SMTP error: ' . $response);
        }
    }

    private function write(string $data): void
    {
        if (!is_resource($this->socket)) {
            throw new RuntimeException('SMTP socket is not connected.');
        }
        $written = fwrite($this->socket, $data);
        if ($written === false) {
            throw new RuntimeException('Failed to write to SMTP socket.');
        }
    }
}

function email_test_encode_header(string $value): string
{
    if (preg_match('/[^\x20-\x7E]/', $value)) {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }
    return $value;
}
