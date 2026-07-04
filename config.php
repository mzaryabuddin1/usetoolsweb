<?php
/**
 * usetoolsweb.com site configuration
 */

if (defined('SITE_CONFIG_LOADED')) {
    return;
}
define('SITE_CONFIG_LOADED', true);
define('QUICKTOOLS_CONFIG_LOADED', true); // legacy alias

define('SITE_NAME', 'usetoolsweb');
define('SITE_DOMAIN', 'usetoolsweb.com');
define('SITE_TAGLINE', 'Free Online Tools — Fast, Simple, Private');
define('SITE_DESCRIPTION', 'Free online tools for everyday tasks — PDF tools, image tools, calculators, developer utilities, text tools, and more. All in your browser.');
define('SITE_AUTHOR', 'usetoolsweb');
define('SITE_EMAIL', 'hello@usetoolsweb.com');

if (!defined('SITE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('SITE_URL', $protocol . '://' . $host);
}

define('SITE_FULL_NAME', SITE_NAME);

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

/** PDF tools — max upload size and temp storage for server-side processing */
define('PDF_MAX_BYTES', 50 * 1024 * 1024);
define('PDF_TMP_DIR', __DIR__ . '/tmp/pdf');

/** Leave empty to auto-detect Ghostscript, qpdf, LibreOffice on the server */
define('GHOSTSCRIPT_BINARY', '');
define('QPDF_BINARY', '');
define('LIBREOFFICE_BINARY', '');

/** cURL runner proxy — max response size and timeout (seconds) */
define('CURL_PROXY_MAX_BYTES', 10 * 1024 * 1024);
define('CURL_PROXY_TIMEOUT', 30);

/** Tool categories for home page grouping */
$TOOL_CATEGORIES = [
    'pdf'        => 'PDF Tools',
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
    ['slug' => 'background-remover', 'title' => 'Background Remover', 'description' => 'Remove image backgrounds with AI — runs locally in your browser.', 'icon' => '✂️', 'category' => 'image'],
    ['slug' => 'resize-image', 'title' => 'Image Resizer', 'description' => 'Resize images to exact width and height or by percentage.', 'icon' => '📐', 'category' => 'image'],
    ['slug' => 'image-converter', 'title' => 'Image Converter', 'description' => 'Convert PNG, JPG, and WebP images to another format.', 'icon' => '🔄', 'category' => 'image'],
    ['slug' => 'image-to-base64', 'title' => 'Image to Base64', 'description' => 'Convert images to Base64 data URIs and back.', 'icon' => '🔢', 'category' => 'image'],
    ['slug' => 'favicon-generator', 'title' => 'Favicon Generator', 'description' => 'Create favicon PNGs from any image in multiple sizes.', 'icon' => '⭐', 'category' => 'image'],
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
    ['slug' => 'timestamp-converter', 'title' => 'Timestamp Converter', 'description' => 'Live Unix ms clock, convert epochs, ISO 8601, HTTP date, Windows ticks, NTP, GPS, and code snippets.', 'icon' => '🕐', 'category' => 'developer'],
    ['slug' => 'curl-runner', 'title' => 'cURL Runner', 'description' => 'Paste a cURL command from Postman or anywhere, parse it, and send the HTTP request instantly.', 'icon' => '🌐', 'category' => 'developer'],
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
    // --- PDF Tools (Organize) ---
    ['slug' => 'pdf-tools', 'title' => 'All PDF Tools', 'description' => 'Browse every PDF tool — merge, split, compress, convert, edit, and secure PDFs.', 'icon' => '📚', 'category' => 'pdf'],
    ['slug' => 'pdf-merge', 'title' => 'Merge PDF', 'description' => 'Combine multiple PDF files into one document in order.', 'icon' => '📎', 'category' => 'pdf'],
    ['slug' => 'pdf-split', 'title' => 'Split PDF', 'description' => 'Split a PDF into separate files by page or page ranges.', 'icon' => '✂️', 'category' => 'pdf'],
    ['slug' => 'pdf-remove-pages', 'title' => 'Remove PDF Pages', 'description' => 'Delete selected pages from a PDF document.', 'icon' => '🗑️', 'category' => 'pdf'],
    ['slug' => 'pdf-extract-pages', 'title' => 'Extract PDF Pages', 'description' => 'Extract specific pages into a new PDF file.', 'icon' => '📄', 'category' => 'pdf'],
    ['slug' => 'pdf-organize', 'title' => 'Organize PDF', 'description' => 'Reorder, rotate, and rearrange PDF pages.', 'icon' => '📋', 'category' => 'pdf'],
    ['slug' => 'scan-to-pdf', 'title' => 'Scan to PDF', 'description' => 'Turn photos or scans (JPG, PNG) into a single PDF.', 'icon' => '📷', 'category' => 'pdf'],
    // --- PDF Tools (Optimize) ---
    ['slug' => 'pdf-compress', 'title' => 'Compress PDF', 'description' => 'Reduce PDF file size while keeping good quality.', 'icon' => '🗜️', 'category' => 'pdf'],
    ['slug' => 'pdf-repair', 'title' => 'Repair PDF', 'description' => 'Fix corrupted or damaged PDF files.', 'icon' => '🔧', 'category' => 'pdf'],
    ['slug' => 'ocr-pdf', 'title' => 'OCR PDF', 'description' => 'Make scanned PDFs searchable with OCR (server processing).', 'icon' => '🔍', 'category' => 'pdf'],
    // --- PDF Tools (Convert to PDF) ---
    ['slug' => 'jpg-to-pdf', 'title' => 'JPG to PDF', 'description' => 'Convert JPG and PNG images into a PDF document.', 'icon' => '🖼️', 'category' => 'pdf'],
    ['slug' => 'word-to-pdf', 'title' => 'Word to PDF', 'description' => 'Convert DOC and DOCX files to PDF.', 'icon' => '📝', 'category' => 'pdf'],
    ['slug' => 'excel-to-pdf', 'title' => 'Excel to PDF', 'description' => 'Convert XLS and XLSX spreadsheets to PDF.', 'icon' => '📊', 'category' => 'pdf'],
    ['slug' => 'ppt-to-pdf', 'title' => 'PowerPoint to PDF', 'description' => 'Convert PPT and PPTX presentations to PDF.', 'icon' => '📽️', 'category' => 'pdf'],
    ['slug' => 'html-to-pdf', 'title' => 'HTML to PDF', 'description' => 'Convert a webpage URL or HTML file to PDF.', 'icon' => '🌐', 'category' => 'pdf'],
    // --- PDF Tools (Convert from PDF) ---
    ['slug' => 'pdf-to-jpg', 'title' => 'PDF to JPG', 'description' => 'Convert PDF pages to JPG images in your browser.', 'icon' => '📑', 'category' => 'pdf'],
    ['slug' => 'pdf-to-word', 'title' => 'PDF to Word', 'description' => 'Convert PDF files to editable DOCX documents.', 'icon' => '📃', 'category' => 'pdf'],
    ['slug' => 'pdf-to-excel', 'title' => 'PDF to Excel', 'description' => 'Convert PDF tables and data to Excel spreadsheets.', 'icon' => '📈', 'category' => 'pdf'],
    ['slug' => 'pdf-to-ppt', 'title' => 'PDF to PowerPoint', 'description' => 'Convert PDF slides to editable PPTX presentations.', 'icon' => '🎞️', 'category' => 'pdf'],
    ['slug' => 'pdf-to-pdfa', 'title' => 'PDF to PDF/A', 'description' => 'Convert PDF to PDF/A for long-term archiving.', 'icon' => '🏛️', 'category' => 'pdf'],
    // --- PDF Tools (Edit) ---
    ['slug' => 'pdf-rotate', 'title' => 'Rotate PDF', 'description' => 'Rotate PDF pages 90°, 180°, or 270°.', 'icon' => '🔄', 'category' => 'pdf'],
    ['slug' => 'pdf-page-numbers', 'title' => 'Add Page Numbers', 'description' => 'Add page numbers to your PDF document.', 'icon' => '🔢', 'category' => 'pdf'],
    ['slug' => 'pdf-watermark', 'title' => 'Add Watermark', 'description' => 'Stamp text or image watermarks on PDF pages.', 'icon' => '💧', 'category' => 'pdf'],
    ['slug' => 'pdf-crop', 'title' => 'Crop PDF', 'description' => 'Crop PDF margins or trim page edges.', 'icon' => '✂️', 'category' => 'pdf'],
    ['slug' => 'pdf-edit', 'title' => 'Edit PDF', 'description' => 'Add text annotations to PDF pages.', 'icon' => '✏️', 'category' => 'pdf'],
    // --- PDF Tools (Security) ---
    ['slug' => 'pdf-unlock', 'title' => 'Unlock PDF', 'description' => 'Remove password protection from PDF files.', 'icon' => '🔓', 'category' => 'pdf'],
    ['slug' => 'pdf-protect', 'title' => 'Protect PDF', 'description' => 'Encrypt PDF files with a password.', 'icon' => '🔒', 'category' => 'pdf'],
    ['slug' => 'pdf-sign', 'title' => 'Sign PDF', 'description' => 'Add your signature image to a PDF document.', 'icon' => '✍️', 'category' => 'pdf'],
    ['slug' => 'pdf-redact', 'title' => 'Redact PDF', 'description' => 'Permanently black out sensitive text or areas.', 'icon' => '⬛', 'category' => 'pdf'],
    ['slug' => 'pdf-compare', 'title' => 'Compare PDF', 'description' => 'Compare two PDF files page by page.', 'icon' => '⚖️', 'category' => 'pdf'],
    ['slug' => 'dice-roller', 'title' => 'Dice Roller', 'description' => 'Roll virtual dice — d4, d6, d8, d10, d12, d20.', 'icon' => '🎲', 'category' => 'utility'],
    ['slug' => 'online-timer', 'title' => 'Online Timer', 'description' => 'Countdown timer and stopwatch in your browser.', 'icon' => '⏱️', 'category' => 'utility'],
    ['slug' => 'card-validator', 'title' => 'Credit Card Validator', 'description' => 'Validate credit card numbers using the Luhn algorithm.', 'icon' => '💳', 'category' => 'utility'],
    ['slug' => 'iban-validator', 'title' => 'IBAN Validator', 'description' => 'Validate International Bank Account Numbers.', 'icon' => '🏛️', 'category' => 'utility'],
    ['slug' => 'video-cutter', 'title' => 'Video Cutter', 'description' => 'Trim and cut videos online. Preview in browser, export with FFmpeg — deleted after processing.', 'icon' => '🎬', 'category' => 'utility'],
    ['slug' => 'audio-cutter', 'title' => 'Audio Cutter', 'description' => 'Trim audio with waveform. Reorder clips, insert silence, remove blank gaps, export MP3 or WAV.', 'icon' => '🎵', 'category' => 'utility'],
];

/** SEO — default site keywords (used on home + appended on tool pages) */
define('SITE_KEYWORDS', 'online tools, free tools, web utilities, usetoolsweb, pdf tools, developer tools, image tools, calculators, text tools');

/** OG default image — social share preview */
define('SITE_OG_IMAGE', '/assets/images/og-default.png');

/** Site logo — day (light mode) and night (dark mode) */
define('SITE_LOGO', '/assets/images/logo-light.png');
define('SITE_LOGO_DARK', '/assets/images/logo-dark.png');
define('SITE_LOGO_ALT', 'usetoolsweb');

/** Favicons — UTW gradient icon set */
define('SITE_FAVICON_SVG', '/assets/images/favicon.svg');
define('SITE_FAVICON_32', '/assets/images/favicon-32x32.png');
define('SITE_FAVICON_16', '/assets/images/favicon-16x16.png');
define('SITE_APPLE_TOUCH_ICON', '/assets/images/apple-touch-icon.png');
define('SITE_THEME_COLOR', '#0a2558');

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
