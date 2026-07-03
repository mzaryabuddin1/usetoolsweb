<?php
require_once __DIR__ . '/config.php';

$meta = page_meta(
    'Contact',
    'Contact ' . SITE_NAME . ' — get in touch with questions, feedback, or support.'
);

$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Replace with mail() or SMTP when hosting is set up
        $sent = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container content-page">
    <h1>Contact Us</h1>
    <p>Have a question, suggestion, or found a bug? We'd love to hear from you.</p>

    <?php if ($sent): ?>
        <div class="alert alert-success">Thank you! Your message has been received. We'll get back to you soon.</div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="tool-panel" style="margin-top:1.5rem;">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

            <label for="email" style="margin-top:1rem;">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="message" style="margin-top:1rem;">Message</label>
            <textarea id="message" name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

            <div class="btn-row">
                <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
        </form>

        <p style="margin-top:1.5rem;color:var(--color-muted);">Or email us directly at <a href="mailto:<?= htmlspecialchars(SITE_EMAIL) ?>"><?= htmlspecialchars(SITE_EMAIL) ?></a></p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
