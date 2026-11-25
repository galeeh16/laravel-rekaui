<script setup>
import { Head } from '@inertiajs/vue3';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { computed, nextTick, onBeforeUnmount, reactive, ref, watch } from 'vue';
import EditorToolbar from '@/components/editor/EditorToolbar.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Select from '@/components/ui/Select.vue';
import Switch from '@/components/ui/Switch.vue';
import { toast } from '@/composables/useToast';
import { createEditorExtensions } from '@/lib/editor-extensions';
import { printDocument } from '@/lib/print-document';

/** The draft lives in this browser only (localStorage) until documents are stored on the server. */
const DRAFT_STORAGE_KEY = 'document-editor:draft';
const DEFAULT_TITLE = 'Dokumen tanpa judul';

const DEFAULT_HEADER = `
    <p style="text-align: center"><span style="font-size: 16pt"><strong>NAMA INSTANSI / PERUSAHAAN</strong></span></p>
    <p style="text-align: center"><span style="font-size: 9pt">Jl. Alamat Lengkap No. 1, Kota 12345 · Telp. (021) 1234567 · info@instansi.id</span></p>
`;

const DEFAULT_BODY = `
    <h1 style="text-align: center">Judul Dokumen</h1>
    <p style="text-align: center">Nomor: 001/SK/XI/2026</p>
    <p></p>
    <p>Mulai menulis di sini. Gunakan toolbar di atas untuk mengatur <strong>tebal</strong>, <em>miring</em>, <u>garis bawah</u>, warna, perataan paragraf, daftar, tabel, dan gambar.</p>
    <p>Klik dua kali kop surat di atas atau footer di bawah untuk mengubahnya.</p>
`;

const DEFAULT_FOOTER = `<p><span style="font-size: 8pt; color: #666666">Dokumen ini dibuat secara elektronik.</span></p>`;

/** Height of the text area of one A4 page (297 mm minus 2.54 cm top and bottom margins), in CSS pixels. */
const PAGE_TEXT_HEIGHT_PX = ((297 - 25.4 * 2) / 25.4) * 96;

const zoomOptions = [75, 90, 100, 125, 150].map((value) => ({ value, label: `${value}%` }));

const regionLabels = { header: 'kop surat (header)', footer: 'footer' };

/**
 * @returns {{ title?: string, html?: string, header?: string, footer?: string, settings?: object } | null}
 */
function readDraft() {
    try {
        return JSON.parse(window.localStorage.getItem(DRAFT_STORAGE_KEY));
    } catch {
        return null;
    }
}

const draft = readDraft();
const title = ref(draft?.title ?? DEFAULT_TITLE);
const settings = reactive({ headerFirstPageOnly: true, headerRule: true, pageNumbers: true, ...draft?.settings });
const lastSavedAt = ref(null);
const zoom = ref(100);
const isExporting = ref(false);

/** Which part of the page is being edited: `body`, `header` or `footer`. */
const activeRegion = ref('body');

let saveTimeout = null;
let resizeObserver = null;

/* Registered before useEditor() so it runs before the editors are destroyed on unmount. */
onBeforeUnmount(() => {
    if (saveTimeout) {
        clearTimeout(saveTimeout);
        saveDraft();
    }

    resizeObserver?.disconnect();
});

/** Escape leaves the header/footer and goes back to the body, like Word. */
function leaveOnEscape(view, event) {
    if (event.key !== 'Escape') {
        return false;
    }

    activate('body');

    return true;
}

const bodyEditor = useEditor({
    content: draft?.html ?? DEFAULT_BODY,
    extensions: createEditorExtensions({ placeholder: 'Mulai menulis dokumen…', characterCount: true }),
    editorProps: { attributes: { class: 'document-content', spellcheck: 'false' } },
    onUpdate: scheduleSave,
    onFocus: () => activate('body', { focus: false }),
});

const headerEditor = useEditor({
    content: draft?.header ?? DEFAULT_HEADER,
    editable: false,
    extensions: createEditorExtensions({ placeholder: 'Klik dua kali untuk menambah kop surat', showPlaceholderWhenReadOnly: true }),
    editorProps: { attributes: { class: 'document-content', spellcheck: 'false' }, handleKeyDown: leaveOnEscape },
    onUpdate: scheduleSave,
});

const footerEditor = useEditor({
    content: draft?.footer ?? DEFAULT_FOOTER,
    editable: false,
    extensions: createEditorExtensions({ placeholder: 'Klik dua kali untuk menambah footer', showPlaceholderWhenReadOnly: true }),
    editorProps: { attributes: { class: 'document-content', spellcheck: 'false' }, handleKeyDown: leaveOnEscape },
    onUpdate: scheduleSave,
});

const editors = { body: bodyEditor, header: headerEditor, footer: footerEditor };

/** The editor the toolbar works on. */
const activeEditor = computed(() => editors[activeRegion.value].value);

/**
 * Switch editing between the body, header and footer. Only the active header/footer is editable,
 * so a stray click on the page never changes them.
 */
function activate(region, { focus = true } = {}) {
    activeRegion.value = region;
    headerEditor.value?.setEditable(region === 'header', false);
    footerEditor.value?.setEditable(region === 'footer', false);

    if (focus) {
        nextTick(() => editors[region].value?.commands.focus());
    }
}

function saveDraft() {
    saveTimeout = null;

    if (!bodyEditor.value || bodyEditor.value.isDestroyed) {
        return;
    }

    try {
        window.localStorage.setItem(
            DRAFT_STORAGE_KEY,
            JSON.stringify({
                title: title.value,
                html: bodyEditor.value.getHTML(),
                header: headerEditor.value?.getHTML(),
                footer: footerEditor.value?.getHTML(),
                settings: { ...settings },
            }),
        );
        lastSavedAt.value = new Date();
    } catch {
        // Storage can be full or blocked (private mode); the editor keeps working without it.
    }
}

function scheduleSave() {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveDraft, 500);
}

watch(title, scheduleSave);
watch(settings, scheduleSave, { deep: true });

const saveStatus = computed(() => {
    if (!lastSavedAt.value) {
        return 'Perubahan disimpan otomatis di browser ini';
    }

    const time = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit' }).format(lastSavedAt.value);

    return `Draf tersimpan di browser ini · ${time}`;
});

const wordCount = computed(() => bodyEditor.value?.storage.characterCount.words() ?? 0);
const characterCount = computed(() => bodyEditor.value?.storage.characterCount.characters() ?? 0);

/* The editor is one continuous sheet, so the page count is an estimate from the text height. */
const bodyHeight = ref(0);

watch(bodyEditor, (editor) => {
    if (!editor) {
        return;
    }

    resizeObserver = new ResizeObserver(() => (bodyHeight.value = editor.view.dom.offsetHeight));
    resizeObserver.observe(editor.view.dom);
});

const estimatedPages = computed(() => Math.max(1, Math.ceil(bodyHeight.value / PAGE_TEXT_HEIGHT_PX)));

/** Clicking the body's empty space places the cursor at the end, like a word processor. */
function focusBodyFromPage(event) {
    if (event.target === event.currentTarget) {
        bodyEditor.value?.commands.focus('end');
    }
}

const fileName = computed(() => title.value.replace(/[\\/:*?"<>|]+/g, '').trim() || 'dokumen');

/** Content of the header or footer editor (`getHTML` / `getJSON`), or null when it is empty. */
const contentOf = (editor, method) => (editor.value && !editor.value.isEmpty ? editor.value[method]() : null);

function print() {
    printDocument({
        title: title.value,
        headerHtml: contentOf(headerEditor, 'getHTML'),
        bodyHtml: bodyEditor.value.getHTML(),
        footerHtml: contentOf(footerEditor, 'getHTML'),
        settings: { ...settings },
    });
}

async function downloadDocx() {
    isExporting.value = true;

    try {
        /* Loaded on demand: the docx library is only needed when exporting. */
        const { createDocx } = await import('@/lib/docx-export');

        const blob = await createDocx({
            title: title.value,
            header: contentOf(headerEditor, 'getJSON'),
            body: bodyEditor.value.getJSON(),
            footer: contentOf(footerEditor, 'getJSON'),
            settings: { ...settings },
        });

        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = `${fileName.value}.docx`;
        link.click();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    } catch (error) {
        console.error(error);
        toast({ title: 'Gagal membuat file Word', description: 'Coba lagi, atau gunakan Cetak / PDF.', variant: 'danger' });
    } finally {
        isExporting.value = false;
    }
}

/** Classes for the header/footer areas: highlighted while edited, faded otherwise. */
function regionClasses(region) {
    return activeRegion.value === region
        ? 'bg-brand-soft/25 ring-1 ring-brand/40 ring-inset'
        : 'cursor-default opacity-60 hover:bg-black/3 hover:opacity-100';
}
</script>

<template>
    <Head :title="title || DEFAULT_TITLE" />

    <Card class="mt-6 flex h-[calc(100dvh-10rem)] min-h-[32rem] flex-col overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-line px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="min-w-0 flex-1">
                <input
                    v-model="title"
                    type="text"
                    aria-label="Judul dokumen"
                    :placeholder="DEFAULT_TITLE"
                    class="-mx-2 w-full truncate rounded-lg bg-transparent px-2 py-0.5 text-xl font-medium tracking-tight outline-none transition hover:bg-ink/5 focus:bg-surface focus:ring-3 focus:ring-brand/15 sm:text-2xl"
                />
                <p class="mt-0.5 text-sm text-muted">{{ saveStatus }}</p>
            </div>

            <div class="flex shrink-0 gap-2">
                <Button variant="secondary" icon="printer" :disabled="!bodyEditor" @click="print">Cetak / PDF</Button>
                <Button icon="download" :disabled="!bodyEditor || isExporting" @click="downloadDocx">
                    {{ isExporting ? 'Menyiapkan…' : 'Unduh Word' }}
                </Button>
            </div>
        </div>

        <EditorToolbar v-if="activeEditor" :editor="activeEditor" />
        <div v-else class="h-12 shrink-0 animate-pulse border-b border-line bg-ink/5" />

        <!-- Header & footer options, shown while editing one of them (like Word's Header & Footer tab) -->
        <div
            v-if="activeRegion !== 'body'"
            class="flex flex-wrap items-center gap-x-6 gap-y-2 border-b border-line bg-brand-soft px-4 py-2 text-brand-ink sm:px-5"
        >
            <span class="text-sm font-medium">Mengedit {{ regionLabels[activeRegion] }}</span>

            <template v-if="activeRegion === 'header'">
                <Switch v-model="settings.headerFirstPageOnly" size="sm">Hanya di halaman pertama</Switch>
                <Switch v-model="settings.headerRule" size="sm">Garis ganda di bawah kop</Switch>
            </template>

            <Switch v-else v-model="settings.pageNumbers" size="sm">Nomor halaman</Switch>

            <Button variant="ghost" size="sm" icon="x" class="ml-auto" @click="activate('body')">Selesai (Esc)</Button>
        </div>

        <div class="scrollbar-muted flex-1 overflow-auto bg-ink/8 px-3 py-6 sm:px-8 sm:py-10">
            <div
                class="mx-auto flex min-h-[297mm] w-full flex-col bg-white text-neutral-900 shadow-md shadow-black/10 ring-1 ring-black/5 sm:w-[210mm]"
                :style="{ zoom: zoom / 100 }"
            >
                <!-- Header: the letterhead, in the top margin -->
                <div
                    :class="['group relative px-[12mm] pt-[10mm] pb-[5mm] transition sm:px-[25.4mm] sm:pt-[12.7mm]', regionClasses('header')]"
                    :title="activeRegion === 'header' ? undefined : 'Klik dua kali untuk mengedit kop surat'"
                    @dblclick="activate('header')"
                >
                    <div :class="{ 'document-kop-rule': settings.headerRule && !headerEditor?.isEmpty }">
                        <EditorContent :editor="headerEditor" />
                    </div>

                    <span
                        :class="[
                            'pointer-events-none absolute bottom-0 left-[12mm] z-10 translate-y-1/2 rounded px-1.5 py-0.5 text-[10px] font-medium sm:left-[25.4mm]',
                            activeRegion === 'header' ? 'bg-brand text-white' : 'hidden bg-neutral-200 text-neutral-600 group-hover:block',
                        ]"
                    >
                        Header{{ settings.headerFirstPageOnly ? ' · halaman pertama' : '' }}
                    </span>
                </div>

                <!-- Body -->
                <div
                    :class="['flex-1 cursor-text px-[12mm] py-[6mm] transition sm:px-[25.4mm]', { 'opacity-40': activeRegion !== 'body' }]"
                    @click="focusBodyFromPage"
                >
                    <EditorContent :editor="bodyEditor" />
                </div>

                <!-- Footer: footer text and page number, in the bottom margin -->
                <div
                    :class="['group relative px-[12mm] pt-[5mm] pb-[10mm] transition sm:px-[25.4mm] sm:pb-[12.7mm]', regionClasses('footer')]"
                    :title="activeRegion === 'footer' ? undefined : 'Klik dua kali untuk mengedit footer'"
                    @dblclick="activate('footer')"
                >
                    <span
                        :class="[
                            'pointer-events-none absolute top-0 left-[12mm] z-10 -translate-y-1/2 rounded px-1.5 py-0.5 text-[10px] font-medium sm:left-[25.4mm]',
                            activeRegion === 'footer' ? 'bg-brand text-white' : 'hidden bg-neutral-200 text-neutral-600 group-hover:block',
                        ]"
                    >
                        Footer
                    </span>

                    <EditorContent :editor="footerEditor" />

                    <p v-if="settings.pageNumbers" class="mt-1 text-right" style="font: 9pt Arial, sans-serif; color: #666666">
                        Halaman 1 dari {{ estimatedPages }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 border-t border-line px-4 py-1.5 text-xs text-muted sm:px-5">
            <span class="tabular-nums">
                {{ wordCount }} kata · {{ characterCount }} karakter · ± {{ estimatedPages }} halaman
            </span>

            <div class="flex items-center gap-2">
                <span id="zoom-label">Zoom</span>
                <Select v-model="zoom" :options="zoomOptions" size="sm" class="w-22" aria-labelledby="zoom-label" />
            </div>
        </div>
    </Card>
</template>
