<script setup>
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Avatar from '@/components/ui/Avatar.vue';
import Button from '@/components/ui/Button.vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

/* Example 1: row actions */
const lastAction = ref(null);

/* Example 3: data-driven items with checkbox / radio / sub-menu */
const columnLabels = { recipient: 'Penerima', date: 'Tanggal', due: 'Tenggat', status: 'Status' };
const visibleColumns = ref({ recipient: true, date: true, due: false, status: true });
const sortBy = ref('newest');
const exportFormat = ref(null);

const sortOptions = [
    { value: 'newest', label: 'Terbaru dulu' },
    { value: 'oldest', label: 'Terlama dulu' },
    { value: 'due', label: 'Tenggat terdekat' },
];

const viewItems = computed(() => [
    { type: 'label', label: 'Kolom' },
    ...Object.entries(columnLabels).map(([key, label]) => ({
        type: 'checkbox',
        label,
        checked: visibleColumns.value[key],
        onCheckedChange: (checked) => (visibleColumns.value[key] = checked),
        /* Keep the menu open so several columns can be toggled in a row. */
        onSelect: (event) => event.preventDefault(),
    })),
    { type: 'separator' },
    { type: 'label', label: 'Urutkan' },
    { type: 'radio', value: sortBy.value, options: sortOptions, onValueChange: (value) => (sortBy.value = value) },
    { type: 'separator' },
    {
        label: 'Ekspor',
        icon: 'download',
        items: [
            { label: 'PDF', icon: 'file-text', onSelect: () => (exportFormat.value = 'PDF') },
            { label: 'Excel', icon: 'layout-grid', onSelect: () => (exportFormat.value = 'Excel') },
            { label: 'CSV', icon: 'file', onSelect: () => (exportFormat.value = 'CSV') },
        ],
    },
]);

const shownColumns = computed(() =>
    Object.entries(columnLabels)
        .filter(([key]) => visibleColumns.value[key])
        .map(([, label]) => label),
);

const basicCode = `
<script setup>
import Button from '@/components/ui/Button.vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
<\/script>

<template>
    <DropdownMenu>
        <template #trigger>
            <Button variant="ghost" size="icon-sm" icon="ellipsis" aria-label="Aksi lain" />
        </template>

        <DropdownMenuItem icon="eye" @select="view">Lihat surat</DropdownMenuItem>
        <DropdownMenuItem icon="pencil" shortcut="⌘E" @select="edit">Edit</DropdownMenuItem>
        <DropdownMenuItem icon="copy" shortcut="⌘D" @select="duplicate">Duplikat</DropdownMenuItem>
        <DropdownMenuItem icon="lock" disabled>Kunci surat</DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuItem icon="trash" variant="danger" @select="destroy">Hapus</DropdownMenuItem>
    </DropdownMenu>
</template>
`;

const accountCode = `
<template>
    <DropdownMenu side="bottom" align="start" width="w-60">
        <template #trigger>
            <button type="button">...avatar + nama...</button>
        </template>

        <DropdownMenuLabel>Akun saya</DropdownMenuLabel>
        <DropdownMenuItem icon="user">Profil</DropdownMenuItem>
        <DropdownMenuItem icon="settings">Pengaturan</DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuItem :href="route('logout')" method="post" icon="log-out" variant="danger">Logout</DropdownMenuItem>
    </DropdownMenu>
</template>
`;

const itemsCode = `
<script setup>
import { computed, ref } from 'vue';

const visibleColumns = ref({ recipient: true, date: true, due: false });
const sortBy = ref('newest');

const viewItems = computed(() => [
    { type: 'label', label: 'Kolom' },
    {
        type: 'checkbox',
        label: 'Penerima',
        checked: visibleColumns.value.recipient,
        onCheckedChange: (checked) => (visibleColumns.value.recipient = checked),
        onSelect: (event) => event.preventDefault(), // keep the menu open
    },
    { type: 'separator' },
    { type: 'label', label: 'Urutkan' },
    {
        type: 'radio',
        value: sortBy.value,
        options: [{ value: 'newest', label: 'Terbaru dulu' }, { value: 'oldest', label: 'Terlama dulu' }],
        onValueChange: (value) => (sortBy.value = value),
    },
    { type: 'separator' },
    { label: 'Ekspor', icon: 'download', items: [{ label: 'PDF', icon: 'file-text', onSelect: exportPdf }] },
]);
<\/script>

<template>
    <DropdownMenu :items="viewItems" align="start">
        <template #trigger>
            <Button variant="secondary" icon="settings" icon-trailing="chevron-down">Tampilan</Button>
        </template>
    </DropdownMenu>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Aksi per baris seperti di dashboard: ikon, pintasan, item nonaktif, dan item danger."
            :code="basicCode"
        >
            <div class="flex flex-col items-center gap-4">
                <div class="flex w-full max-w-md items-center gap-3 rounded-xl border border-line px-4 py-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">Undangan rapat anggota tahunan</p>
                        <p class="truncate text-xs text-muted">014/SK/XI/2026 · PT Sinar Mandiri</p>
                    </div>

                    <DropdownMenu>
                        <template #trigger>
                            <Button variant="ghost" size="icon-sm" icon="ellipsis" aria-label="Aksi lain untuk 014/SK/XI/2026" />
                        </template>

                        <DropdownMenuItem icon="eye" @select="lastAction = 'Lihat surat'">Lihat surat</DropdownMenuItem>
                        <DropdownMenuItem icon="pencil" shortcut="⌘E" @select="lastAction = 'Edit'">Edit</DropdownMenuItem>
                        <DropdownMenuItem icon="copy" shortcut="⌘D" @select="lastAction = 'Duplikat'">Duplikat</DropdownMenuItem>
                        <DropdownMenuItem icon="lock" disabled>Kunci surat</DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem icon="trash" variant="danger" @select="lastAction = 'Hapus'">Hapus</DropdownMenuItem>
                    </DropdownMenu>
                </div>

                <p class="text-sm text-muted">Aksi terakhir: {{ lastAction ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Label, link, dan posisi"
            description="Menu akun seperti di sidebar: DropdownMenuLabel, item dengan href (Inertia Link, method post untuk logout), side/align/width."
            :code="accountCode"
        >
            <DropdownMenu side="bottom" align="start" width="w-60">
                <template #trigger>
                    <button
                        type="button"
                        class="flex w-64 cursor-pointer items-center gap-3 rounded-xl border border-line bg-surface p-2 text-left shadow-xs transition-colors hover:bg-canvas focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                    >
                        <Avatar name="Dewi Lestari" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium">Dewi Lestari</span>
                            <span class="block truncate text-xs text-muted">dewi@kantor.co.id</span>
                        </span>
                    </button>
                </template>

                <DropdownMenuLabel>Akun saya</DropdownMenuLabel>
                <DropdownMenuItem icon="user">Profil</DropdownMenuItem>
                <DropdownMenuItem icon="settings">Pengaturan</DropdownMenuItem>
                <DropdownMenuItem icon="layout-grid" :href="route('dashboard')">Dashboard</DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem icon="log-out" variant="danger" @select="lastAction = 'Logout (contoh)'">Logout</DropdownMenuItem>
            </DropdownMenu>
        </DocExample>

        <DocExample
            title="Items sebagai data"
            description="Prop items mendukung checkbox, radio, dan sub-menu. Di sini onSelect memanggil preventDefault agar menu tetap terbuka saat mencentang kolom."
            :code="itemsCode"
        >
            <div class="flex flex-col items-center gap-4 text-center">
                <DropdownMenu :items="viewItems" align="start">
                    <template #trigger>
                        <Button variant="secondary" icon="settings" icon-trailing="chevron-down">Tampilan</Button>
                    </template>
                </DropdownMenu>

                <div class="flex flex-col gap-1 text-sm text-muted">
                    <p>Kolom: {{ shownColumns.join(', ') || '—' }}</p>
                    <p>Urutan: {{ sortOptions.find((option) => option.value === sortBy)?.label }}</p>
                    <p>Ekspor terakhir: {{ exportFormat ?? '—' }}</p>
                </div>
            </div>
        </DocExample>

        <DocProps
            title="DropdownMenu"
            :rows="[
                { name: 'align', type: `'start' | 'center' | 'end'`, default: `'end'`, description: 'Perataan menu terhadap trigger.' },
                { name: 'side', type: `'top' | 'right' | 'bottom' | 'left'`, default: `'bottom'`, description: 'Sisi trigger tempat menu muncul.' },
                { name: 'width', type: 'string', default: `'w-56'`, description: 'Class lebar menu.' },
                { name: 'items', type: 'array', default: 'null', description: 'Isi menu sebagai data (dirender sebelum slot default); lihat tabel items[].' },
                { name: '#trigger', type: 'slot', description: 'Satu elemen pembuka, biasanya Button.' },
                { name: '#default', type: 'slot', description: 'DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, atau konten bebas.' },
            ]"
        />

        <DocProps
            title="DropdownMenuItem"
            :rows="[
                { name: 'icon', type: 'string', description: 'Nama ikon di kiri.' },
                { name: 'variant', type: `'default' | 'danger'`, default: `'default'`, description: 'Danger untuk aksi merusak.' },
                { name: 'shortcut', type: 'string', description: 'Petunjuk pintasan di kanan (hanya tampilan).' },
                { name: 'href', type: 'string', description: 'Jadikan item Inertia Link.' },
                { name: 'method', type: 'string', default: `'get'`, description: 'Method HTTP untuk href, mis. post untuk logout.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan item.' },
                { name: '@select', type: 'event', description: 'Dipanggil saat item dipilih; event.preventDefault() menjaga menu tetap terbuka.' },
            ]"
        />

        <DocProps
            title="DropdownMenuLabel / DropdownMenuSeparator"
            :rows="[
                { name: '#default', type: 'slot', description: 'Label: judul grup kecil. Separator tidak punya prop maupun slot.' },
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
