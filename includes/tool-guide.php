<?php
/**
 * Render AdSense-friendly tool guide sections on every tool page.
 */

if (defined('TOOL_GUIDE_LOADED')) {
    return;
}
define('TOOL_GUIDE_LOADED', true);

if (!defined('SITE_CONFIG_LOADED') && !defined('QUICKTOOLS_CONFIG_LOADED')) {
    require_once dirname(__DIR__) . '/config.php';
}

require_once __DIR__ . '/tool-guides-custom.php';

function tool_guide_processing_type(array $tool): string
{
    $slug = $tool['slug'];
    $cat  = $tool['category'] ?? 'utility';

    if ($slug === 'air-share') {
        return 'server_share';
    }
    if (in_array($slug, ['background-remover', 'video-cutter', 'audio-cutter'], true)) {
        return 'server_media';
    }
    if (in_array($slug, ['curl-runner', 'email-config-tester', 'stress-test', 'seo-report', 'vapt-report', 'lighthouse-report', 'internet-speed-test'], true)) {
        return 'server_api';
    }
    if ($slug === 'cron-job-service') {
        return 'hybrid';
    }
    if ($cat === 'pdf' || str_starts_with($slug, 'pdf-')) {
        return 'server_pdf';
    }
    if ($slug === 'pdf-to-jpg') {
        return 'browser';
    }
    return 'browser';
}

function tool_guide_privacy_line(string $type): string
{
    return match ($type) {
        'server_pdf'    => 'Files are uploaded for processing on our server and <strong>deleted immediately</strong> after you download the result.',
        'server_media'  => 'Media is processed on the server and <strong>not stored permanently</strong> after your session.',
        'server_share'  => 'Shared content is kept temporarily and <strong>auto-deleted</strong> after the retention period.',
        'server_api'    => 'We process your request in real time and <strong>do not store</strong> credentials or audit results on disk.',
        'hybrid'        => 'Schedules and logs stay in <strong>your browser</strong> (localStorage). HTTP requests run through our server proxy only when you trigger them.',
        default         => 'All processing happens <strong>in your browser</strong>. Your files and text are not uploaded to our servers.',
    };
}

function tool_guide_related_slugs(array $tool): array
{
    global $TOOLS;
    $slug = $tool['slug'];
    $cat  = $tool['category'] ?? 'utility';
    $picked = [];

    foreach ($TOOLS as $t) {
        if ($t['slug'] === $slug) {
            continue;
        }
        if (($t['category'] ?? '') === $cat) {
            $picked[] = $t['slug'];
        }
    }

    if (count($picked) < 4) {
        foreach ($TOOLS as $t) {
            if ($t['slug'] === $slug || in_array($t['slug'], $picked, true)) {
                continue;
            }
            $picked[] = $t['slug'];
            if (count($picked) >= 4) {
                break;
            }
        }
    }

    return array_slice($picked, 0, 4);
}

function tool_guide_category_context(array $tool): array
{
    $title = $tool['title'];
    $cat   = $tool['category'] ?? 'utility';

    return match ($cat) {
        'image' => [
            'use_case' => 'Resize, compress, convert, or edit images before uploading them to a website, social post, or email attachment.',
            'tip'      => 'For best results, start with the highest-quality source file you have. Most image tools preserve transparency when the format supports it (PNG, WebP).',
            'extra_faq' => ['What image formats are supported?', 'Common formats include JPG, PNG, WebP, and GIF depending on the tool. Check the upload area on the page for the exact list.'],
        ],
        'developer' => [
            'use_case' => 'Format JSON, minify CSS, test regex patterns, or run API requests while building a website or app — without installing desktop software.',
            'tip'      => 'Copy the output directly into your IDE, CI pipeline, or documentation. Developer tools here are meant for quick checks, not as a replacement for full local tooling.',
            'extra_faq' => ['Can I use this in production workflows?', 'Yes for formatting and testing. Always review generated code and run your own tests before deploying to production.'],
        ],
        'text' => [
            'use_case' => 'Clean up copy, compare drafts, count words, or transform text for essays, blog posts, emails, and social captions.',
            'tip'      => 'Paste plain text for the fastest results. Rich text from Word or Google Docs may include hidden formatting — paste as plain text if counts or diffs look wrong.',
            'extra_faq' => ['Is there a character or word limit?', 'Browser-based text tools handle large inputs, but very long documents may slow down on older devices. Split huge files if the page feels sluggish.'],
        ],
        'calculator' => [
            'use_case' => 'Solve everyday math — percentages, BMI, loan estimates, unit conversions — on any device without opening a spreadsheet.',
            'tip'      => 'Double-check inputs and units. Calculators show formulas or labels on the page so you can verify the logic matches your use case.',
            'extra_faq' => ['Are calculator results guaranteed accurate?', 'Results use standard formulas shown on the page. They are for general use — not financial, medical, or legal advice.'],
        ],
        'generator' => [
            'use_case' => 'Create QR codes, passwords, UUIDs, lorem ipsum, or CSS snippets for prototypes, events, and development tasks.',
            'tip'      => 'Download or copy the generated output immediately. Generated items are not saved to an account because no sign-up is required.',
            'extra_faq' => ['Can I customize the output?', 'Most generators offer options on the same page — colors, size, length, or format — before you copy or download.'],
        ],
        'seo' => [
            'use_case' => 'Audit a landing page, check meta tags, run a speed test, or inspect robots/sitemap settings before publishing or pitching a client.',
            'tip'      => 'Run audits on URLs you own or have permission to test. Results are automated hints — combine them with manual review and Search Console data.',
            'extra_faq' => ['Will this fix my Google rankings?', 'SEO tools highlight issues and opportunities. Rankings depend on content quality, competition, and many factors outside any single audit.'],
        ],
        'pdf' => [
            'use_case' => 'Merge contracts, compress scans, convert PDFs to Word or JPG, add page numbers, or unlock files for office and school work.',
            'tip'      => 'Upload the original PDF when possible. Server-processed files are deleted right after download — save the result before closing the tab.',
            'extra_faq' => ['Is there a file size limit?', 'Most PDF tools accept files up to about ' . (int) (PDF_MAX_BYTES / (1024 * 1024)) . ' MB. Larger files may fail or take longer on slow connections.'],
        ],
        default => [
            'use_case' => 'Complete a quick online task — formatting, converting, calculating, or checking something — in one browser tab.',
            'tip'      => 'Bookmark tools you use often. Each page is focused on one job so you can finish faster than a general-purpose app.',
            'extra_faq' => ['Does this tool save my history?', 'No account means no saved history on our side. Browser-based tools may reset when you refresh unless the page says otherwise.'],
        ],
    };
}

function tool_guide_build(array $tool): array
{
    $custom = tool_guide_custom_content($tool['slug']);
    if ($custom) {
        if (empty($custom['related'])) {
            $custom['related'] = tool_guide_related_slugs($tool);
        }
        return $custom;
    }

    $title = $tool['title'];
    $desc  = $tool['description'];
    $type  = tool_guide_processing_type($tool);
    $privacy = tool_guide_privacy_line($type);
    $ctx   = tool_guide_category_context($tool);

    $action = match ($type) {
        'server_pdf'   => 'upload your file and click the process button',
        'server_media' => 'upload your file and run the tool',
        'server_api'   => 'enter your URL or settings and run the test',
        'server_share' => 'open the link, add content, and save',
        'hybrid'       => 'configure your job and start the schedule',
        default        => 'paste or enter your input and click the action button',
    };

    return [
        'what' => [
            '<strong>' . htmlspecialchars($title) . '</strong> is a free online tool on ' . htmlspecialchars(SITE_DOMAIN) . '. ' . htmlspecialchars($desc),
            $privacy,
            htmlspecialchars($ctx['use_case']),
        ],
        'why' => [
            'Free to use — no subscription or paywall.',
            'No account required — open the page and start immediately.',
            'Works on desktop, tablet, and phone in any modern browser.',
            'Focused interface — one tool, one job, no clutter.',
            $type === 'browser'
                ? 'Private by design — processing stays on your device when possible.'
                : 'Clear server handling — we explain when uploads are used and how long data is kept.',
        ],
        'how' => 'Open the ' . htmlspecialchars($title) . ' page, ' . $action . ', then copy or download the result. ' . htmlspecialchars($ctx['tip']),
        'how_steps' => [
            'Go to the ' . $title . ' tool on ' . SITE_DOMAIN . '.',
            ucfirst($action) . '.',
            'Review the output, preview, or download on the same page.',
            'Optional: copy the result or run the tool again with new input.',
        ],
        'steps' => [
            ['title' => 'Using ' . $title, 'items' => [
                'Open this tool page in your browser.',
                'Read the short description at the top for any upload limits or tips.',
                'Enter, paste, or upload your input in the form or editor.',
                'Click the primary action button (e.g. Convert, Generate, Minify, Run).',
                'Copy or download the result, or adjust settings and run again.',
            ]],
        ],
        'example' => [
            'title' => 'You need a quick result without installing software.',
            'items' => [
                'You open ' . $title . ' on your phone or laptop.',
                'You provide the input the tool asks for — for example: ' . lcfirst($ctx['use_case']),
                'You get the output in seconds and copy it into your project, document, or chat.',
            ],
        ],
        'faqs' => [
            ['Is ' . $title . ' free?', 'Yes. All tools on ' . SITE_NAME . ' are free to use.'],
            ['Do I need to create an account?', 'No. Every tool works without sign-up.'],
            ['Is my data stored on your servers?', strip_tags($privacy)],
            ['Does it work on mobile?', 'Yes. The site is responsive and works in mobile browsers.'],
            $ctx['extra_faq'],
            ['Can I use the output commercially?', 'You own your input and output. Check third-party licenses if the tool uses external APIs or formats.'],
        ],
        'related' => tool_guide_related_slugs($tool),
    ];
}

function tool_guide_slug_from_request(): string
{
    if (!empty($GLOBALS['tool_guide_slug'])) {
        return (string) $GLOBALS['tool_guide_slug'];
    }

    $script = basename($_SERVER['SCRIPT_FILENAME'] ?? '', '.php');
    if ($script !== '' && $script !== 'router' && $script !== 'index') {
        return $script;
    }

    $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $slug = trim((string) $uri, '/');
    if (str_ends_with($slug, '.php')) {
        $slug = substr($slug, 0, -4);
    }

    return $slug;
}

function render_tool_guide(string $slug): void
{
    if ($slug === '') {
        $slug = tool_guide_slug_from_request();
    }

    if ($slug === '' || in_array($slug, ['index', 'about', 'contact', 'privacy', 'router'], true)) {
        return;
    }

    $tool = get_tool_by_slug($slug);
    if (!$tool) {
        return;
    }

    $guide = tool_guide_build($tool);
    $title = $tool['title'];

    echo '<div class="container tool-guide-wrap">';
    echo '<section class="tool-guide content-page" id="tool-guide">';

    echo '<h2>What this tool does</h2>';
    foreach ($guide['what'] as $p) {
        echo '<p>' . $p . '</p>';
    }

    echo '<h2>Why use ' . htmlspecialchars($title) . '</h2><ul>';
    foreach ($guide['why'] as $item) {
        echo '<li>' . $item . '</li>';
    }
    echo '</ul>';

    echo '<h2>How it works</h2>';
    echo '<p>' . ($guide['how'] ?? '') . '</p>';
    if (!empty($guide['how_steps'])) {
        echo '<ol>';
        foreach ($guide['how_steps'] as $step) {
            echo '<li>' . $step . '</li>';
        }
        echo '</ol>';
    }

    echo '<h2>Step-by-step instructions</h2>';
    foreach ($guide['steps'] as $block) {
        if (!empty($block['title'])) {
            echo '<h3>' . htmlspecialchars($block['title']) . '</h3>';
        }
        echo '<ol>';
        foreach ($block['items'] as $item) {
            echo '<li>' . $item . '</li>';
        }
        echo '</ol>';
    }

    if (!empty($guide['example'])) {
        echo '<h2>Example</h2>';
        echo '<p><strong>Scenario:</strong> ' . htmlspecialchars($guide['example']['title']) . '</p><ol>';
        foreach ($guide['example']['items'] as $item) {
            echo '<li>' . $item . '</li>';
        }
        echo '</ol>';
    }

    echo '<h2>FAQs</h2><dl class="tool-faq">';
    foreach ($guide['faqs'] as $faq) {
        echo '<dt>' . htmlspecialchars($faq[0]) . '</dt><dd>' . htmlspecialchars($faq[1]) . '</dd>';
    }
    echo '</dl>';

    echo '<h2>Related tools</h2><ul class="tool-related-list">';
    foreach ($guide['related'] as $relSlug) {
        $rel = get_tool_by_slug($relSlug);
        if (!$rel) {
            continue;
        }
        echo '<li><a href="' . htmlspecialchars(tool_url($relSlug)) . '">' . htmlspecialchars($rel['title']) . '</a> — ' . htmlspecialchars($rel['description']) . '</li>';
    }
    echo '</ul>';

    echo '</section></div>';
}
