<?php
/**
 * Contact form — send via site SMTP (credentials in config/smtp.local.php).
 */

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

require_once __DIR__ . '/email-test-helper.php';

function contact_smtp_configured(): bool
{
    return defined('SMTP_HOST') && SMTP_HOST !== ''
        && defined('SMTP_USERNAME') && SMTP_USERNAME !== ''
        && defined('SMTP_PASSWORD') && SMTP_PASSWORD !== '';
}

function contact_send_message(string $name, string $email, string $message): void
{
    if (!contact_smtp_configured()) {
        throw new RuntimeException('Contact email is not configured on the server.');
    }

    $name    = trim($name);
    $email   = trim($email);
    $message = trim($message);

    if ($name === '' || $email === '' || $message === '') {
        throw new InvalidArgumentException('Please fill in all fields.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Please enter a valid email address.');
    }
    if (strlen($message) > 8000) {
        throw new InvalidArgumentException('Message is too long.');
    }

    $to      = defined('CONTACT_TO_EMAIL') ? CONTACT_TO_EMAIL : SITE_EMAIL;
    $from    = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : SITE_EMAIL;
    $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : SITE_NAME;
    $subject = 'Contact form — ' . SITE_DOMAIN . ' — ' . $name;

    $body = "New message from the contact form on " . SITE_DOMAIN . "\n\n"
        . "Name: " . $name . "\n"
        . "Email: " . $email . "\n\n"
        . "Message:\n" . $message . "\n";

    $host       = SMTP_HOST;
    $port       = (int) (defined('SMTP_PORT') ? SMTP_PORT : 465);
    $encryption = defined('SMTP_ENCRYPTION') ? strtolower((string) SMTP_ENCRYPTION) : 'ssl';

    $client = new EmailTestSmtpClient($host, $port, $encryption, email_test_timeout());

    try {
        $client->connect();
        $client->ehlo();
        if ($encryption === 'tls') {
            $client->startTls();
            $client->ehlo();
        }
        $client->authLogin(SMTP_USERNAME, SMTP_PASSWORD);
        $client->mailFrom($from);
        $client->rcptTo($to);
        $client->data($from, $fromName, $to, $subject, $body, false);
        $client->quit();
    } catch (Throwable $e) {
        try {
            $client->quit();
        } catch (Throwable $ignored) {
        }
        throw new RuntimeException('Could not send your message. Please email us directly at ' . SITE_EMAIL . '.');
    }
}
