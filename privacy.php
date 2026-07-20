<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Privacy Policy',
    'Privacy policy for ' . SITE_NAME . '. Learn how we handle your data, cookies, analytics, and advertising when using our free online tools.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container content-page">
    <h1>Privacy Policy</h1>
    <p>Last updated: <?= date('F j, Y') ?></p>

    <p><?= htmlspecialchars(SITE_NAME) ?> ("we", "us", or "our") operates <?= htmlspecialchars(SITE_DOMAIN) ?>. This page explains what information we collect, how we use it, and your choices when you use our website and free online tools.</p>

    <h2>Information we collect</h2>
    <p>Most of our tools process data entirely in your browser. We do not upload your files or text to our servers for tools such as the image compressor, word counter, or JSON formatter. Each tool page includes a guide that explains whether processing is local or server-based.</p>
    <p>When you use server-based tools (for example PDF processing, background removal, or Air Share), files or content may be uploaded temporarily and deleted after your session or download.</p>
    <p>We may collect standard web analytics data (such as pages visited, browser type, device type, and general location) through Google Analytics when you accept cookies.</p>

    <h2>Cookies and consent</h2>
    <p>We use a cookie banner so you can accept or reject non-essential cookies. If you accept, we may load Google Analytics to understand how visitors use the site. If you reject, analytics cookies are not loaded.</p>
    <p>You can also disable cookies in your browser settings at any time.</p>

    <h2>Google Analytics</h2>
    <p>We use Google Analytics to measure traffic and improve our tools. Analytics data is aggregated and helps us see which pages are most useful. Google’s privacy policy applies to how they process analytics data: <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</a>.</p>

    <h2>Third-party advertising (Google AdSense)</h2>
    <p>We may display advertisements through Google AdSense. Google and its partners may use cookies to serve ads based on your prior visits to this or other websites.</p>
    <ul>
        <li>You can opt out of personalized advertising at <a href="https://www.google.com/settings/ads" target="_blank" rel="noopener">Google Ads Settings</a>.</li>
        <li>Learn more about how Google uses data at <a href="https://policies.google.com/technologies/partner-sites" target="_blank" rel="noopener">Google Partner Sites</a>.</li>
        <li>EU users may also visit <a href="https://www.youronlinechoices.eu/" target="_blank" rel="noopener">Your Online Choices</a>.</li>
    </ul>

    <h2>Contact form</h2>
    <p>If you submit our contact form, we receive your name, email address, and message so we can reply. We do not sell this information to third parties.</p>

    <h2>Children’s privacy</h2>
    <p>Our site is intended for a general audience. We do not knowingly collect personal information from children under 13.</p>

    <h2>Data retention</h2>
    <p>Browser-based tools do not retain your input on our servers. Server-processed uploads are deleted promptly after processing. Shared Air Share boards expire automatically after the retention period stated on that tool’s page.</p>

    <h2>Terms of Service</h2>
    <p>Your use of this website is also governed by our <a href="<?= htmlspecialchars(tool_url('terms')) ?>">Terms of Service</a>.</p>

    <h2>Contact</h2>
    <p>If you have questions about this privacy policy, contact us at <a href="mailto:<?= htmlspecialchars(SITE_EMAIL) ?>"><?= htmlspecialchars(SITE_EMAIL) ?></a> or via our <a href="<?= htmlspecialchars(tool_url('contact')) ?>">contact page</a>.</p>
</div>

<div class="container">
    <?php ad_slot(); ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
