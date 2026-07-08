<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Email Config Tester',
    'Test SMTP or PHP mail settings — SendGrid, Gmail, Mailgun, Amazon SES, and more. Verify email delivery from your server. We do not save your credentials.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>Email Config Tester</h1>
        <p>Send a one-time test email using your SMTP or server mail settings. Works with SendGrid, Gmail, Mailgun, Amazon SES, Outlook, and custom SMTP hosts.</p>
    </div>

    <div class="tool-panel email-test-panel">
        <div class="alert alert-info email-test-privacy">
            <strong>We do not save anything.</strong> Your SMTP password, API keys, and message content are used only to send this test email. Nothing is written to disk, database, or logs on our server.
        </div>

        <section class="email-test-section">
            <h2 class="email-test-section-title">1. Choose provider</h2>
            <label for="email-preset">Quick preset</label>
            <select id="email-preset">
                <option value="custom">Custom SMTP</option>
                <option value="sendgrid">SendGrid</option>
                <option value="gmail">Gmail / Google Workspace</option>
                <option value="mailgun">Mailgun</option>
                <option value="ses">Amazon SES</option>
                <option value="outlook">Outlook / Office 365</option>
                <option value="brevo">Brevo (Sendinblue)</option>
            </select>
            <p id="email-preset-hint" class="hint email-preset-hint"></p>
        </section>

        <section class="email-test-section">
            <h2 class="email-test-section-title">2. Transport</h2>
            <label class="radio-label">
                <input type="radio" name="email-transport" value="smtp" checked> SMTP <span class="hint">(SendGrid, Gmail, Mailgun, etc.)</span>
            </label>
            <label class="radio-label">
                <input type="radio" name="email-transport" value="mail"> PHP mail() <span class="hint">(uses this server’s default mail — not your SMTP)</span>
            </label>
        </section>

        <section id="email-smtp-section" class="email-test-section">
            <h2 class="email-test-section-title">3. SMTP settings</h2>
            <div class="email-test-grid">
                <div class="email-test-full">
                    <label for="email-smtp-host">SMTP host</label>
                    <input type="text" id="email-smtp-host" placeholder="smtp.sendgrid.net" autocomplete="off">
                </div>
                <div>
                    <label for="email-smtp-port">Port</label>
                    <input type="number" id="email-smtp-port" min="1" max="65535" value="587">
                </div>
                <div>
                    <label for="email-smtp-encryption">Encryption</label>
                    <select id="email-smtp-encryption">
                        <option value="tls" selected>TLS (STARTTLS)</option>
                        <option value="ssl">SSL</option>
                        <option value="none">None</option>
                    </select>
                </div>
                <div class="email-test-full">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="email-smtp-auth" checked> Use SMTP authentication
                    </label>
                </div>
                <div id="email-smtp-auth-fields" class="email-test-auth-fields email-test-full">
                    <div class="email-test-grid">
                        <div>
                            <label for="email-smtp-user">SMTP username</label>
                            <input type="text" id="email-smtp-user" placeholder="apikey or your@email.com" autocomplete="off">
                        </div>
                        <div>
                            <label for="email-smtp-pass">SMTP password / API key</label>
                            <div class="email-pass-wrap">
                                <input type="password" id="email-smtp-pass" placeholder="SMTP password or API key" autocomplete="new-password">
                                <button type="button" class="btn btn-secondary btn-sm" id="btn-email-pass-toggle" aria-label="Show password">Show</button>
                            </div>
                        </div>
                    </div>
                </div>
                <p id="email-smtp-no-auth-hint" class="hint email-test-full hidden">Authentication is off — username and password are not required.</p>
            </div>
        </section>

        <section class="email-test-section">
            <h2 class="email-test-section-title">4. Test message</h2>
            <div class="email-test-grid">
                <div>
                    <label for="email-from">From email</label>
                    <input type="email" id="email-from" placeholder="sender@yourdomain.com" autocomplete="off">
                </div>
                <div>
                    <label for="email-from-name">From name <span class="hint">(optional)</span></label>
                    <input type="text" id="email-from-name" placeholder="My App">
                </div>
                <div class="email-test-full">
                    <label for="email-to">Send test to</label>
                    <input type="email" id="email-to" placeholder="you@example.com" autocomplete="off">
                </div>
                <div class="email-test-full">
                    <label for="email-subject">Subject</label>
                    <input type="text" id="email-subject" value="Email configuration test">
                </div>
                <div class="email-test-full">
                    <label for="email-body">Message</label>
                    <textarea id="email-body" rows="5" placeholder="This is a test email to verify SMTP settings."></textarea>
                </div>
                <div class="email-test-full">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="email-is-html"> Send as HTML
                    </label>
                </div>
            </div>
        </section>

        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="btn-email-test">Send test email</button>
            <button type="button" class="btn btn-secondary" id="btn-email-clear">Clear form</button>
        </div>

        <div id="email-test-error" class="alert alert-error hidden"></div>
        <div id="email-test-success" class="alert alert-success hidden"></div>

        <section id="email-test-result" class="email-test-result hidden">
            <h2 class="email-test-section-title">SMTP conversation</h2>
            <p class="hint">Server responses from the test (passwords are never shown).</p>
            <pre id="email-test-log" class="email-test-log"></pre>
        </section>
    </div>

    <?php ad_slot(); ?>
</div>

<?php
$extra_scripts = '<script src="/assets/js/tools/email-config-tester.js"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
