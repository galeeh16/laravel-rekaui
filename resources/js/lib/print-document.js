/**
 * Prints a document from the editor (or saves it as PDF from the print dialog) through a hidden iframe that
 * reuses the app stylesheets, so the printout matches the editor.
 *
 * The footer and a repeated header are `position: fixed`, which browsers repeat on every printed page; table
 * header/footer rows of the same height keep the text from running underneath them. Page numbers use CSS page
 * margin boxes (Chrome and Edge; other browsers print without them).
 *
 * Usage: printDocument({ title, headerHtml, bodyHtml, footerHtml, settings });
 */

/** Space between the header/footer and the text, in millimetres. */
const GAP_MM = 6;

function escapeHtml(text) {
    return text.replace(/[&<>"']/g, (character) => `&#${character.charCodeAt(0)};`);
}

/**
 * @param {{
 *     title: string,
 *     headerHtml: string | null,
 *     bodyHtml: string,
 *     footerHtml: string | null,
 *     settings: { headerFirstPageOnly: boolean, headerRule: boolean, pageNumbers: boolean },
 * }} input  `headerHtml` / `footerHtml` are null when that part is empty.
 */
export function printDocument({ title, headerHtml, bodyHtml, footerHtml, settings }) {
    const stylesheets = [...document.querySelectorAll('link[rel="stylesheet"], style')].map((node) => node.outerHTML).join('');

    const repeatHeader = Boolean(headerHtml) && !settings.headerFirstPageOnly;
    const headerClass = `document-content${settings.headerRule ? ' document-kop-rule' : ''}`;
    const header = headerHtml ? `<div class="${headerClass}">${headerHtml}</div>` : '';
    const footer = footerHtml ? `<div class="print-footer document-content">${footerHtml}</div>` : '';

    const pageNumber = settings.pageNumbers
        ? `@bottom-right { content: "Halaman " counter(page) " dari " counter(pages); font: 9pt Arial, sans-serif; color: #666666; }`
        : '';

    /* With a header the page starts higher (the header sits in the top margin, like Word). */
    const firstPageTop = headerHtml ? '12.7mm' : '25.4mm';
    const otherPagesTop = repeatHeader ? '12.7mm' : '25.4mm';

    const css = `
        @page { size: A4; margin: ${otherPagesTop} 25.4mm 20mm; ${pageNumber} }
        @page :first { margin-top: ${firstPageTop}; }
        html, body { margin: 0; background: #ffffff; }
        .print-layout { width: 100%; border-collapse: collapse; }
        .print-layout > * > tr > td { padding: 0; }
        .print-header, .print-footer { position: fixed; left: 0; right: 0; background: #ffffff; }
        .print-header { top: 0; }
        .print-footer { bottom: 0; }
        .print-first-header { margin-bottom: ${GAP_MM}mm; }
    `;

    const frame = document.createElement('iframe');

    frame.setAttribute('aria-hidden', 'true');
    /* Off screen but as wide as the printed text, so the header/footer heights measured below are right. */
    frame.style.cssText = 'position: fixed; top: 0; left: -10000px; width: 159.2mm; height: 297mm; border: 0;';
    frame.srcdoc = `<!doctype html><html><head><meta charset="utf-8"><title>${escapeHtml(title)}</title>${stylesheets}<style>${css}</style></head>
        <body>
            ${repeatHeader ? `<div class="print-header">${header}</div>` : ''}
            ${footer}
            <table class="print-layout">
                <thead><tr><td><div class="print-header-space"></div></td></tr></thead>
                <tbody><tr><td>
                    ${headerHtml && !repeatHeader ? `<div class="print-first-header">${header}</div>` : ''}
                    <div class="document-content">${bodyHtml}</div>
                </td></tr></tbody>
                <tfoot><tr><td><div class="print-footer-space"></div></td></tr></tfoot>
            </table>
        </body></html>`;

    frame.addEventListener('load', () => {
        const printDocument = frame.contentDocument;
        const reserveSpace = (fixedSelector, spaceSelector) => {
            const fixed = printDocument.querySelector(fixedSelector);

            if (fixed) {
                printDocument.querySelector(spaceSelector).style.height = `calc(${fixed.offsetHeight}px + ${GAP_MM}mm)`;
            }
        };

        reserveSpace('.print-header', '.print-header-space');
        reserveSpace('.print-footer', '.print-footer-space');

        frame.contentWindow.addEventListener('afterprint', () => frame.remove());
        frame.contentWindow.focus();
        frame.contentWindow.print();
    });

    document.body.appendChild(frame);
}
