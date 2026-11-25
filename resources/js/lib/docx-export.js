/**
 * Converts Tiptap documents (JSON from `editor.getJSON()`) into a real .docx file with the `docx` library:
 * A4 page, header (kop surat) that can be limited to the first page, and a footer with page numbers.
 *
 * Usage: const blob = await createDocx({ title, header, body, footer, settings });
 */
import {
    AlignmentType,
    BorderStyle,
    Document,
    ExternalHyperlink,
    Footer,
    Header,
    ImageRun,
    LevelFormat,
    Packer,
    PageNumber,
    Paragraph,
    ShadingType,
    Table,
    TableCell,
    TableLayoutType,
    TableRow,
    TextRun,
    WidthType,
} from 'docx';

/** A4 in twentieths of a point (twips), with 2.54 cm margins like the editor page. */
const PAGE_WIDTH = 11906;
const PAGE_HEIGHT = 16838;
const PAGE_MARGIN = 1440;
const HEADER_FOOTER_DISTANCE = 720;
const CONTENT_WIDTH = PAGE_WIDTH - PAGE_MARGIN * 2;

/** Widest an image may be, in pixels (the content width at 96 dpi). */
const MAX_IMAGE_WIDTH = Math.round((CONTENT_WIDTH / 1440) * 96);

const ORDERED_LIST_REFERENCE = 'ordered-list';
const LIST_TYPES = ['bulletList', 'orderedList', 'taskList'];
const LIST_INDENT = 360;

const ALIGNMENTS = {
    left: AlignmentType.LEFT,
    center: AlignmentType.CENTER,
    right: AlignmentType.RIGHT,
    justify: AlignmentType.JUSTIFIED,
};

const HEADING_SIZES = { 1: 40, 2: 32, 3: 26 };

/**
 * `#rgb`, `#rrggbb` or `rgb(r, g, b)` to the `RRGGBB` docx expects, or null.
 */
function toHexColor(color) {
    if (!color) {
        return null;
    }

    const value = color.trim();
    const shortHex = value.match(/^#([0-9a-f])([0-9a-f])([0-9a-f])$/i);

    if (shortHex) {
        return shortHex.slice(1).map((digit) => digit + digit).join('').toUpperCase();
    }

    if (/^#[0-9a-f]{6}$/i.test(value)) {
        return value.slice(1).toUpperCase();
    }

    const rgb = value.match(/^rgba?\(\s*(\d+)[\s,]+(\d+)[\s,]+(\d+)/i);

    if (rgb) {
        return rgb.slice(1, 4).map((channel) => Number(channel).toString(16).padStart(2, '0')).join('').toUpperCase();
    }

    return null;
}

/**
 * CSS font size (`12pt`, `16px`) to docx half-points, or null.
 */
function toHalfPoints(fontSize) {
    const match = String(fontSize ?? '').match(/^([\d.]+)\s*(pt|px)?$/i);

    if (!match) {
        return null;
    }

    const points = match[2]?.toLowerCase() === 'px' ? Number(match[1]) * 0.75 : Number(match[1]);

    return Math.round(points * 2);
}

/**
 * docx run options for a text node's marks, merged over `defaults` (e.g. bold for table header cells).
 */
function runOptions(marks = [], defaults = {}) {
    const options = { ...defaults };

    for (const mark of marks) {
        const attrs = mark.attrs ?? {};

        switch (mark.type) {
            case 'bold':
                options.bold = true;
                break;
            case 'italic':
                options.italics = true;
                break;
            case 'underline':
                options.underline = {};
                break;
            case 'strike':
                options.strike = true;
                break;
            case 'superscript':
                options.superScript = true;
                break;
            case 'subscript':
                options.subScript = true;
                break;
            case 'code':
                options.font = 'Courier New';
                options.shading = { type: ShadingType.CLEAR, color: 'auto', fill: 'F1F1EF' };
                break;
            case 'highlight':
                options.shading = { type: ShadingType.CLEAR, color: 'auto', fill: toHexColor(attrs.color) ?? 'FFFF00' };
                break;
            case 'link':
                options.color = '1A56C4';
                options.underline = {};
                break;
            case 'textStyle': {
                const color = toHexColor(attrs.color);
                const size = toHalfPoints(attrs.fontSize);
                const font = attrs.fontFamily?.split(',')[0].replace(/["']/g, '').trim();

                if (color) {
                    options.color = color;
                }

                if (size) {
                    options.size = size;
                }

                if (font) {
                    options.font = font;
                }

                break;
            }
        }
    }

    return options;
}

/**
 * Inline content of a paragraph-like node as docx runs and hyperlinks.
 */
function inlineChildren(nodes = [], context, runDefaults = {}) {
    return nodes.flatMap((node) => {
        if (node.type === 'hardBreak') {
            return [new TextRun({ break: 1 })];
        }

        if (node.type === 'image') {
            return imageRun(node, context);
        }

        if (node.type !== 'text') {
            return [];
        }

        const run = new TextRun({ text: node.text, ...runOptions(node.marks, runDefaults) });
        const link = node.marks?.find((mark) => mark.type === 'link');

        return link?.attrs?.href ? [new ExternalHyperlink({ link: link.attrs.href, children: [run] })] : [run];
    });
}

function imageRun(node, context) {
    const image = context.images.get(node.attrs?.src);

    if (!image) {
        return node.attrs?.alt ? [new TextRun({ text: `[${node.attrs.alt}]`, italics: true, color: '888888' })] : [];
    }

    const scale = Math.min(1, MAX_IMAGE_WIDTH / image.width);

    return [
        new ImageRun({
            type: image.type,
            data: image.data,
            transformation: { width: Math.round(image.width * scale), height: Math.round(image.height * scale) },
            altText: node.attrs?.alt ? { name: node.attrs.alt, description: node.attrs.alt, title: node.attrs.alt } : undefined,
        }),
    ];
}

function paragraph(node, context, extras = {}, runDefaults = {}) {
    return new Paragraph({
        alignment: ALIGNMENTS[node.attrs?.textAlign],
        ...extras,
        children: inlineChildren(node.content, context, runDefaults),
    });
}

/**
 * List items: the first paragraph of each item carries the bullet or number, the rest are indented with it.
 */
function listBlocks(node, context, level) {
    const instance = node.type === 'orderedList' ? ++context.listInstance : null;

    return (node.content ?? []).flatMap((item) =>
        (item.content ?? []).flatMap((child, childIndex) => {
            if (LIST_TYPES.includes(child.type)) {
                return listBlocks(child, context, level + 1);
            }

            const indent = { left: LIST_INDENT * (level + 1), hanging: LIST_INDENT };
            const isFirst = childIndex === 0;

            if (node.type === 'taskList') {
                const checkbox = item.attrs?.checked ? '☑' : '☐';
                const text = isFirst ? [new TextRun({ text: `${checkbox} ` })] : [];

                return new Paragraph({
                    indent,
                    children: [...text, ...inlineChildren(child.content, context)],
                });
            }

            if (!isFirst) {
                return blocks(child, context, { indent: { left: LIST_INDENT * (level + 1) } });
            }

            const marker =
                node.type === 'orderedList'
                    ? { numbering: { reference: ORDERED_LIST_REFERENCE, level: Math.min(level, 8), instance } }
                    : { bullet: { level: Math.min(level, 8) } };

            return blocks(child, context, marker);
        }),
    );
}

function table(node, context) {
    const rows = node.content ?? [];
    const firstRow = rows[0]?.content ?? [];
    const columnCount = Math.max(1, firstRow.reduce((total, cell) => total + (cell.attrs?.colspan ?? 1), 0));

    /* Use the widths dragged in the editor when every column has one, otherwise equal columns. */
    const draggedWidths = firstRow.flatMap((cell) => cell.attrs?.colwidth ?? Array(cell.attrs?.colspan ?? 1).fill(null));
    const hasDraggedWidths = draggedWidths.length === columnCount && draggedWidths.every(Boolean);
    const totalDragged = hasDraggedWidths ? draggedWidths.reduce((sum, width) => sum + width, 0) : 0;
    const columnWidths = hasDraggedWidths
        ? draggedWidths.map((width) => Math.round((width / totalDragged) * CONTENT_WIDTH))
        : Array(columnCount).fill(Math.floor(CONTENT_WIDTH / columnCount));

    return new Table({
        width: { size: CONTENT_WIDTH, type: WidthType.DXA },
        columnWidths,
        layout: TableLayoutType.FIXED,
        rows: rows.map((row) => {
            let columnIndex = 0;

            return new TableRow({
                tableHeader: (row.content ?? []).every((cell) => cell.type === 'tableHeader'),
                children: (row.content ?? []).map((cell) => {
                    const isHeader = cell.type === 'tableHeader';
                    const children = (cell.content ?? []).flatMap((child) => blocks(child, context, {}, isHeader ? { bold: true } : {}));
                    const span = cell.attrs?.colspan ?? 1;

                    /* Explicit cell widths: Google Docs and Pages ignore the table grid without them. */
                    const width = columnWidths.slice(columnIndex, columnIndex + span).reduce((sum, columnWidth) => sum + columnWidth, 0);
                    columnIndex += span;

                    return new TableCell({
                        width: width > 0 ? { size: width, type: WidthType.DXA } : undefined,
                        columnSpan: span,
                        rowSpan: cell.attrs?.rowspan ?? 1,
                        margins: { top: 60, bottom: 60, left: 100, right: 100 },
                        shading: isHeader ? { type: ShadingType.CLEAR, color: 'auto', fill: 'F3F3F3' } : undefined,
                        children: children.length > 0 ? children : [new Paragraph({})],
                    });
                }),
            });
        }),
    });
}

/**
 * One Tiptap block node as docx paragraphs and tables.
 */
function blocks(node, context, extras = {}, runDefaults = {}) {
    switch (node.type) {
        case 'paragraph':
            return [paragraph(node, context, extras, runDefaults)];

        case 'heading': {
            const size = HEADING_SIZES[node.attrs?.level] ?? HEADING_SIZES[3];

            return [paragraph(node, context, { spacing: { before: 240, after: 120 }, keepNext: true, ...extras }, { bold: true, size, ...runDefaults })];
        }

        case 'bulletList':
        case 'orderedList':
        case 'taskList':
            return listBlocks(node, context, 0);

        case 'blockquote':
            return (node.content ?? []).flatMap((child) =>
                blocks(
                    child,
                    context,
                    {
                        indent: { left: 360 },
                        border: { left: { style: BorderStyle.SINGLE, size: 18, color: 'D4D4D4', space: 8 } },
                        ...extras,
                    },
                    { color: '555555', ...runDefaults },
                ),
            );

        case 'codeBlock': {
            const lines = (node.content ?? []).map((child) => child.text ?? '').join('').split('\n');

            return [
                new Paragraph({
                    shading: { type: ShadingType.CLEAR, color: 'auto', fill: 'F6F6F4' },
                    spacing: { line: 276 },
                    ...extras,
                    children: lines.map((line, index) => new TextRun({ text: line, font: 'Courier New', size: 20, break: index > 0 ? 1 : 0 })),
                }),
            ];
        }

        case 'horizontalRule':
            return [new Paragraph({ border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: 'D4D4D4', space: 1 } } })];

        case 'image':
            return [new Paragraph({ ...extras, children: imageRun(node, context) })];

        case 'table':
            return [table(node, context)];

        default:
            return (node.content ?? []).flatMap((child) => blocks(child, context, extras, runDefaults));
    }
}

function documentBlocks(doc, context) {
    return (doc?.content ?? []).flatMap((node) => blocks(node, context));
}

function collectImageSources(doc, sources = new Set()) {
    if (doc?.type === 'image' && doc.attrs?.src) {
        sources.add(doc.attrs.src);
    }

    (doc?.content ?? []).forEach((child) => collectImageSources(child, sources));

    return sources;
}

const DOCX_IMAGE_TYPES = { 'image/png': 'png', 'image/jpeg': 'jpg', 'image/gif': 'gif', 'image/bmp': 'bmp' };

/**
 * Image bytes and size for docx. Other formats (WebP, SVG…) are converted to PNG.
 * Returns null when the image can't be read (e.g. another site blocks cross-origin requests).
 *
 * @returns {Promise<{ data: ArrayBuffer, type: string, width: number, height: number } | null>}
 */
async function loadImage(src) {
    try {
        const blob = await (await fetch(src)).blob();
        const objectUrl = URL.createObjectURL(blob);

        try {
            const image = new Image();
            image.src = objectUrl;
            await image.decode();

            const width = image.naturalWidth || 300;
            const height = image.naturalHeight || 150;
            const type = DOCX_IMAGE_TYPES[blob.type];

            if (type) {
                return { data: await blob.arrayBuffer(), type, width, height };
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(image, 0, 0, width, height);

            const png = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));

            return png ? { data: await png.arrayBuffer(), type: 'png', width, height } : null;
        } finally {
            URL.revokeObjectURL(objectUrl);
        }
    } catch {
        return null;
    }
}

const ORDERED_LIST_FORMATS = [LevelFormat.DECIMAL, LevelFormat.LOWER_LETTER, LevelFormat.LOWER_ROMAN];

/**
 * @param {{
 *     title: string,
 *     header: object,
 *     body: object,
 *     footer: object,
 *     settings: { headerFirstPageOnly: boolean, headerRule: boolean, pageNumbers: boolean },
 * }} input  Tiptap JSON documents plus the header/footer settings.
 * @returns {Promise<Blob>}
 */
export async function createDocx({ title, header, body, footer, settings }) {
    const sources = [header, body, footer].reduce((all, doc) => collectImageSources(doc, all), new Set());
    const images = new Map();

    await Promise.all(
        [...sources].map(async (src) => {
            const image = await loadImage(src);

            if (image) {
                images.set(src, image);
            }
        }),
    );

    const context = { images, listInstance: 0 };

    const headerChildren = documentBlocks(header, context);

    if (settings.headerRule && headerChildren.length > 0) {
        headerChildren.push(
            new Paragraph({
                spacing: { after: 0, line: 240 },
                border: { bottom: { style: BorderStyle.DOUBLE, size: 6, color: '1F1F1F', space: 1 } },
            }),
        );
    }

    const footerChildren = documentBlocks(footer, context);

    if (settings.pageNumbers) {
        footerChildren.push(
            new Paragraph({
                alignment: AlignmentType.RIGHT,
                children: [new TextRun({ children: ['Halaman ', PageNumber.CURRENT, ' dari ', PageNumber.TOTAL_PAGES], size: 18, color: '666666' })],
            }),
        );
    }

    const kopHeader = new Header({ children: headerChildren });
    const pageFooter = new Footer({ children: footerChildren });

    const document = new Document({
        title,
        creator: 'Manajemen surat',
        styles: {
            default: {
                document: {
                    run: { font: 'Arial', size: 22, color: '1F1F1F' },
                    paragraph: { spacing: { after: 132, line: 360 } },
                },
            },
        },
        numbering: {
            config: [
                {
                    reference: ORDERED_LIST_REFERENCE,
                    levels: Array.from({ length: 9 }, (_, level) => ({
                        level,
                        format: ORDERED_LIST_FORMATS[level % ORDERED_LIST_FORMATS.length],
                        text: `%${level + 1}.`,
                        alignment: AlignmentType.START,
                        style: { paragraph: { indent: { left: LIST_INDENT * (level + 1), hanging: LIST_INDENT } } },
                    })),
                },
            ],
        },
        sections: [
            {
                properties: {
                    titlePage: settings.headerFirstPageOnly,
                    page: {
                        size: { width: PAGE_WIDTH, height: PAGE_HEIGHT },
                        margin: {
                            top: PAGE_MARGIN,
                            right: PAGE_MARGIN,
                            bottom: PAGE_MARGIN,
                            left: PAGE_MARGIN,
                            header: HEADER_FOOTER_DISTANCE,
                            footer: HEADER_FOOTER_DISTANCE,
                        },
                    },
                },
                headers: settings.headerFirstPageOnly ? { first: kopHeader, default: new Header({ children: [] }) } : { default: kopHeader },
                footers: settings.headerFirstPageOnly ? { first: pageFooter, default: pageFooter } : { default: pageFooter },
                children: documentBlocks(body, context),
            },
        ],
    });

    return Packer.toBlob(document);
}
