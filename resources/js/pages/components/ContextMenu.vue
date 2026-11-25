<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Badge from '@/components/ui/Badge.vue';
import ContextMenu from '@/components/ui/ContextMenu.vue';
import Icon from '@/components/ui/Icon.vue';

/* Example 1: simple area */
const lastAction = ref(null);

const basicItems = [
    { label: 'Surat baru', icon: 'plus', shortcut: '⌘N', onSelect: () => (lastAction.value = 'Surat baru') },
    { label: 'Unggah lampiran', icon: 'upload', onSelect: () => (lastAction.value = 'Unggah lampiran') },
    { type: 'separator' },
    { label: 'Tempel', icon: 'copy', shortcut: '⌘V', disabled: true },
];

/* Example 2: per-row menu with checkbox, radio and sub-menu */
const letters = ref([
    { number: '012/SK/XI/2026', subject: 'Pemberitahuan perubahan alamat', recipient: 'PT Sinar Mandiri', folder: 'Masuk', important: true, priority: 'normal' },
    { number: '011/SK/XI/2026', subject: 'Permintaan tanda tangan kontrak', recipient: 'Yayasan Pelita Harapan', folder: 'Masuk', important: false, priority: 'high' },
    { number: '010/SK/X/2026', subject: 'Konfirmasi kehadiran seminar', recipient: 'CV Arunika Desain', folder: 'Masuk', important: false, priority: 'low' },
]);

const rowLog = ref(null);

const priorityLabels = { high: 'Tinggi', normal: 'Normal', low: 'Rendah' };

function rowItems(letter) {
    const log = (message) => (rowLog.value = `${message} — ${letter.number}`);

    return [
        { type: 'label', label: letter.number },
        { label: 'Buka surat', icon: 'eye', shortcut: '↵', onSelect: () => log('Dibuka') },
        { label: 'Teruskan', icon: 'forward', onSelect: () => log('Diteruskan') },
        {
            label: 'Pindahkan ke',
            icon: 'folder',
            items: ['Masuk', 'Penting', 'Arsip'].map((folder) => ({
                label: folder,
                icon: folder === 'Arsip' ? 'archive' : 'folder',
                disabled: letter.folder === folder,
                onSelect: () => {
                    letter.folder = folder;
                    log(`Dipindahkan ke ${folder}`);
                },
            })),
        },
        { type: 'separator' },
        { type: 'checkbox', label: 'Tandai penting', checked: letter.important, onCheckedChange: (checked) => (letter.important = checked) },
        { type: 'label', label: 'Prioritas' },
        {
            type: 'radio',
            value: letter.priority,
            options: Object.entries(priorityLabels).map(([value, label]) => ({ value, label })),
            onValueChange: (value) => (letter.priority = value),
        },
        { type: 'separator' },
        { label: 'Hapus', icon: 'trash', variant: 'danger', shortcut: '⌫', onSelect: () => log('Dihapus') },
    ];
}

const basicCode = `
<script setup>
import ContextMenu from '@/components/ui/ContextMenu.vue';

const items = [
    { label: 'Surat baru', icon: 'plus', shortcut: '⌘N', onSelect: createLetter },
    { label: 'Unggah lampiran', icon: 'upload', onSelect: upload },
    { type: 'separator' },
    { label: 'Tempel', icon: 'copy', shortcut: '⌘V', disabled: true },
];
<\/script>

<template>
    <ContextMenu :items="items">
        <div class="rounded-2xl border-2 border-dashed border-line p-10 data-[state=open]:border-brand">
            Klik kanan di sini
        </div>
    </ContextMenu>
</template>
`;

const rowCode = `
<script setup>
import ContextMenu from '@/components/ui/ContextMenu.vue';

/** Items are rebuilt per row, so checked / value always reflect the current state. */
function rowItems(letter) {
    return [
        { type: 'label', label: letter.number },
        { label: 'Buka surat', icon: 'eye', onSelect: () => open(letter) },
        {
            label: 'Pindahkan ke',
            icon: 'folder',
            items: [
                { label: 'Penting', onSelect: () => (letter.folder = 'Penting') },
                { label: 'Arsip', icon: 'archive', onSelect: () => (letter.folder = 'Arsip') },
            ],
        },
        { type: 'separator' },
        { type: 'checkbox', label: 'Tandai penting', checked: letter.important, onCheckedChange: (checked) => (letter.important = checked) },
        {
            type: 'radio',
            value: letter.priority,
            options: [{ value: 'high', label: 'Tinggi' }, { value: 'normal', label: 'Normal' }],
            onValueChange: (value) => (letter.priority = value),
        },
        { type: 'separator' },
        { label: 'Hapus', icon: 'trash', variant: 'danger', onSelect: () => destroy(letter) },
    ];
}
<\/script>

<template>
    <ContextMenu v-for="letter in letters" :key="letter.number" :items="rowItems(letter)">
        <li class="data-[state=open]:bg-ink/5">{{ letter.subject }}</li>
    </ContextMenu>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik kanan (atau tekan lama di layar sentuh) di dalam area. Trigger mendapat data-state=&quot;open&quot; selama menu terbuka."
            :code="basicCode"
        >
            <div class="flex w-full max-w-md flex-col items-center gap-3">
                <ContextMenu :items="basicItems">
                    <div
                        class="flex w-full flex-col items-center gap-2 rounded-2xl border-2 border-dashed border-line px-6 py-10 text-center text-sm text-muted transition-colors select-none data-[state=open]:border-brand data-[state=open]:bg-brand-soft/40"
                    >
                        <Icon name="inbox" class="size-6" />
                        Klik kanan di sini
                    </div>
                </ContextMenu>

                <p class="text-sm text-muted">Aksi terakhir: {{ lastAction ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Menu per baris"
            description="Label, sub-menu, item checkbox dan radio, serta item danger. Item dibuat ulang per baris sehingga selalu sesuai state."
            :code="rowCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <ul class="divide-y divide-line overflow-hidden rounded-xl border border-line">
                    <ContextMenu v-for="letter in letters" :key="letter.number" :items="rowItems(letter)">
                        <li class="flex cursor-context-menu items-center gap-3 px-4 py-3 transition-colors select-none data-[state=open]:bg-ink/5">
                            <Icon
                                name="star"
                                :class="['size-4', letter.important ? 'fill-current text-warning' : 'text-line']"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ letter.subject }}</p>
                                <p class="truncate text-xs text-muted">{{ letter.number }} · {{ letter.recipient }}</p>
                            </div>
                            <Badge :variant="letter.priority === 'high' ? 'danger' : 'neutral'">{{ priorityLabels[letter.priority] }}</Badge>
                            <Badge variant="info">{{ letter.folder }}</Badge>
                        </li>
                    </ContextMenu>
                </ul>

                <p class="text-sm text-muted">{{ rowLog ?? 'Klik kanan salah satu baris.' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="ContextMenu"
            :rows="[
                { name: 'items', type: 'array', description: 'Isi menu sebagai data (wajib); bentuk item ada di tabel items[].' },
                { name: 'width', type: 'string', default: `'w-56'`, description: 'Class lebar menu.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Pakai menu klik kanan bawaan browser.' },
                { name: 'v-model:open', type: 'boolean', description: 'Status terbuka menu (opsional).' },
                { name: '#default', type: 'slot', description: 'Satu elemen area yang merespons klik kanan.' },
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
                { name: 'href / method', type: 'string', description: 'Jadikan item Inertia Link (method post untuk aksi seperti logout).' },
                { name: 'items', type: 'array', description: 'Item anak; menjadikan item ini sub-menu.' },
                { name: `type: 'checkbox'`, type: 'checked, onCheckedChange', description: 'Item centang dengan nilai boolean.' },
                { name: `type: 'radio'`, type: 'value, options, onValueChange', description: 'Grup pilihan tunggal; options berisi { value, label, disabled? }.' },
                { name: `type: 'label' | 'separator'`, type: '—', description: 'Judul grup kecil atau garis pemisah.' },
            ]"
        />
    </DocPage>
</template>
