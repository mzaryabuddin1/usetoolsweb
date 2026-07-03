<?php
require_once __DIR__ . '/config.php';

$toolCount = count($TOOLS);
$categories = tools_by_category();

$meta = page_meta(
    'About',
    'About ' . SITE_NAME . ' — ' . $toolCount . '+ free online tools for images, developers, writers, and everyday tasks. Browser-based, private, no sign-up.'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container content-page">
    <h1>About <?= htmlspecialchars(SITE_NAME) ?></h1>
    <p>
        <strong><?= htmlspecialchars(SITE_FULL_NAME) ?></strong> is a free collection of
        <strong><?= $toolCount ?> online tools</strong> for everyday work — image editing,
        developer utilities, text processing, calculators, generators, SEO helpers, and more.
        Everything runs in your browser. No account required.
    </p>

    <h2>What we offer</h2>
    <p>Use the search bar on our <a href="<?= rtrim(SITE_URL, '/') ?>/">home page</a> to find the right tool quickly, or browse by category:</p>
    <ul>
        <?php foreach ($categories as $catKey => $group): ?>
            <li>
                <strong><?= htmlspecialchars($group['label']) ?></strong>
                — <?= count($group['tools']) ?> tools
                (e.g. <?= htmlspecialchars($group['tools'][0]['title']) ?><?= count($group['tools']) > 1 ? ', ' . htmlspecialchars($group['tools'][1]['title']) : '' ?>)
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Why use <?= htmlspecialchars(SITE_NAME) ?>?</h2>
    <ul>
        <li><strong>Private by design</strong> — Most tools process your data locally in the browser. Your files and text are not uploaded to our servers.</li>
        <li><strong>No sign-up</strong> — Open any tool and start using it immediately.</li>
        <li><strong>Free to use</strong> — All <?= $toolCount ?> tools are free with no paywalls.</li>
        <li><strong>Fast and focused</strong> — Each tool does one job well, with a clean interface and no bloat.</li>
        <li><strong>Works everywhere</strong> — Use on desktop, tablet, or phone — no software to install.</li>
        <li><strong>Accessibility built in</strong> — A floating accessibility panel lets visitors adjust contrast, font size, reading aids, and more.</li>
    </ul>

    <h2>Who is it for?</h2>
    <p><?= htmlspecialchars(SITE_NAME) ?> is built for anyone who needs quick online utilities:</p>
    <ul>
        <li><strong>Developers</strong> — JSON formatting, Base64, JWT decoding, regex testing, hash generation, and more.</li>
        <li><strong>Designers &amp; creators</strong> — Image compressors, resizers, color converters, QR codes, and favicon tools.</li>
        <li><strong>Writers &amp; students</strong> — Word counters, text analyzers, case converters, and Lorem Ipsum generators.</li>
        <li><strong>Everyone else</strong> — Calculators, unit converters, password generators, and everyday helpers.</li>
    </ul>

    <h2>Our mission</h2>
    <p>
        Useful tools should be accessible to everyone — not locked behind accounts, subscriptions, or heavy desktop apps.
        <?= htmlspecialchars(SITE_NAME) ?> exists to give you fast, reliable utilities in one place, powered by modern web
        technology that keeps your work on your device whenever possible.
    </p>

    <h2>Questions or feedback?</h2>
    <p>
        We'd love to hear from you. Visit our <a href="<?= rtrim(SITE_URL, '/') ?>/contact">contact page</a>
        or email us at <a href="mailto:<?= htmlspecialchars(SITE_EMAIL) ?>"><?= htmlspecialchars(SITE_EMAIL) ?></a>.
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
