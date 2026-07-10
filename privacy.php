<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Privacy Policy',
    'Privacy policy for ' . SITE_NAME . '. Learn how we handle your data when using our free online tools.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container content-page">
    <h1>Privacy Policy</h1>
    <p>Last updated: <?= date('F j, Y') ?></p>

    <p><?= htmlspecialchars(SITE_NAME) ?> ("we", "us", or "our") operates <?= htmlspecialchars(SITE_DOMAIN) ?>. This page informs you of our policies regarding the collection, use, and disclosure of information when you use our website.</p>

    <h2>Information we collect</h2>
    <p>Most of our tools process data entirely in your browser. We do not upload your files or text to our servers for tools such as the image compressor, word counter, or JSON formatter.</p>
    <p>We may collect standard web analytics data (such as pages visited, browser type, and general location) through third-party services like Google Analytics or Google AdSense, if enabled.</p>

    <h2>Cookies</h2>
    <p>We may use cookies to serve advertisements through Google AdSense and to analyze site traffic. You can disable cookies in your browser settings.</p>

    <h2>Third-party advertising</h2>
    <p>We may use Google AdSense to display ads. Google may use cookies to serve ads based on your prior visits to this or other websites. You can opt out of personalized advertising by visiting <a href="https://www.google.com/settings/ads" target="_blank" rel="noopener">Google Ads Settings</a>.</p>

    <h2>Terms of Service</h2>
    <p>Your use of this website is also governed by our <a href="<?= htmlspecialchars(tool_url('terms')) ?>">Terms of Service</a>.</p>

    <h2>Contact</h2>
    <p>If you have questions about this privacy policy, contact us at <a href="mailto:<?= htmlspecialchars(SITE_EMAIL) ?>"><?= htmlspecialchars(SITE_EMAIL) ?></a>.</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
