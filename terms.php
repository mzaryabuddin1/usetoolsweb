<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Terms of Service',
    'Terms of service for ' . SITE_NAME . ' — rules for using our free online tools.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container content-page">
    <h1>Terms of Service</h1>
    <p>Last updated: <?= date('F j, Y') ?></p>

    <p>By using <?= htmlspecialchars(SITE_DOMAIN) ?> ("the Site"), operated by <?= htmlspecialchars(SITE_NAME) ?>, you agree to these Terms of Service. If you do not agree, please do not use the Site.</p>

    <h2>Use of our tools</h2>
    <ul>
        <li>Our tools are provided <strong>free of charge</strong> for lawful personal and professional use.</li>
        <li>You must not use the Site to violate any law, infringe others' rights, or distribute malware, spam, or abusive content.</li>
        <li>Tools that send HTTP requests, email, or load tests (e.g. cURL Runner, Stress Test, Email Config Tester, Cron Job Service) may only be used on <strong>systems and endpoints you own or have explicit permission to test</strong>.</li>
        <li>Security and SEO audit tools provide automated surface checks only — they are <strong>not</strong> a substitute for professional security audits or penetration testing.</li>
    </ul>

    <h2>Your content</h2>
    <p>You retain ownership of files, text, and data you submit. Many tools process data in your browser; some tools temporarily process uploads on our server and delete them after delivery. Do not upload confidential, illegal, or sensitive personal data unless you understand how each tool works.</p>

    <h2>No warranty</h2>
    <p>The Site and all tools are provided <strong>"as is"</strong> without warranties of any kind. We do not guarantee accuracy, availability, or fitness for a particular purpose. Use outputs at your own risk — especially for financial, legal, medical, or security decisions.</p>

    <h2>Limitation of liability</h2>
    <p>To the fullest extent permitted by law, <?= htmlspecialchars(SITE_NAME) ?> shall not be liable for any indirect, incidental, or consequential damages arising from your use of the Site.</p>

    <h2>Third-party services</h2>
    <p>We may use third-party services such as Google Analytics and Google AdSense. Their use is subject to their respective policies. See our <a href="<?= htmlspecialchars(tool_url('privacy')) ?>">Privacy Policy</a> for more information.</p>

    <h2>Changes</h2>
    <p>We may update these terms at any time. Continued use of the Site after changes constitutes acceptance of the updated terms.</p>

    <h2>Contact</h2>
    <p>Questions about these terms? Contact us at <a href="mailto:<?= htmlspecialchars(SITE_EMAIL) ?>"><?= htmlspecialchars(SITE_EMAIL) ?></a> or via our <a href="<?= htmlspecialchars(tool_url('contact')) ?>">contact page</a>.</p>
</div>

<div class="container">
    <?php ad_slot(); ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
