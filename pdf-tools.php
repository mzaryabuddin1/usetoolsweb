<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/pdf-tool-scripts.php';

$meta = page_meta(
    'All PDF Tools',
    'Free online PDF tools — merge, split, compress, convert, edit, sign, and secure PDF files.'
);

require_once __DIR__ . '/includes/header.php';

$sections = [
    'Organize PDF' => ['pdf-merge', 'pdf-split', 'pdf-remove-pages', 'pdf-extract-pages', 'pdf-organize', 'scan-to-pdf'],
    'Optimize PDF' => ['pdf-compress', 'pdf-repair', 'ocr-pdf'],
    'Convert to PDF' => ['jpg-to-pdf', 'word-to-pdf', 'excel-to-pdf', 'ppt-to-pdf', 'html-to-pdf'],
    'Convert from PDF' => ['pdf-to-jpg', 'pdf-to-word', 'pdf-to-excel', 'pdf-to-ppt', 'pdf-to-pdfa'],
    'Edit PDF' => ['pdf-rotate', 'pdf-page-numbers', 'pdf-watermark', 'pdf-crop', 'pdf-edit'],
    'PDF Security' => ['pdf-unlock', 'pdf-protect', 'pdf-sign', 'pdf-redact', 'pdf-compare'],
];
?>

<div class="container tool-page">
    <div class="tool-page-header">
        <h1>PDF Tools</h1>
        <p>Every PDF tool you need — merge, split, compress, convert, edit, and secure documents. Most tools run in your browser; some use secure server processing when needed.</p>
    </div>

    <?php foreach ($sections as $title => $slugs): ?>
        <section class="pdf-tools-section">
            <h2 class="section-title"><?= htmlspecialchars($title) ?></h2>
            <div class="tools-grid">
                <?php foreach ($slugs as $slug):
                    $tool = get_tool_by_slug($slug);
                    if (!$tool) continue;
                ?>
                    <a href="/<?= htmlspecialchars($tool['slug']) ?>" class="tool-card" data-search="<?= htmlspecialchars(strtolower($tool['title'] . ' ' . $tool['description'])) ?>">
                        <span class="tool-icon"><?= $tool['icon'] ?></span>
                        <h3><?= htmlspecialchars($tool['title']) ?></h3>
                        <p><?= htmlspecialchars($tool['description']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <?php ad_slot(); ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
