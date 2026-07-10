    <?php
    if (empty($skip_tool_guide)) {
        require_once __DIR__ . '/tool-guide.php';
        render_tool_guide(tool_guide_slug_from_request());
    }
    ?>
    </main>
    <footer class="site-footer">
        <div class="container footer-inner">
            <div class="footer-brand">
                <a href="<?= rtrim(SITE_URL, '/') ?>/" class="footer-logo">
                    <img src="<?= SITE_LOGO ?>" alt="" class="logo-img-light" width="180" height="30" loading="lazy">
                    <img src="<?= SITE_LOGO_DARK ?>" alt="" class="logo-img-dark" width="180" height="30" loading="lazy">
                    <span class="sr-only"><?= htmlspecialchars(SITE_LOGO_ALT) ?></span>
                </a>
                <p><?= htmlspecialchars(SITE_TAGLINE) ?></p>
            </div>
            <div class="footer-links">
                <a href="<?= rtrim(SITE_URL, '/') ?>/about">About</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/privacy">Privacy Policy</a>
                <a href="<?= rtrim(SITE_URL, '/') ?>/contact">Contact</a>
            </div>
            <p class="footer-copy">&copy; <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME) ?>. All rights reserved.</p>
        </div>
    </footer>

    <?php /* AccessibleWeb Widget — MIT https://github.com/ifrederico/accessible-web-widget */ ?>
    <div
        data-acc-position="<?= htmlspecialchars(ACC_WIDGET_POSITION) ?>"
        data-acc-offset="<?= htmlspecialchars(ACC_WIDGET_OFFSET) ?>"
        data-acc-lang="<?= htmlspecialchars(ACC_WIDGET_LANG) ?>"
    ></div>
    <script>
        window.AccessibleWebWidgetOptions = {
            ttsNativeVoiceLang: 'en-US'
        };
    </script>
    <script src="https://cdn.jsdelivr.net/gh/ifrederico/accessible-web-widget@<?= ACC_WIDGET_VERSION ?>/dist/accessible-web-widget.min.js"></script>

    <script src="/assets/js/theme-toggle.js"></script>
    <script src="/assets/js/main.js"></script>
    <?php if (!empty($extra_scripts)) echo $extra_scripts; ?>
</body>
</html>
