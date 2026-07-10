<?php
/**
 * Render AdSense-friendly tool guide sections on every tool page.
 */

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
        'how' => 'Open the ' . htmlspecialchars($title) . ' page, ' . $action . ', then copy or download the result. The interface labels each step so you can complete the task in one visit.',
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
                'You provide the input the tool asks for.',
                'You get the output in seconds and copy it into your project, document, or chat.',
            ],
        ],
        'faqs' => [
            ['Is ' . $title . ' free?', 'Yes. All tools on ' . SITE_NAME . ' are free to use.'],
            ['Do I need to create an account?', 'No. Every tool works without sign-up.'],
            ['Is my data stored on your servers?', strip_tags($privacy)],
            ['Does it work on mobile?', 'Yes. The site is responsive and works in mobile browsers.'],
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
