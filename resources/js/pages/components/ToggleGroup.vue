<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import ToggleGroup from '@/components/ui/ToggleGroup.vue';

const alignmentOptions = [
    { value: 'left', label: 'Rata kiri', icon: 'align-left' },
    { value: 'center', label: 'Rata tengah', icon: 'align-center' },
    { value: 'right', label: 'Rata kanan', icon: 'align-right' },
];
const alignment = ref('left');

const channelOptions = [
    { value: 'email', label: 'Email', icon: 'mail' },
    { value: 'app', label: 'Notifikasi aplikasi', icon: 'bell' },
    { value: 'courier', label: 'Kurir', icon: 'send' },
];
const channels = ref(['email', 'app']);

const viewOptions = [
    { value: 'list', label: 'Daftar', icon: 'list' },
    { value: 'grid', label: 'Kartu', icon: 'layout-grid' },
    { value: 'calendar', label: 'Kalender', icon: 'calendar', disabled: true },
];
const viewMode = ref('list');

const alignmentClasses = { left: 'text-left', center: 'text-center', right: 'text-right' };

const singleCode = `
<script setup>
import { ref } from 'vue';
import ToggleGroup from '@/components/ui/ToggleGroup.vue';

const alignment = ref('left');
<\/script>

<template>
    <ToggleGroup
        v-model="alignment"
        icon-only
        disallow-empty
        :options="[
            { value: 'left', label: 'Rata kiri', icon: 'align-left' },
            { value: 'center', label: 'Rata tengah', icon: 'align-center' },
            { value: 'right', label: 'Rata kanan', icon: 'align-right' },
        ]"
    />

    <p>Perataan: {{ alignment }}</p>
</template>
`;

const multipleCode = `
<script setup>
import { ref } from 'vue';
import ToggleGroup from '@/components/ui/ToggleGroup.vue';

const channels = ref(['email', 'app']);
<\/script>

<template>
    <ToggleGroup
        v-model="channels"
        type="multiple"
        label="Kirim pemberitahuan lewat"
        hint="Boleh lebih dari satu."
        :options="[
            { value: 'email', label: 'Email', icon: 'mail' },
            { value: 'app', label: 'Notifikasi aplikasi', icon: 'bell' },
            { value: 'courier', label: 'Kurir', icon: 'send' },
        ]"
    />

    <p>Dipilih: {{ channels.join(', ') }}</p>
</template>
`;

const ghostCode = `
<template>
    <ToggleGroup
        v-model="viewMode"
        variant="ghost"
        size="sm"
        disallow-empty
        :options="[
            { value: 'list', label: 'Daftar', icon: 'list' },
            { value: 'grid', label: 'Kartu', icon: 'layout-grid' },
            { value: 'calendar', label: 'Kalender', icon: 'calendar', disabled: true },
        ]"
    />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Tunggal, ikon saja"
            description="type single (default). disallow-empty mencegah semua tombol mati saat yang aktif diklik lagi; label jadi aria-label dan tooltip."
            :code="singleCode"
        >
            <div class="flex w-full max-w-md flex-col items-center gap-4">
                <ToggleGroup v-model="alignment" :options="alignmentOptions" icon-only disallow-empty />

                <p :class="['w-full rounded-xl border border-line bg-canvas p-4 text-sm', alignmentClasses[alignment]]">
                    Dengan hormat, bersama surat ini kami sampaikan undangan rapat koordinasi.
                </p>

                <p class="text-sm text-muted">Perataan: {{ alignment }}</p>
            </div>
        </DocExample>

        <DocExample title="Banyak pilihan" description="type multiple: v-model berupa array dan setiap tombol bisa aktif bersamaan." :code="multipleCode">
            <div class="flex flex-col gap-4">
                <ToggleGroup
                    v-model="channels"
                    type="multiple"
                    :options="channelOptions"
                    label="Kirim pemberitahuan lewat"
                    hint="Boleh lebih dari satu."
                />

                <p class="text-sm text-muted">Dipilih: {{ channels.join(', ') || '—' }}</p>
            </div>
        </DocExample>

        <DocExample title="Ghost & ukuran kecil" description="Tanpa kotak pembungkus, cocok di header tabel; opsi bisa dinonaktifkan." :code="ghostCode">
            <div class="flex flex-col items-center gap-4">
                <ToggleGroup v-model="viewMode" :options="viewOptions" variant="ghost" size="sm" disallow-empty />
                <p class="text-sm text-muted">Tampilan: {{ viewMode }}</p>
            </div>
        </DocExample>

        <DocProps
            title="ToggleGroup"
            :rows="[
                { name: 'v-model', type: 'string | string[]', description: 'Nilai aktif (array untuk type multiple).' },
                { name: 'options', type: 'Array<{ value, label, icon?, disabled? }>', description: 'Daftar tombol (wajib); nilai polos juga diterima.' },
                { name: 'type', type: `'single' | 'multiple'`, default: `'single'`, description: 'Satu atau banyak tombol aktif.' },
                { name: 'icon-only', type: 'boolean', default: 'false', description: 'Sembunyikan label; label dipakai sebagai aria-label dan tooltip.' },
                { name: 'disallow-empty', type: 'boolean', default: 'false', description: 'Abaikan klik yang membuat tidak ada tombol aktif.' },
                { name: 'variant', type: `'outline' | 'ghost'`, default: `'outline'`, description: 'Di dalam kotak berborder, atau tombol lepas.' },
                { name: 'size', type: `'sm' | 'md'`, default: `'md'`, description: 'Tinggi tombol 28 atau 32px.' },
                { name: 'orientation', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Arah susunan dan tombol panah.' },
                { name: 'label', type: 'string', description: 'Judul grup (aria-labelledby).' },
                { name: 'name', type: 'string', description: 'Nama field untuk form HTML biasa.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua tombol.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah grup.' },
            ]"
        />
    </DocPage>
</template>
