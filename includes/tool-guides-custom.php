<?php
/**
 * Tool guide sections for AdSense / SEO — custom overrides per slug.
 * Return null to use the auto-generated template.
 */

function tool_guide_custom_content(string $slug): ?array
{
    $custom = tool_guide_custom_guides();
    return $custom[$slug] ?? null;
}

function tool_guide_custom_guides(): array
{
    $site = rtrim(SITE_URL, '/');
    $airRetention = defined('AIR_SHARE_RETENTION_DAYS') ? (int) AIR_SHARE_RETENTION_DAYS : 7;
    $airMaxMb = defined('AIR_SHARE_MAX_BYTES') ? (int) (AIR_SHARE_MAX_BYTES / (1024 * 1024)) : 50;

    return [
        'air-share' => [
            'what' => [
                '<strong>Air Share</strong> is a free online shared clipboard and file board. Open one link, type text or upload files, hit <strong>Save</strong>, and anyone with the same link sees your updates — in the office or across the internet. No accounts or apps required.',
                'Each board gets a unique URL like <code>' . htmlspecialchars($site . '/air-share?d=397f68cb') . '</code>. Content is stored temporarily and <strong>automatically deleted after ' . $airRetention . ' days</strong>.',
            ],
            'why' => [
                'Share with colleagues instantly — one link in chat or email.',
                'No sign-up required — start sharing in seconds.',
                'Text and files in one place — notes, PDFs, images, ZIPs.',
                'Works anywhere — same Wi‑Fi not required.',
                'Auto-sync — teammates see updates within a few seconds.',
                'Temporary by design — boards expire after ' . $airRetention . ' days.',
            ],
            'how' => 'Air Share uses a <strong>shared board ID</strong> in the URL (<code>?d=</code> plus an 8-character code). Saving text or uploading files updates the board on the server. The page polls every few seconds so others see changes automatically.',
            'how_steps' => [
                'Open Air Share and copy your board link (or open a link someone sent you).',
                'Type in the <strong>Text</strong> area or upload files under <strong>Files</strong>.',
                'Click <strong>Save</strong> for text; wait for uploads to finish for files.',
                'Teammates open the same link and see your content.',
                'After ' . $airRetention . ' days, the board and files are removed.',
            ],
            'steps' => [
                ['title' => 'Start a new board', 'items' => [
                    'Go to Air Share on ' . SITE_DOMAIN . '.',
                    'Copy the board link at the top or click <strong>New board</strong>.',
                    'Send the link via Slack, Teams, WhatsApp, or email.',
                ]],
                ['title' => 'Share text', 'items' => [
                    'Type or paste in the <strong>Text</strong> box.',
                    'Click <strong>Save</strong>.',
                    'Others see the text within a few seconds.',
                ]],
                ['title' => 'Share files', 'items' => [
                    'Drag files onto the drop zone or click to browse.',
                    'Wait for upload (up to ' . $airMaxMb . ' MB each, max 20 files).',
                    'Others click <strong>Download</strong> next to each file.',
                ]],
            ],
            'example' => [
                'title' => 'A designer sends a logo and brief to a developer in another city.',
                'items' => [
                    'Designer copies <code>' . $site . '/air-share?d=397f68cb</code>.',
                    'Pastes the brief in <strong>Text</strong> and clicks <strong>Save</strong>.',
                    'Uploads <code>logo.png</code> to <strong>Files</strong>.',
                    'Developer opens the link, reads the brief, and downloads the logo.',
                ],
            ],
            'faqs' => [
                ['Do I need an account?', 'No. Air Share is free without registration.'],
                ['How long is data kept?', 'Boards and files are deleted after ' . $airRetention . ' days.'],
                ['Maximum file size?', 'Up to ' . $airMaxMb . ' MB per file, max 20 files per board.'],
                ['Same network required?', 'No — works over the public internet with the board link.'],
                ['Is it private?', 'Anyone with the link can view content. Share only with people you trust.'],
            ],
            'related' => ['qr-code-generator', 'compress-image', 'base64-encode-decode', 'json-formatter'],
        ],
    ];
}
