<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Menubar from '@/components/ui/Menubar.vue';

const lastCommand = ref(null);
const openMenu = ref('');

const view = ref({ ruler: true, letterhead: true, zoom: '100' });

const run = (command) => () => (lastCommand.value = command);

const menus = computed(() => [
    {
        value: 'file',
        label: 'Berkas',
        items: [
            { label: 'Surat baru', icon: 'plus', shortcut: '⌘N', onSelect: run('Surat baru') },
            { label: 'Buka…', icon: 'folder-open', shortcut: '⌘O', onSelect: run('Buka') },
            { label: 'Simpan', icon: 'check', shortcut: '⌘S', onSelect: run('Simpan') },
            { type: 'separator' },
            {
                label: 'Ekspor sebagai',
                icon: 'download',
                items: [
                    { label: 'PDF', icon: 'file-text', onSelect: run('Ekspor PDF') },
                    { label: 'Word (.docx)', icon: 'file', onSelect: run('Ekspor Word') },
                ],
            },
            { label: 'Kirim ke penerima', icon: 'send', onSelect: run('Kirim') },
            { type: 'separator' },
            { label: 'Hapus draf', icon: 'trash', variant: 'danger', onSelect: run('Hapus draf') },
        ],
    },
    {
        value: 'edit',
        label: 'Edit',
        items: [
            { label: 'Urungkan', shortcut: '⌘Z', onSelect: run('Urungkan') },
            { label: 'Ulangi', shortcut: '⇧⌘Z', disabled: true },
            { type: 'separator' },
            { label: 'Salin', icon: 'copy', shortcut: '⌘C', onSelect: run('Salin') },
            { label: 'Sisipkan tautan', icon: 'link', shortcut: '⌘K', onSelect: run('Sisipkan tautan') },
            { label: 'Sisipkan gambar', icon: 'image', onSelect: run('Sisipkan gambar') },
        ],
    },
    {
        value: 'view',
        label: 'Tampilan',
        items: [
            { type: 'checkbox', label: 'Penggaris', checked: view.value.ruler, onCheckedChange: (checked) => (view.value.ruler = checked) },
            {
                type: 'checkbox',
                label: 'Kop surat',
                checked: view.value.letterhead,
                onCheckedChange: (checked) => (view.value.letterhead = checked),
            },
            { type: 'separator' },
            { type: 'label', label: 'Perbesar' },
            {
                type: 'radio',
                value: view.value.zoom,
                options: [
                    { value: '75', label: '75%' },
                    { value: '100', label: '100%' },
                    { value: '125', label: '125%' },
                ],
                onValueChange: (value) => (view.value.zoom = value),
            },
        ],
    },
    {
        value: 'help',
        label: 'Bantuan',
        items: [
            { label: 'Panduan penulisan surat', icon: 'book-open', onSelect: run('Buka panduan') },
            { label: 'Pintasan keyboard', icon: 'info', shortcut: '⌘/', onSelect: run('Pintasan keyboard') },
        ],
    },
]);

const basicCode = `
<script setup>
import { computed, ref } from 'vue';
import Menubar from '@/components/ui/Menubar.vue';

const openMenu = ref('');
const view = ref({ ruler: true, zoom: '100' });

/* computed, so checked / value follow the state */
const menus = computed(() => [
    {
        value: 'file',
        label: 'Berkas',
        items: [
            { label: 'Surat baru', icon: 'plus', shortcut: '⌘N', onSelect: createLetter },
            { label: 'Ekspor sebagai', icon: 'download', items: [{ label: 'PDF', icon: 'file-text', onSelect: exportPdf }] },
            { type: 'separator' },
            { label: 'Hapus draf', icon: 'trash', variant: 'danger', onSelect: discard },
        ],
    },
    {
        value: 'view',
        label: 'Tampilan',
        items: [
            { type: 'checkbox', label: 'Penggaris', checked: view.value.ruler, onCheckedChange: (checked) => (view.value.ruler = checked) },
            { type: 'separator' },
            {
                type: 'radio',
                value: view.value.zoom,
                options: [{ value: '75', label: '75%' }, { value: '100', label: '100%' }],
                onValueChange: (value) => (view.value.zoom = value),
            },
        ],
    },
]);
<\/script>

<template>
    <Menubar v-model="openMenu" :menus="menus" />

    <p>Menu terbuka: {{ openMenu || '—' }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Editor surat"
            description="Klik sebuah menu, lalu geser kursor atau pakai panah kiri/kanan untuk pindah menu. Checkbox dan radio di menu Tampilan terhubung ke state."
            :code="basicCode"
            preview-class="flex min-h-80 flex-col items-center justify-start gap-6 p-6 sm:p-10"
        >
            <Menubar v-model="openMenu" :menus="menus" />

            <div
                :class="[
                    'w-full max-w-md rounded-xl border border-line bg-canvas p-5 text-sm transition-transform',
                    { '75': 'scale-95', '100': 'scale-100', '125': 'scale-105' }[view.zoom],
                ]"
            >
                <div v-if="view.ruler" class="mb-3 h-2 rounded-full bg-[repeating-linear-gradient(90deg,var(--color-line)_0_1px,transparent_1px_12px)]" />
                <p v-if="view.letterhead" class="mb-3 border-b border-line pb-2 text-xs font-semibold tracking-wide text-muted uppercase">
                    Koperasi Maju Bersama
                </p>
                <p class="font-medium">Perihal: Pengajuan anggaran kegiatan</p>
                <p class="mt-1 text-muted">Dengan hormat, bersama surat ini kami mengajukan…</p>
            </div>

            <p class="text-sm text-muted">
                Menu terbuka: {{ openMenu || '—' }} · Perintah terakhir: {{ lastCommand ?? '—' }} · Zoom {{ view.zoom }}%
            </p>
        </DocExample>

        <DocProps
            title="Menubar"
            :rows="[
                { name: 'menus', type: 'array', description: 'Daftar menu { label, value?, disabled?, items } (wajib).' },
                { name: 'v-model', type: 'string', description: `Value menu yang terbuka ('' bila semua tertutup).` },
                { name: 'width', type: 'string', default: `'w-56'`, description: 'Class lebar setiap menu.' },
                { name: 'loop', type: 'boolean', default: 'true', description: 'Tombol panah berputar dari menu terakhir ke pertama.' },
            ]"
        />

        <DocProps
            title="items[]"
            :rows="[
                { name: 'label', type: 'string', description: 'Teks item, label grup, atau sub-menu.' },
                { name: 'icon', type: 'string', description: 'Nama ikon di kiri.' },
                { name: 'shortcut', type: 'string', description: 'Petunjuk pintasan di kanan (hanya tampilan).' },
                { name: 'variant', type: `'default' | 'danger'`, default: `'default'`, description: 'Danger untuk aksi merusak seperti hapus.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan item.' },
                { name: 'onSelect', type: '(event) => void', description: 'Dipanggil saat dipilih; event.preventDefault() menjaga menu tetap terbuka.' },
                { name: 'href / method', type: 'string', description: 'Jadikan item Inertia Link.' },
                { name: 'items', type: 'array', description: 'Item anak; menjadikan item ini sub-menu.' },
                { name: `type: 'checkbox'`, type: 'checked, onCheckedChange', description: 'Item centang dengan nilai boolean.' },
                { name: `type: 'radio'`, type: 'value, options, onValueChange', description: 'Grup pilihan tunggal; options berisi { value, label, disabled? }.' },
                { name: `type: 'label' | 'separator'`, type: '—', description: 'Judul grup kecil atau garis pemisah.' },
            ]"
        />
    </DocPage>
</template>
