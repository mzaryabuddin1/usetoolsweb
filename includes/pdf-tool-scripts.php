<?php
/**
 * Shared script tags for PDF tool pages.
 */

function pdf_tool_head(bool $pdfJs = false, bool $jsZip = false): string
{
    $html = '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>';
    $html .= '<script src="/assets/js/tools/pdf-utils.js"></script>';

    if ($pdfJs) {
        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>';
        $html .= '<script>pdfjsLib.GlobalWorkerOptions.workerSrc="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";</script>';
    }

    if ($jsZip) {
        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>';
    }

    return $html;
}

function pdf_tool_script(string $slug): string
{
    return '<script src="/assets/js/tools/' . htmlspecialchars($slug, ENT_QUOTES) . '.js"></script>';
}
