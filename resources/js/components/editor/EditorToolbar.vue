<script setup>
/**
 * Word-style formatting toolbar for a Tiptap editor (see `pages/Editor.vue` for the extensions it expects).
 * Every control reads its state from the editor, so it follows the cursor.
 *
 * Usage: <EditorToolbar :editor="editor" />
 */
import { ToolbarRoot } from 'reka-ui';
import { computed } from 'vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Select from '@/components/ui/Select.vue';
import ToolbarSeparator from '@/components/ui/ToolbarSeparator.vue';
import ToolbarToggleGroup from '@/components/ui/ToolbarToggleGroup.vue';
import EditorColorPicker from './EditorColorPicker.vue';
import EditorImagePopover from './EditorImagePopover.vue';
import EditorLinkPopover from './EditorLinkPopover.vue';
import EditorToolbarButton from './EditorToolbarButton.vue';

const props = defineProps({
    editor: { type: Object, required: true },
});

/** Select value for "use the document default" (reka-ui Select does not allow empty strings). */
const DEFAULT_VALUE = 'default';

const blockTypeOptions = [
    { value: 'paragraph', label: 'Teks normal' },
    { value: 'h1', label: 'Judul 1' },
    { value: 'h2', label: 'Judul 2' },
    { value: 'h3', label: 'Judul 3' },
];

const fontFamilyOptions = [
    { value: DEFAULT_VALUE, label: 'Arial (bawaan)' },
    { value: 'Calibri', label: 'Calibri' },
    { value: 'Cambria', label: 'Cambria' },
    { value: 'Courier New', label: 'Courier New' },
    { value: 'Georgia', label: 'Georgia' },
    { value: 'Tahoma', label: 'Tahoma' },
    { value: 'Times New Roman', label: 'Times New Roman' },
    { value: 'Verdana', label: 'Verdana' },
];

const fontSizeOptions = [
    { value: DEFAULT_VALUE, label: '11 (bawaan)' },
    ...[8, 9, 10, 12, 14, 16, 18, 20, 24, 28, 32, 36, 48, 72].map((size) => ({ value: `${size}pt`, label: String(size) })),
];

const alignmentOptions = [
    { value: 'left', label: 'Rata kiri', icon: 'align-left' },
    { value: 'center', label: 'Rata tengah', icon: 'align-center' },
    { value: 'right', label: 'Rata kanan', icon: 'align-right' },
    { value: 'justify', label: 'Rata kiri-kanan', icon: 'align-justify' },
];

const textColors = [
    '#000000', '#434343', '#666666', '#999999', '#b7b7b7', '#cccccc', '#efefef', '#ffffff',
    '#980000', '#ff0000', '#ff9900', '#ffff00', '#00ff00', '#00ffff', '#4a86e8', '#0000ff',
    '#9900ff', '#ff00ff', '#85200c', '#b45f06', '#38761d', '#134f5c', '#1155cc', '#351c75',
];

const highlightColors = [
    '#fff2cc', '#ffe599', '#ffff00', '#d9ead3', '#b6d7a8', '#d0e0e3', '#c9daf8', '#cfe2f3',
    '#d9d2e9', '#ead1dc', '#f4cccc', '#fce5cd', '#f9cb9c', '#a2c4c9', '#a4c2f4', '#efefef',
];

const blockType = computed({
    get() {
        const level = [1, 2, 3].find((headingLevel) => props.editor.isActive('heading', { level: headingLevel }));

        return level ? `h${level}` : 'paragraph';
    },
    set(value) {
        const chain = props.editor.chain().focus();

        if (value === 'paragraph') {
            chain.setParagraph().run();
        } else {
            chain.setHeading({ level: Number(value.slice(1)) }).run();
        }
    },
});

const fontFamily = computed({
    /** Browsers quote multi-word names when serializing styles, so strip quotes before comparing. */
    get: () => props.editor.getAttributes('textStyle').fontFamily?.replace(/["']/g, '') ?? DEFAULT_VALUE,
    set(value) {
        const chain = props.editor.chain().focus();

        if (value === DEFAULT_VALUE) {
            chain.unsetFontFamily().run();
        } else {
            chain.setFontFamily(value).run();
        }
    },
});

const fontSize = computed({
    get: () => props.editor.getAttributes('textStyle').fontSize ?? DEFAULT_VALUE,
    set(value) {
        const chain = props.editor.chain().focus();

        if (value === DEFAULT_VALUE) {
            chain.unsetFontSize().run();
        } else {
            chain.setFontSize(value).run();
        }
    },
});

const alignment = computed({
    get: () => alignmentOptions.find((option) => props.editor.isActive({ textAlign: option.value }))?.value ?? 'left',
    set: (value) => props.editor.chain().focus().setTextAlign(value).run(),
});

const textColor = computed(() => props.editor.getAttributes('textStyle').color ?? null);
const highlightColor = computed(() => props.editor.getAttributes('highlight').color ?? null);

function setTextColor(color) {
    const chain = props.editor.chain().focus();

    if (color) {
        chain.setColor(color).run();
    } else {
        chain.unsetColor().run();
    }
}

function setHighlightColor(color) {
    const chain = props.editor.chain().focus();

    if (color) {
        chain.setHighlight({ color }).run();
    } else {
        chain.unsetHighlight().run();
    }
}

/** Indenting works on list items: regular lists use `listItem`, checklists use `taskItem`. */
const listItemType = computed(() => (props.editor.isActive('taskItem') ? 'taskItem' : 'listItem'));

const run = (command, ...args) => props.editor.chain().focus()[command](...args).run();

const isInTable = computed(() => props.editor.isActive('table'));

const tableActions = [
    { label: 'Tambah baris di atas', icon: 'plus', command: 'addRowBefore' },
    { label: 'Tambah baris di bawah', icon: 'plus', command: 'addRowAfter' },
    { label: 'Tambah kolom di kiri', icon: 'plus', command: 'addColumnBefore' },
    { label: 'Tambah kolom di kanan', icon: 'plus', command: 'addColumnAfter' },
    { type: 'separator' },
    { label: 'Gabung / pisah sel', icon: 'table', command: 'mergeOrSplit' },
    { label: 'Baris judul', icon: 'bold', command: 'toggleHeaderRow' },
    { type: 'separator' },
    { label: 'Hapus baris', icon: 'minus', command: 'deleteRow' },
    { label: 'Hapus kolom', icon: 'minus', command: 'deleteColumn' },
    { label: 'Hapus tabel', icon: 'trash', command: 'deleteTable', variant: 'danger' },
];
</script>

<template>
    <ToolbarRoot
        aria-label="Format dokumen"
        class="flex flex-wrap items-center gap-1 border-b border-line bg-surface px-2 py-1.5"
    >
        <EditorToolbarButton icon="undo" label="Urungkan (⌘Z)" :disabled="!editor.can().undo()" @click="run('undo')" />
        <EditorToolbarButton icon="redo" label="Ulangi (⌘⇧Z)" :disabled="!editor.can().redo()" @click="run('redo')" />

        <ToolbarSeparator />

        <Select v-model="blockType" :options="blockTypeOptions" size="sm" class="w-34" aria-label="Gaya paragraf" />
        <Select v-model="fontFamily" :options="fontFamilyOptions" size="sm" class="w-40" placeholder="Font" aria-label="Jenis huruf" />
        <Select v-model="fontSize" :options="fontSizeOptions" size="sm" class="w-28" placeholder="Ukuran" aria-label="Ukuran huruf" />

        <ToolbarSeparator />

        <EditorToolbarButton icon="bold" label="Tebal (⌘B)" toggle :active="editor.isActive('bold')" @click="run('toggleBold')" />
        <EditorToolbarButton icon="italic" label="Miring (⌘I)" toggle :active="editor.isActive('italic')" @click="run('toggleItalic')" />
        <EditorToolbarButton icon="underline" label="Garis bawah (⌘U)" toggle :active="editor.isActive('underline')" @click="run('toggleUnderline')" />
        <EditorToolbarButton icon="strikethrough" label="Coret (⌘⇧S)" toggle :active="editor.isActive('strike')" @click="run('toggleStrike')" />
        <EditorToolbarButton icon="superscript" label="Superskrip (⌘.)" toggle :active="editor.isActive('superscript')" @click="run('toggleSuperscript')" />
        <EditorToolbarButton icon="subscript" label="Subskrip (⌘,)" toggle :active="editor.isActive('subscript')" @click="run('toggleSubscript')" />

        <EditorColorPicker
            icon="baseline"
            label="Warna teks"
            reset-label="Otomatis"
            fallback-color="#000000"
            :colors="textColors"
            :color="textColor"
            @select="setTextColor"
        />
        <EditorColorPicker
            icon="highlighter"
            label="Warna sorotan"
            reset-label="Tanpa sorotan"
            :colors="highlightColors"
            :color="highlightColor"
            @select="setHighlightColor"
        />

        <ToolbarSeparator />

        <ToolbarToggleGroup v-model="alignment" :options="alignmentOptions" icon-only disallow-empty />

        <ToolbarSeparator />

        <EditorToolbarButton icon="list" label="Daftar berpoin" toggle :active="editor.isActive('bulletList')" @click="run('toggleBulletList')" />
        <EditorToolbarButton icon="list-ordered" label="Daftar bernomor" toggle :active="editor.isActive('orderedList')" @click="run('toggleOrderedList')" />
        <EditorToolbarButton icon="list-checks" label="Daftar centang" toggle :active="editor.isActive('taskList')" @click="run('toggleTaskList')" />
        <EditorToolbarButton
            icon="indent-decrease"
            label="Kurangi indentasi (⇧Tab)"
            :disabled="!editor.can().liftListItem(listItemType)"
            @click="run('liftListItem', listItemType)"
        />
        <EditorToolbarButton
            icon="indent-increase"
            label="Tambah indentasi (Tab)"
            :disabled="!editor.can().sinkListItem(listItemType)"
            @click="run('sinkListItem', listItemType)"
        />

        <ToolbarSeparator />

        <EditorLinkPopover :editor="editor" />
        <EditorImagePopover :editor="editor" />

        <DropdownMenu align="start" width="w-60">
            <template #trigger>
                <EditorToolbarButton icon="table" label="Tabel" :active="isInTable" />
            </template>

            <DropdownMenuItem icon="table" :disabled="isInTable" @select="run('insertTable', { rows: 3, cols: 3, withHeaderRow: true })">
                Sisipkan tabel 3 × 3
            </DropdownMenuItem>

            <template v-for="(action, index) in tableActions" :key="index">
                <DropdownMenuSeparator v-if="action.type === 'separator'" />
                <DropdownMenuItem
                    v-else
                    :icon="action.icon"
                    :variant="action.variant ?? 'default'"
                    :disabled="!isInTable"
                    @select="run(action.command)"
                >
                    {{ action.label }}
                </DropdownMenuItem>
            </template>
        </DropdownMenu>

        <EditorToolbarButton icon="quote" label="Kutipan" toggle :active="editor.isActive('blockquote')" @click="run('toggleBlockquote')" />
        <EditorToolbarButton icon="code" label="Blok kode" toggle :active="editor.isActive('codeBlock')" @click="run('toggleCodeBlock')" />
        <EditorToolbarButton icon="minus" label="Garis pemisah" @click="run('setHorizontalRule')" />

        <ToolbarSeparator />

        <EditorToolbarButton icon="remove-formatting" label="Hapus format" @click="editor.chain().focus().unsetAllMarks().clearNodes().run()" />
    </ToolbarRoot>
</template>
