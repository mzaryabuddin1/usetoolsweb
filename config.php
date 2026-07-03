<?php
/**
 * QuickTools site configuration
 * Change these values when you buy your domain or rebrand.
 */

if (defined('QUICKTOOLS_CONFIG_LOADED')) {
    return;
}
define('QUICKTOOLS_CONFIG_LOADED', true);

define('SITE_NAME', 'QuickTools');
define('SITE_DOMAIN', 'usetoolsweb.com');
define('SITE_TAGLINE', 'Free Online Tools — Fast, Simple, Private');
define('SITE_DESCRIPTION', 'Free online tools for everyday tasks — image tools, calculators, developer utilities, text tools, and more. All in your browser.');
define('SITE_AUTHOR', 'QuickTools');
define('SITE_EMAIL', 'hello@usetoolsweb.com');

if (!defined('SITE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('SITE_URL', $protocol . '://' . $host);
}

define('SITE_FULL_NAME', SITE_NAME . ' — ' . SITE_DOMAIN);

/** AccessibleWeb Widget — https://github.com/ifrederico/accessible-web-widget (MIT) */
define('ACC_WIDGET_VERSION', '1.3.3');
define('ACC_WIDGET_POSITION', 'bottom-right'); // bottom-right | bottom-left | top-left | top-right
define('ACC_WIDGET_OFFSET', '20,20');         // x, y in pixels
define('ACC_WIDGET_LANG', 'en');

/** Google Analytics — set to empty string to disable */
define('GA_MEASUREMENT_ID', 'G-1VF9ZJ7EPG');

/** FFmpeg — leave empty to auto-detect; set full path on server if not in PATH (e.g. /usr/bin/ffmpeg) */
define('FFMPEG_BINARY', '');

/** Video cutter — max upload size (bytes) and temp storage */
define('VIDEO_CUT_MAX_BYTES', 200 * 1024 * 1024);
define('VIDEO_CUT_TMP_DIR', __DIR__ . '/tmp/video');

/** Tool categories for home page grouping */
$TOOL_CATEGORIES = [
    'image'      => 'Image Tools',
    'developer'  => 'Developer Tools',
    'text'       => 'Text Tools',
    'calculator' => 'Calculators',
    'generator'  => 'Generators',
    'seo'        => 'SEO Tools',
    'utility'    => 'Utilities',
];

/** Tools registry */
$TOOLS = [
    // --- Image ---
    ['slug' => 'compress-image', 'title' => 'Image Compressor', 'description' => 'Reduce JPG and PNG file size without uploading to a server.', 'icon' => '🖼️', 'category' => 'image'],
    ['slug' => 'resize-image', 'title' => 'Image Resizer', 'description' => 'Resize images to exact width and height or by percentage.', 'icon' => '📐', 'category' => 'image'],
    ['slug' => 'image-converter', 'title' => 'Image Converter', 'description' => 'Convert PNG, JPG, and WebP images to another format.', 'icon' => '🔄', 'category' => 'image'],
    ['slug' => 'image-to-base64', 'title' => 'Image to Base64', 'description' => 'Convert images to Base64 data URIs and back.', 'icon' => '🔢', 'category' => 'image'],
    ['slug' => 'favicon-generator', 'title' => 'Favicon Generator', 'description' => 'Create favicon PNGs from any image in multiple sizes.', 'icon' => '⭐', 'category' => 'image'],
    ['slug' => 'pdf-to-jpg', 'title' => 'PDF to JPG', 'description' => 'Convert PDF pages to JPG images in your browser.', 'icon' => '📑', 'category' => 'image'],
    // --- Developer ---
    ['slug' => 'json-formatter', 'title' => 'JSON Formatter', 'description' => 'Validate, beautify, or minify JSON instantly.', 'icon' => '{ }', 'category' => 'developer'],
    ['slug' => 'base64-encode-decode', 'title' => 'Base64 Encoder/Decoder', 'description' => 'Encode or decode text and strings to Base64 format.', 'icon' => '🔤', 'category' => 'developer'],
    ['slug' => 'url-encode-decode', 'title' => 'URL Encoder/Decoder', 'description' => 'Encode or decode URLs and query strings safely.', 'icon' => '🔗', 'category' => 'developer'],
    ['slug' => 'hash-generator', 'title' => 'Hash Generator', 'description' => 'Generate MD5, SHA-1, SHA-256, and SHA-512 hashes from text.', 'icon' => '#️⃣', 'category' => 'developer'],
    ['slug' => 'html-encode-decode', 'title' => 'HTML Encoder/Decoder', 'description' => 'Escape or unescape HTML entities in text.', 'icon' => '🏷️', 'category' => 'developer'],
    ['slug' => 'csv-to-json', 'title' => 'CSV to JSON', 'description' => 'Convert CSV data to JSON format instantly.', 'icon' => '📋', 'category' => 'developer'],
    ['slug' => 'jwt-decoder', 'title' => 'JWT Decoder', 'description' => 'Decode JSON Web Token headers and payloads.', 'icon' => '🎫', 'category' => 'developer'],
    ['slug' => 'regex-tester', 'title' => 'Regex Tester', 'description' => 'Test regular expressions against sample text.', 'icon' => '🔍', 'category' => 'developer'],
    ['slug' => 'markdown-preview', 'title' => 'Markdown Preview', 'description' => 'Write Markdown and preview rendered HTML live.', 'icon' => '📖', 'category' => 'developer'],
    ['slug' => 'css-minifier', 'title' => 'CSS Minifier', 'description' => 'Minify CSS code by removing whitespace and comments.', 'icon' => '🎨', 'category' => 'developer'],
    ['slug' => 'code-minifier', 'title' => 'HTML/JS Minifier', 'description' => 'Minify HTML or JavaScript code.', 'icon' => '💻', 'category' => 'developer'],
    ['slug' => 'timestamp-converter', 'title' => 'Timestamp Converter', 'description' => 'Convert Unix timestamps to human-readable dates and back.', 'icon' => '🕐', 'category' => 'developer'],
    // --- Text ---
    ['slug' => 'word-counter', 'title' => 'Word Counter', 'description' => 'Count words, characters, sentences, and reading time.', 'icon' => '📝', 'category' => 'text'],
    ['slug' => 'text-analyzer', 'title' => 'Text Analyzer', 'description' => 'Analyze text: lines, bytes, unique words, and more.', 'icon' => '📊', 'category' => 'text'],
    ['slug' => 'case-converter', 'title' => 'Case Converter', 'description' => 'Convert text to uppercase, lowercase, title case, and more.', 'icon' => 'Aa', 'category' => 'text'],
    ['slug' => 'remove-duplicate-lines', 'title' => 'Remove Duplicate Lines', 'description' => 'Remove duplicate lines from text while keeping order.', 'icon' => '🧹', 'category' => 'text'],
    ['slug' => 'text-diff', 'title' => 'Text Diff', 'description' => 'Compare two texts and highlight the differences.', 'icon' => '⚖️', 'category' => 'text'],
    ['slug' => 'reverse-text', 'title' => 'Reverse Text', 'description' => 'Reverse characters, words, or lines in text.', 'icon' => '↩️', 'category' => 'text'],
    ['slug' => 'morse-code', 'title' => 'Morse Code Translator', 'description' => 'Convert text to Morse code and decode Morse to text.', 'icon' => '📡', 'category' => 'text'],
    ['slug' => 'word-frequency', 'title' => 'Word Frequency Counter', 'description' => 'Count how often each word appears in your text.', 'icon' => '📈', 'category' => 'text'],
    // --- Calculators ---
    ['slug' => 'percentage-calculator', 'title' => 'Percentage Calculator', 'description' => 'Calculate percentages, increases, and decreases.', 'icon' => '%', 'category' => 'calculator'],
    ['slug' => 'age-calculator', 'title' => 'Age Calculator', 'description' => 'Calculate exact age from date of birth.', 'icon' => '🎂', 'category' => 'calculator'],
    ['slug' => 'bmi-calculator', 'title' => 'BMI Calculator', 'description' => 'Calculate Body Mass Index from height and weight.', 'icon' => '⚖️', 'category' => 'calculator'],
    ['slug' => 'tip-calculator', 'title' => 'Tip Calculator', 'description' => 'Calculate tip amount and split bill between people.', 'icon' => '💵', 'category' => 'calculator'],
    // --- Generators ---
    ['slug' => 'password-generator', 'title' => 'Password Generator', 'description' => 'Generate strong random passwords with custom length and options.', 'icon' => '🔐', 'category' => 'generator'],
    ['slug' => 'uuid-generator', 'title' => 'UUID Generator', 'description' => 'Generate random UUID v4 identifiers instantly.', 'icon' => '🆔', 'category' => 'generator'],
    ['slug' => 'qr-code-generator', 'title' => 'QR Code Generator', 'description' => 'Create QR codes from text or URLs and download as PNG.', 'icon' => '📱', 'category' => 'generator'],
    ['slug' => 'qr-code-reader', 'title' => 'QR Code Reader', 'description' => 'Scan and decode QR codes using your camera.', 'icon' => '📷', 'category' => 'generator'],
    ['slug' => 'lorem-ipsum-generator', 'title' => 'Lorem Ipsum Generator', 'description' => 'Generate placeholder Lorem Ipsum text for designs and mockups.', 'icon' => '📄', 'category' => 'generator'],
    ['slug' => 'random-number-generator', 'title' => 'Random Number Generator', 'description' => 'Generate random numbers within a custom range.', 'icon' => '🎲', 'category' => 'generator'],
    ['slug' => 'barcode-generator', 'title' => 'Barcode Generator', 'description' => 'Generate CODE128 barcodes and download as PNG.', 'icon' => '📊', 'category' => 'generator'],
    // --- SEO ---
    ['slug' => 'meta-tags-generator', 'title' => 'Meta Tags Generator', 'description' => 'Generate HTML meta tags for SEO and social sharing.', 'icon' => '🏷️', 'category' => 'seo'],
    ['slug' => 'slug-generator', 'title' => 'Slug Generator', 'description' => 'Convert titles to URL-friendly slugs.', 'icon' => '🔤', 'category' => 'seo'],
    ['slug' => 'sitemap-generator', 'title' => 'Sitemap Generator', 'description' => 'Create an XML sitemap from a list of URLs.', 'icon' => '🗺️', 'category' => 'seo'],
    ['slug' => 'robots-txt-generator', 'title' => 'Robots.txt Generator', 'description' => 'Generate robots.txt files for your website.', 'icon' => '🤖', 'category' => 'seo'],
    // --- Utility / Converters ---
    ['slug' => 'color-converter', 'title' => 'Color Converter', 'description' => 'Convert colors between HEX, RGB, and HSL with live preview.', 'icon' => '🎨', 'category' => 'utility'],
    ['slug' => 'unit-converter', 'title' => 'Unit Converter', 'description' => 'Convert length, weight, and temperature units.', 'icon' => '📏', 'category' => 'utility'],
    ['slug' => 'binary-converter', 'title' => 'Binary Converter', 'description' => 'Convert text to binary and binary back to text.', 'icon' => '01', 'category' => 'utility'],
    ['slug' => 'roman-numerals', 'title' => 'Roman Numerals Converter', 'description' => 'Convert numbers to Roman numerals and back.', 'icon' => 'Ⅻ', 'category' => 'utility'],
    ['slug' => 'timezone-converter', 'title' => 'Timezone Converter', 'description' => 'Convert date and time between timezones.', 'icon' => '🌍', 'category' => 'utility'],
    ['slug' => 'pdf-merge', 'title' => 'PDF Merge', 'description' => 'Merge multiple PDF files into one document.', 'icon' => '📎', 'category' => 'utility'],
    ['slug' => 'dice-roller', 'title' => 'Dice Roller', 'description' => 'Roll virtual dice — d4, d6, d8, d10, d12, d20.', 'icon' => '🎲', 'category' => 'utility'],
    ['slug' => 'online-timer', 'title' => 'Online Timer', 'description' => 'Countdown timer and stopwatch in your browser.', 'icon' => '⏱️', 'category' => 'utility'],
    ['slug' => 'card-validator', 'title' => 'Credit Card Validator', 'description' => 'Validate credit card numbers using the Luhn algorithm.', 'icon' => '💳', 'category' => 'utility'],
    ['slug' => 'iban-validator', 'title' => 'IBAN Validator', 'description' => 'Validate International Bank Account Numbers.', 'icon' => '🏛️', 'category' => 'utility'],
    ['slug' => 'video-cutter', 'title' => 'Video Cutter', 'description' => 'Trim and cut videos online. Preview in browser, export with FFmpeg — deleted after processing.', 'icon' => '🎬', 'category' => 'utility'],
    ['slug' => 'audio-cutter', 'title' => 'Audio Cutter', 'description' => 'Trim audio with waveform. Reorder clips, insert silence, remove blank gaps, export MP3 or WAV.', 'icon' => '🎵', 'category' => 'utility'],
];

/** SEO — default site keywords (used on home + appended on tool pages) */
define('SITE_KEYWORDS', 'online tools, free tools, web utilities, usetoolsweb, quicktools, developer tools, image tools, calculators, text tools');

/** OG default image — update path when you add a real social share image */
define('SITE_OG_IMAGE', '/assets/images/og-default.png');

function get_tool_by_slug(string $slug): ?array
{
    global $TOOLS;
    foreach ($TOOLS as $tool) {
        if ($tool['slug'] === $slug) {
            return $tool;
        }
    }
    return null;
}

function seo_keywords_for_slug(string $slug, string $title = ''): string
{
    global $TOOL_CATEGORIES;

    $tool = get_tool_by_slug($slug);
    if ($tool) {
        $cat = $TOOL_CATEGORIES[$tool['category'] ?? 'utility'] ?? 'tools';
        $parts = [
            $tool['title'],
            str_replace('-', ' ', $tool['slug']),
            'free online ' . strtolower($cat),
            'free ' . str_replace('-', ' ', $tool['slug']),
            SITE_NAME,
        ];
        return implode(', ', array_unique(array_map('trim', $parts)));
    }

    if ($title) {
        return $title . ', ' . SITE_KEYWORDS;
    }

    return SITE_KEYWORDS;
}

function page_meta(string $title, string $description = '', string $keywords = ''): array
{
    $canonical = rtrim(SITE_URL, '/') . ($_SERVER['REQUEST_URI'] ?? '/');
    $path      = trim(parse_url($canonical, PHP_URL_PATH) ?? '/', '/');

    if ($keywords === '' && $path !== '') {
        $keywords = seo_keywords_for_slug($path, $title);
    } elseif ($keywords === '') {
        $keywords = SITE_KEYWORDS;
    }

    return [
        'title'       => $title . ' | ' . SITE_NAME,
        'description' => $description ?: SITE_DESCRIPTION,
        'keywords'    => $keywords,
        'canonical'   => $canonical,
        'og_type'     => ($path === '' || $path === 'index.php') ? 'website' : 'website',
        'og_image'    => rtrim(SITE_URL, '/') . SITE_OG_IMAGE,
    ];
}

function all_site_urls(): array
{
    $urls = [
        ['loc' => rtrim(SITE_URL, '/') . '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => tool_url('about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['loc' => tool_url('privacy'), 'priority' => '0.3', 'changefreq' => 'monthly'],
        ['loc' => tool_url('contact'), 'priority' => '0.4', 'changefreq' => 'monthly'],
    ];

    global $TOOLS;
    foreach ($TOOLS as $tool) {
        $urls[] = [
            'loc'        => tool_url($tool['slug']),
            'priority'   => '0.8',
            'changefreq' => 'monthly',
        ];
    }

    return $urls;
}

function tool_url(string $slug): string
{
    return rtrim(SITE_URL, '/') . '/' . $slug;
}

function tools_by_category(): array
{
    global $TOOLS, $TOOL_CATEGORIES;
    $grouped = [];
    foreach ($TOOL_CATEGORIES as $key => $label) {
        $grouped[$key] = ['label' => $label, 'tools' => []];
    }
    foreach ($TOOLS as $tool) {
        $cat = $tool['category'] ?? 'utility';
        if (!isset($grouped[$cat])) {
            $grouped[$cat] = ['label' => ucfirst($cat), 'tools' => []];
        }
        $grouped[$cat]['tools'][] = $tool;
    }
    return array_filter($grouped, function ($g) {
        return count($g['tools']) > 0;
    });
}
