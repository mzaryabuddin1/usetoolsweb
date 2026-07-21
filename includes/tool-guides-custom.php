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
                '<strong>Air Share</strong> has two modes: <strong>Same network</strong> for centralized sharing on your Wi‑Fi (no link needed), and <strong>Share link</strong> for colleagues outside your network.',
                'On the same network, everyone opens Air Share and sees one shared board — text and files sync automatically. For remote sharing, switch to Share link and copy the URL.',
            ],
            'why' => [
                'Same Wi‑Fi sharing — open the tool, no link to copy.',
                'Share link mode for teammates anywhere on the internet.',
                'No sign-up required — start sharing in seconds.',
                'Text and files in one place — notes, PDFs, images, ZIPs.',
                'Auto-sync — updates appear within a few seconds.',
                'Temporary by design — boards expire after ' . $airRetention . ' days.',
            ],
            'how' => 'Default <strong>Same network</strong> mode joins a board shared by everyone on your Wi‑Fi (or everyone using the same local server address). Switch to <strong>Share link</strong> to create a private URL for people outside your network.',
            'how_steps' => [
                'Open Air Share — you join the network board automatically.',
                'Type in <strong>Text</strong> or upload files, then click <strong>Save</strong> for text.',
                'Others on your Wi‑Fi open Air Share and see the same content.',
                'Need remote access? Switch to <strong>Share link</strong> and copy the URL.',
                'After ' . $airRetention . ' days, boards are removed.',
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
        'qr-code-generator' => [
            'what' => [
                '<strong>QR Code Generator</strong> creates scannable QR codes for URLs, plain text, Wi‑Fi networks, email, phone numbers, and more. Customize colors, add a logo, use solid or gradient fills, and download PNG or SVG.',
                'Everything runs in your browser — the QR image is generated locally and is not uploaded to our servers.',
            ],
            'why' => [
                'Free QR codes with no watermark or account.',
                'Custom colors and gradient foreground options.',
                'Optional logo overlay for branding on marketing materials.',
                'Download PNG for print or SVG for sharp scaling.',
                'Works on mobile — create a code and share it from your phone.',
            ],
            'how' => 'Choose a content type (URL, text, Wi‑Fi, etc.), enter your data, adjust size and colors, optionally upload a logo, then download or copy the image.',
            'how_steps' => [
                'Select what the QR code should encode (link, text, contact, Wi‑Fi, etc.).',
                'Enter the content in the form fields.',
                'Pick foreground and background colors — solid or gradient.',
                'Optionally upload a small logo for the center.',
                'Download PNG/SVG or scan the live preview to test.',
            ],
            'steps' => [
                ['title' => 'QR code for a website link', 'items' => [
                    'Choose <strong>URL</strong> as the type.',
                    'Paste your landing page or menu link.',
                    'Set size to at least 256px for print flyers.',
                    'Download PNG and add it to your poster or business card.',
                ]],
            ],
            'example' => [
                'title' => 'A café adds a QR code to table tents linking to the online menu.',
                'items' => [
                    'Staff opens QR Code Generator on a laptop.',
                    'Pastes the menu URL and sets brand colors.',
                    'Downloads PNG and prints table tents.',
                    'Customers scan with their phone camera — no app required.',
                ],
            ],
            'faqs' => [
                ['Do QR codes expire?', 'Static QR codes generated here do not expire. If you change the destination URL later, generate a new code.'],
                ['Can I add my logo?', 'Yes. Upload a square logo — keep it small so the code stays scannable.'],
                ['Gradient QR codes — will they scan?', 'Yes on modern phones if contrast is strong. Test with the live preview before printing.'],
                ['Is data uploaded?', 'No. Generation happens in your browser.'],
            ],
            'related' => ['qr-code-reader', 'air-share', 'url-encode-decode', 'barcode-generator'],
        ],
        'background-remover' => [
            'what' => [
                '<strong>Background Remover</strong> removes the background from photos using AI (rembg). Upload a portrait, product shot, or logo and download a PNG with a transparent background.',
                'Images are processed on our server and <strong>deleted immediately</strong> after you download the result.',
            ],
            'why' => [
                'Clean product photos for e‑commerce listings.',
                'Profile pictures with transparent backgrounds for slides or thumbnails.',
                'Faster than manual selection in desktop editors for simple subjects.',
                'No Photoshop or GIMP install required.',
                'Free to try — paywall-free downloads.',
            ],
            'how' => 'Upload a JPG or PNG, wait a few seconds for AI processing, preview the cutout, then download the transparent PNG.',
            'how_steps' => [
                'Upload a photo with a clear subject (person, product, object).',
                'Click remove / process and wait for the preview.',
                'Check edges around hair or fine details.',
                'Download PNG and use in Canva, Shopify, PowerPoint, etc.',
            ],
            'steps' => [
                ['title' => 'Product photo for an online store', 'items' => [
                    'Photograph the item on a plain background if possible.',
                    'Upload to Background Remover.',
                    'Download transparent PNG.',
                    'Place on a white or branded background in your store editor.',
                ]],
            ],
            'example' => [
                'title' => 'A seller lists handmade jewelry on Etsy.',
                'items' => [
                    'Takes a phone photo of a necklace on a table.',
                    'Uploads to Background Remover.',
                    'Downloads PNG with transparent background.',
                    'Adds a consistent white backdrop in the listing editor.',
                ],
            ],
            'faqs' => [
                ['What file format do I get?', 'PNG with transparency.'],
                ['Best photos for AI removal?', 'Clear subject, good lighting, and contrast between subject and background work best.'],
                ['Are my photos stored?', 'No. Files are deleted right after processing.'],
                ['Hair and fur edges?', 'AI handles many cases well; complex hair may need touch-up in an image editor.'],
            ],
            'related' => ['compress-image', 'resize-image', 'image-converter', 'favicon-generator'],
        ],
        'curl-runner' => [
            'what' => [
                '<strong>cURL Runner</strong> sends HTTP requests from your browser through our server proxy — test GET, POST, PUT, PATCH, DELETE with custom headers and body, then inspect status, headers, and response body.',
                'Use it to debug APIs, webhooks, and REST endpoints without opening a terminal.',
            ],
            'why' => [
                'Quick API testing without installing Postman or curl locally.',
                'Shareable workflow — copy request settings from docs and run immediately.',
                'Inspect response headers and JSON/XML bodies in one view.',
                'Helpful on locked-down work laptops where CLI tools are restricted.',
                'Free for endpoints you are allowed to test.',
            ],
            'how' => 'Enter a URL, pick the HTTP method, add headers and body if needed, then run the request. Results show status code, timing, headers, and body.',
            'how_steps' => [
                'Paste the API endpoint URL.',
                'Select method (GET, POST, etc.).',
                'Add Authorization or Content-Type headers if required.',
                'Paste JSON or form body for POST/PUT requests.',
                'Run and review the response — copy or troubleshoot from there.',
            ],
            'steps' => [
                ['title' => 'Test a JSON POST endpoint', 'items' => [
                    'Set method to POST.',
                    'Add header: Content-Type: application/json.',
                    'Paste the JSON body from your API docs.',
                    'Run and verify status 200 and expected fields.',
                ]],
            ],
            'example' => [
                'title' => 'A developer verifies a staging webhook before go-live.',
                'items' => [
                    'Pastes the webhook URL into cURL Runner.',
                    'Sets POST with a sample JSON payload.',
                    'Confirms 200 OK and correct response body.',
                    'Fixes server config before production deploy.',
                ],
            ],
            'faqs' => [
                ['Can I test any URL?', 'Only use URLs you own or have permission to test. Do not probe third-party systems without authorization.'],
                ['Are credentials stored?', 'No. Requests run in real time and are not saved on disk.'],
                ['CORS errors?', 'The proxy runs the request server-side so browser CORS limits do not apply the same way as fetch from DevTools.'],
                ['Request size limits?', 'Large responses may be truncated to protect server resources — check the status message on the page.'],
            ],
            'related' => ['postman-to-swagger', 'json-formatter', 'xml-html-beautifier', 'stress-test'],
        ],
        'audio-cutter' => [
            'what' => [
                '<strong>Audio Cutter</strong> trims MP3, WAV, OGG, and other audio files online. Set start and end points on a waveform, preview the selection, and download the clipped segment.',
                'Audio is processed on the server for accurate trimming and is not stored permanently after download.',
            ],
            'why' => [
                'Cut ringtones, podcast clips, or sound effects without desktop software.',
                'Visual waveform makes it easy to find the exact start and end.',
                'Supports common formats used on web and mobile.',
                'No account — upload, trim, download.',
                'Useful for teachers, podcasters, and video editors needing a quick clip.',
            ],
            'how' => 'Upload an audio file, drag the trim handles or enter timestamps, preview the selection, then download the trimmed file.',
            'how_steps' => [
                'Upload MP3, WAV, or another supported format.',
                'Use the waveform to set start and end points.',
                'Preview the clip to confirm it sounds right.',
                'Download the trimmed audio file.',
            ],
            'steps' => [
                ['title' => 'Create a short notification sound', 'items' => [
                    'Upload a longer sound effect file.',
                    'Select the 1–2 second portion you want.',
                    'Preview and download.',
                    'Use in your app or presentation.',
                ]],
            ],
            'example' => [
                'title' => 'A teacher clips a quote from an interview for a classroom slide.',
                'items' => [
                    'Uploads the full interview MP3.',
                    'Finds the quote on the waveform.',
                    'Trims to 15 seconds and downloads.',
                    'Inserts the clip into the slide deck.',
                ],
            ],
            'faqs' => [
                ['What formats are supported?', 'Common formats include MP3, WAV, and OGG — see the upload hint on the page.'],
                ['Is there a length limit?', 'Very large files may hit the upload size limit shown on the tool page.'],
                ['Is my audio kept on the server?', 'No. Files are removed after processing.'],
                ['Will quality change?', 'The tool trims without re-encoding when possible; some formats may be re-encoded on export.'],
            ],
            'related' => ['video-cutter', 'compress-image', 'word-counter', 'text-analyzer'],
        ],
        'box-shadow-css-generator' => [
            'what' => [
                '<strong>Box Shadow CSS Generator</strong> builds <code>box-shadow</code> CSS with a live preview. Drag the shadow handle to set offset, adjust blur, spread, color, opacity, and inset — then copy the CSS.',
                'Runs entirely in your browser. Customize stage and box backgrounds to preview shadows on light, dark, or checkerboard surfaces.',
            ],
            'why' => [
                'See shadow changes instantly instead of guessing values in DevTools.',
                'Drag handle and arrow keys for precise X/Y offset.',
                'Copy ready-to-paste CSS for cards, buttons, and modals.',
                'Test inset shadows for pressed buttons or wells.',
                'Free — no extension or Figma plugin required.',
            ],
            'how' => 'Adjust sliders or drag the orange handle on the preview stage. Toggle stage/box fill between checkerboard and solid colors, then copy the generated CSS.',
            'how_steps' => [
                'Set offset X/Y with sliders or drag the handle.',
                'Adjust blur and spread for soft or sharp shadows.',
                'Pick shadow color and opacity.',
                'Toggle inset for inner shadows if needed.',
                'Copy the CSS into your stylesheet or component.',
            ],
            'steps' => [
                ['title' => 'Card elevation for a dashboard UI', 'items' => [
                    'Set a small Y offset (e.g. 4px) and moderate blur (16px).',
                    'Use a dark shadow at 15–25% opacity.',
                    'Preview on a light gray stage.',
                    'Copy CSS to your .card class.',
                ]],
            ],
            'example' => [
                'title' => 'A frontend dev prototypes button hover states.',
                'items' => [
                    'Creates a default shadow for the resting state.',
                    'Increases blur and spread for hover elevation.',
                    'Copies both CSS blocks into a React component.',
                ],
            ],
            'faqs' => [
                ['Can I get multiple shadows?', 'This tool generates one shadow at a time — combine multiple rules in your CSS for layered effects.'],
                ['Does it include background-color?', 'Yes — the output includes the box background you set in the preview.'],
                ['Inset vs outer shadow?', 'Check the inset box for inner shadows (e.g. form inputs).'],
                ['Works on mobile?', 'Yes — sliders and preview work on touch devices; drag may vary by browser.'],
            ],
            'related' => ['css-minifier', 'css-js-minifier', 'html-encode-decode', 'json-formatter'],
        ],
    ];
}
