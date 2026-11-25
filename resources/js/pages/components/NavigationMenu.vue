<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import NavigationMenu from '@/components/ui/NavigationMenu.vue';

const page = usePage();

/** Same approach as the sidebar: reading `page.url` re-evaluates after every Inertia visit. */
const isCurrentRoute = (name) => Boolean(page.url) && route().current(name);

const openItem = ref('');

const items = computed(() => [
    { label: 'Dashboard', icon: 'layout-grid', href: route('dashboard'), active: isCurrentRoute('dashboard') },
    {
        label: 'Surat keluar',
        value: 'letters',
        children: [
            { label: 'Draf', icon: 'pen-line', href: route('dashboard', { status: 'draft' }), description: 'Surat yang belum dikirim.' },
            { label: 'Menunggu', icon: 'clock', href: route('dashboard', { status: 'awaiting' }), description: 'Menunggu balasan penerima.' },
            { label: 'Terlambat', icon: 'triangle-alert', href: route('dashboard', { status: 'overdue' }), description: 'Melewati tenggat balasan.' },
            { label: 'Terkirim', icon: 'send', href: route('dashboard', { status: 'sent' }), description: 'Surat yang sudah selesai.' },
        ],
    },
    {
        label: 'Komponen',
        value: 'components',
        children: [
            { label: 'Dialog', icon: 'component', href: route('components.dialog'), description: 'Modal untuk form atau informasi.', active: isCurrentRoute('components.dialog') },
            { label: 'Drawer', icon: 'panel-left', href: route('components.drawer'), description: 'Panel dari tepi layar, bisa digeser.', active: isCurrentRoute('components.drawer') },
            { label: 'Dropdown Menu', icon: 'list', href: route('components.dropdown-menu'), description: 'Menu aksi dari sebuah tombol.', active: isCurrentRoute('components.dropdown-menu') },
            {
                label: 'Navigation Menu',
                icon: 'menu',
                href: route('components.navigation-menu'),
                description: 'Halaman ini.',
                active: isCurrentRoute('components.navigation-menu'),
            },
        ],
    },
    {
        label: 'Bantuan',
        value: 'help',
        columns: 1,
        children: [
            { label: 'Semua komponen', icon: 'book-open', href: route('components.index'), description: 'Daftar komponen reka-ui yang didokumentasikan.' },
        ],
    },
]);

const basicCode = `
<script setup>
import { ref } from 'vue';
import { route } from 'ziggy-js';
import NavigationMenu from '@/components/ui/NavigationMenu.vue';

const openItem = ref('');

const items = [
    { label: 'Dashboard', icon: 'layout-grid', href: route('dashboard'), active: true },
    {
        label: 'Surat keluar',
        value: 'letters',
        children: [
            { label: 'Draf', icon: 'pen-line', href: route('dashboard', { status: 'draft' }), description: 'Surat yang belum dikirim.' },
            { label: 'Menunggu', icon: 'clock', href: route('dashboard', { status: 'awaiting' }), description: 'Menunggu balasan penerima.' },
            // more than three links: two columns
        ],
    },
    {
        label: 'Bantuan',
        value: 'help',
        columns: 1,
        children: [{ label: 'Semua komponen', icon: 'book-open', href: route('components.index') }],
    },
];
<\/script>

<template>
    <NavigationMenu v-model="openItem" :items="items" />

    <p>Panel terbuka: {{ openItem || '—' }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Navigasi situs"
            description="Arahkan kursor atau klik sebuah menu; panel menyesuaikan ukuran saat pindah menu. Link berupa Inertia Link sungguhan dan item aktif diberi warna brand."
            :code="basicCode"
            preview-class="flex min-h-96 flex-col items-center justify-start gap-6 p-6 sm:p-10"
        >
            <NavigationMenu v-model="openItem" :items="items" />

            <p class="mt-auto text-sm text-muted">Panel terbuka: {{ openItem || '—' }}</p>
        </DocExample>

        <DocProps
            title="NavigationMenu"
            :rows="[
                { name: 'items', type: 'array', description: 'Item tingkat atas (wajib): link langsung (href) atau menu dengan children.' },
                { name: 'v-model', type: 'string', description: `Value item yang panelnya terbuka ('' bila tertutup).` },
                { name: 'align', type: `'start' | 'center' | 'end'`, default: `'start'`, description: 'Posisi panel terhadap trigger-nya.' },
            ]"
        />

        <DocProps
            title="items[]"
            :rows="[
                { name: 'label', type: 'string', description: 'Teks item.' },
                { name: 'value', type: 'string', default: `'item-{index}'`, description: 'Identitas item untuk v-model (tanpa spasi).' },
                { name: 'icon', type: 'string', description: 'Ikon di depan label.' },
                { name: 'href', type: 'string', description: 'Item tanpa children menjadi link langsung.' },
                { name: 'active', type: 'boolean', default: 'false', description: 'Tandai link halaman saat ini (aria-current=page).' },
                { name: 'children', type: 'array', description: 'Link di dalam panel: { label, href, icon?, description?, active? }.' },
                { name: 'columns', type: '1 | 2', description: 'Jumlah kolom panel; default 2 bila lebih dari tiga link.' },
            ]"
        />
    </DocPage>
</template>
