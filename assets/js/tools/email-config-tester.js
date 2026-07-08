(function () {
    'use strict';

    var $ = window.jQuery;

    var PRESETS = {
        custom: {
            hint: 'Enter your own SMTP host, port, and credentials.'
        },
        sendgrid: {
            host: 'smtp.sendgrid.net',
            port: 587,
            encryption: 'tls',
            username: 'apikey',
            hint: 'SendGrid: username is always apikey. Password is your SendGrid API key. Verify the From address in SendGrid.'
        },
        gmail: {
            host: 'smtp.gmail.com',
            port: 587,
            encryption: 'tls',
            username: '',
            hint: 'Gmail: use an App Password (Google Account → Security → App passwords). Regular password often fails.'
        },
        mailgun: {
            host: 'smtp.mailgun.org',
            port: 587,
            encryption: 'tls',
            username: '',
            hint: 'Mailgun: use SMTP credentials from your Mailgun domain settings (postmaster@...).'
        },
        ses: {
            host: 'email-smtp.us-east-1.amazonaws.com',
            port: 587,
            encryption: 'tls',
            username: '',
            hint: 'Amazon SES: use SMTP credentials from the SES console. Pick the region that matches your endpoint host.'
        },
        outlook: {
            host: 'smtp.office365.com',
            port: 587,
            encryption: 'tls',
            username: '',
            hint: 'Outlook / Office 365: use your mailbox email as username and account password or app password.'
        },
        brevo: {
            host: 'smtp-relay.brevo.com',
            port: 587,
            encryption: 'tls',
            username: '',
            hint: 'Brevo: use your Brevo login email as username and SMTP key as password.'
        }
    };

    function transport() {
        return $('input[name="email-transport"]:checked').val() || 'smtp';
    }

    function applyPreset(key) {
        var preset = PRESETS[key] || PRESETS.custom;
        $('#email-preset-hint').text(preset.hint || '');

        if (key === 'custom') {
            return;
        }

        $('#email-smtp-host').val(preset.host || '');
        $('#email-smtp-port').val(preset.port || 587);
        $('#email-smtp-encryption').val(preset.encryption || 'tls');
        $('#email-smtp-auth').prop('checked', true);
        $('#email-smtp-user').val(preset.username || '');
        $('#email-smtp-pass').val('');
    }

    function toggleSmtpSection() {
        var isSmtp = transport() === 'smtp';
        $('#email-smtp-section').toggleClass('hidden', !isSmtp);
        $('#email-preset').prop('disabled', !isSmtp);
        toggleAuthFields();
    }

    function toggleAuthFields() {
        var isSmtp = transport() === 'smtp';
        var useAuth = $('#email-smtp-auth').is(':checked');
        $('#email-smtp-auth-fields').toggleClass('hidden', !isSmtp || !useAuth);
        $('#email-smtp-no-auth-hint').toggleClass('hidden', !isSmtp || useAuth);
    }

    function showError(msg) {
        $('#email-test-error').text(msg).removeClass('hidden');
        $('#email-test-success').addClass('hidden');
    }

    function showSuccess(msg) {
        $('#email-test-success').text(msg).removeClass('hidden');
        $('#email-test-error').addClass('hidden');
    }

    function hideAlerts() {
        $('#email-test-error, #email-test-success').addClass('hidden');
    }

    function showLog(lines) {
        if (!lines || !lines.length) {
            $('#email-test-result').addClass('hidden');
            return;
        }
        $('#email-test-log').text(lines.join('\n'));
        $('#email-test-result').removeClass('hidden');
    }

    function buildPayload() {
        var payload = {
            transport: transport(),
            from_email: $.trim($('#email-from').val()),
            from_name: $.trim($('#email-from-name').val()),
            to_email: $.trim($('#email-to').val()),
            subject: $.trim($('#email-subject').val()),
            body: $('#email-body').val(),
            is_html: $('#email-is-html').is(':checked')
        };

        if (payload.transport === 'smtp') {
            payload.smtp_host = $.trim($('#email-smtp-host').val());
            payload.smtp_port = parseInt($('#email-smtp-port').val(), 10) || 587;
            payload.smtp_encryption = $('#email-smtp-encryption').val();
            payload.smtp_auth = $('#email-smtp-auth').is(':checked');
            payload.smtp_username = $('#email-smtp-user').val();
            payload.smtp_password = $('#email-smtp-pass').val();
        }

        return payload;
    }

    function validateClient(payload) {
        if (!payload.from_email) {
            return 'Enter a From email address.';
        }
        if (!payload.to_email) {
            return 'Enter a recipient email address.';
        }
        if (payload.transport === 'smtp' && !payload.smtp_host) {
            return 'Enter an SMTP host.';
        }
        if (payload.transport === 'smtp' && payload.smtp_auth && (!payload.smtp_username || !payload.smtp_password)) {
            return 'Enter SMTP username and password (or API key).';
        }
        return '';
    }

    async function sendTest() {
        hideAlerts();
        showLog([]);

        var payload = buildPayload();
        var err = validateClient(payload);
        if (err) {
            showError(err);
            return;
        }

        var $btn = $('#btn-email-test');
        $btn.prop('disabled', true).text('Sending…');

        try {
            var res = await fetch('/api/email-test.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            var data = await res.json();
            showLog(data.log || []);

            if (!res.ok || !data.ok) {
                showError(data.error || data.message || 'Email test failed.');
                return;
            }

            var msg = data.message || 'Test email sent.';
            if (data.time_ms) {
                msg += ' (' + data.time_ms + ' ms)';
            }
            showSuccess(msg);
        } catch (e) {
            showError(e.message || 'Request failed.');
        } finally {
            $btn.prop('disabled', false).text('Send test email');
        }
    }

    function clearForm() {
        $('#email-preset').val('custom');
        $('input[name="email-transport"][value="smtp"]').prop('checked', true);
        $('#email-smtp-host, #email-smtp-user, #email-from, #email-from-name, #email-to, #email-body').val('');
        $('#email-smtp-pass').val('');
        $('#email-smtp-port').val('587');
        $('#email-smtp-encryption').val('tls');
        $('#email-smtp-auth').prop('checked', true);
        $('#email-subject').val('Email configuration test');
        $('#email-is-html').prop('checked', false);
        applyPreset('custom');
        toggleSmtpSection();
        toggleAuthFields();
        hideAlerts();
        showLog([]);
    }

    $(function () {
        applyPreset($('#email-preset').val());
        toggleSmtpSection();
        toggleAuthFields();

        $('#email-preset').on('change', function () {
            applyPreset($(this).val());
        });

        $('input[name="email-transport"]').on('change', toggleSmtpSection);
        $('#email-smtp-auth').on('change', toggleAuthFields);

        $('#btn-email-pass-toggle').on('click', function () {
            var $pass = $('#email-smtp-pass');
            var showing = $pass.attr('type') === 'text';
            $pass.attr('type', showing ? 'password' : 'text');
            $(this).text(showing ? 'Show' : 'Hide');
        });

        $('#btn-email-test').on('click', sendTest);
        $('#btn-email-clear').on('click', clearForm);
    });
})();
