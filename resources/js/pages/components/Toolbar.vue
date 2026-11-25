<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Toolbar from '@/components/ui/Toolbar.vue';
import ToolbarButton from '@/components/ui/ToolbarButton.vue';
import ToolbarSeparator from '@/components/ui/ToolbarSeparator.vue';
import ToolbarToggleGroup from '@/components/ui/ToolbarToggleGroup.vue';

const formatOptions = [
    { value: 'bold', label: 'Tebal', icon: 'bold' },
    { value: 'italic', label: 'Miring', icon: 'italic' },
    { value: 'underline', label: 'Garis bawah', icon: 'underline' },
];

const alignmentOptions = [
    { value: 'left', label: 'Rata kiri', icon: 'align-left' },
    { value: 'center', label: 'Rata tengah', icon: 'align-center' },
    { value: 'right', label: 'Rata kanan', icon: 'align-right' },
];

const formats = ref(['bold']);
const alignment = ref('left');

const previewClasses = computed(() => [
    formats.value.includes('bold') && 'font-semibold',
    formats.value.includes('italic') && 'italic',
    formats.value.includes('underline') && 'underline',
    { left: 'text-left', center: 'text-center', right: 'text-right' }[alignment.value],
]);

const lastAction = ref(null);

const viewOptions = [
    { value: 'list', label: 'Daftar', icon: 'list' },
    { value: 'grid', label: 'Kartu', icon: 'layout-grid' },
];
const viewMode = ref('list');

const editorCode = `
<script setup>
import { ref } from 'vue';
import Toolbar from '@/components/ui/Toolbar.vue';
import ToolbarButton from '@/components/ui/ToolbarButton.vue';
import ToolbarSeparator from '@/components/ui/ToolbarSeparator.vue';
import ToolbarToggleGroup from '@/components/ui/ToolbarToggleGroup.vue';

const formats = ref(['bold']);
const alignment = ref('left');
<\/script>

<template>
    <Toolbar aria-label="Format isi surat">
        <ToolbarToggleGroup
            v-model="formats"
            type="multiple"
            icon-only
            :options="[
                { value: 'bold', label: 'Tebal', icon: 'bold' },
                { value: 'italic', label: 'Miring', icon: 'italic' },
                { value: 'underline', label: 'Garis bawah', icon: 'underline' },
            ]"
        />

        <ToolbarSeparator />

        <ToolbarToggleGroup
            v-model="alignment"
            icon-only
            disallow-empty
            :options="[
                { value: 'left', label: 'Rata kiri', icon: 'align-left' },
                { value: 'center', label: 'Rata tengah', icon: 'align-center' },
                { value: 'right', label: 'Rata kanan', icon: 'align-right' },
            ]"
        />

        <ToolbarSeparator />

        <ToolbarButton icon="link" aria-label="Sisipkan tautan" />
        <ToolbarButton icon="image" aria-label="Sisipkan gambar" />

        <ToolbarButton variant="primary" icon="send" class="ml-auto">Kirim</ToolbarButton>
    </Toolbar>

    <p>Format: {{ formats.join(', ') }} · Perataan: {{ alignment }}</p>
</template>
`;

const bulkCode = `
<script setup>
import { ref } from 'vue';

const lastAction = ref(null);
<\/script>

<template>
    <Toolbar aria-label="Aksi untuk surat terpilih">
        <span class="px-2 text-sm text-muted">3 surat dipilih</span>

        <ToolbarSeparator />

        <ToolbarButton icon="download" @click="lastAction = 'Unduh PDF'">Unduh</ToolbarButton>
        <ToolbarButton icon="forward" @click="lastAction = 'Teruskan'">Teruskan</ToolbarButton>
        <ToolbarButton icon="archive" @click="lastAction = 'Arsipkan'">Arsipkan</ToolbarButton>
        <ToolbarButton icon="trash" disabled>Hapus</ToolbarButton>
    </Toolbar>
</template>
`;

const verticalCode = `
<template>
    <Toolbar orientation="vertical" aria-label="Alat dokumen">
        <ToolbarToggleGroup v-model="viewMode" icon-only disallow-empty :options="viewOptions" />

        <ToolbarSeparator />

        <ToolbarButton icon="pen-line" aria-label="Edit" />
        <ToolbarButton icon="copy" aria-label="Duplikat" />
        <ToolbarButton icon="download" aria-label="Unduh" />
    </Toolbar>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Editor surat"
            description="Satu Tab stop untuk seluruh toolbar; panah kiri/kanan berpindah antar tombol. Gabungkan toggle group, separator dan tombol biasa."
            :code="editorCode"
        >
            <div class="flex w-full max-w-2xl flex-col gap-3">
                <Toolbar aria-label="Format isi surat">
                    <ToolbarToggleGroup v-model="formats" type="multiple" icon-only :options="formatOptions" />
                    <ToolbarSeparator />
                    <ToolbarToggleGroup v-model="alignment" icon-only disallow-empty :options="alignmentOptions" />
                    <ToolbarSeparator />
                    <ToolbarButton icon="link" aria-label="Sisipkan tautan" />
                    <ToolbarButton icon="image" aria-label="Sisipkan gambar" />
                    <ToolbarButton variant="primary" icon="send" class="ml-auto">Kirim</ToolbarButton>
                </Toolbar>

                <p :class="['rounded-xl border border-line bg-canvas p-4 text-sm leading-relaxed', previewClasses]">
                    Dengan hormat, bersama surat ini kami mengundang Bapak/Ibu untuk menghadiri rapat koordinasi anggaran
                    pada hari Senin, 30 November 2026, pukul 09.00 WIB di Ruang Rapat Utama.
                </p>

                <p class="text-sm text-muted">Format: {{ formats.join(', ') || '—' }} · Perataan: {{ alignment }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Aksi massal"
            description="ToolbarButton dengan teks memakai Button ukuran sm; teks biasa boleh diletakkan di dalam toolbar."
            :code="bulkCode"
        >
            <div class="flex w-full max-w-2xl flex-col gap-3">
                <Toolbar aria-label="Aksi untuk surat terpilih">
                    <span class="px-2 text-sm text-muted">3 surat dipilih</span>
                    <ToolbarSeparator />
                    <ToolbarButton icon="download" @click="lastAction = 'Unduh PDF'">Unduh</ToolbarButton>
                    <ToolbarButton icon="forward" @click="lastAction = 'Teruskan'">Teruskan</ToolbarButton>
                    <ToolbarButton icon="archive" @click="lastAction = 'Arsipkan'">Arsipkan</ToolbarButton>
                    <ToolbarButton icon="trash" disabled>Hapus</ToolbarButton>
                </Toolbar>

                <p class="text-sm text-muted">Aksi terakhir: {{ lastAction ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample title="Vertikal" description="orientation vertical: panah atas/bawah untuk berpindah, separator jadi garis horizontal." :code="verticalCode">
            <div class="flex items-start gap-4">
                <Toolbar orientation="vertical" aria-label="Alat dokumen">
                    <ToolbarToggleGroup v-model="viewMode" icon-only disallow-empty :options="viewOptions" />
                    <ToolbarSeparator />
                    <ToolbarButton icon="pen-line" aria-label="Edit" />
                    <ToolbarButton icon="copy" aria-label="Duplikat" />
                    <ToolbarButton icon="download" aria-label="Unduh" />
                </Toolbar>

                <p class="text-sm text-muted">Tampilan: {{ viewMode }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Toolbar"
            :rows="[
                { name: 'orientation', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Arah toolbar dan tombol panah yang dipakai.' },
                { name: 'loop', type: 'boolean', default: 'true', description: 'Panah dari tombol terakhir kembali ke yang pertama.' },
                { name: 'aria-label', type: 'string', description: 'Nama toolbar untuk pembaca layar (disarankan).' },
            ]"
        />

        <DocProps
            title="ToolbarButton"
            :rows="[
                { name: 'icon', type: 'string', description: 'Nama ikon. Tanpa teks slot, tombol menjadi kotak ikon saja (wajib aria-label).' },
                { name: 'variant', type: `'ghost' | 'primary' | 'secondary' | 'soft' | 'dark' | 'danger'`, default: `'ghost'`, description: 'Varian Button.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan tombol; dilewati saat navigasi panah.' },
            ]"
        />

        <DocProps
            title="ToolbarToggleGroup"
            :rows="[
                { name: 'v-model', type: 'string | string[]', description: 'Nilai aktif (array untuk type multiple).' },
                { name: 'options', type: 'Array<{ value, label, icon?, disabled? }>', description: 'Daftar tombol toggle (wajib).' },
                { name: 'type', type: `'single' | 'multiple'`, default: `'single'`, description: 'Satu atau banyak tombol aktif.' },
                { name: 'icon-only', type: 'boolean', default: 'false', description: 'Sembunyikan label; label jadi aria-label dan tooltip.' },
                { name: 'disallow-empty', type: 'boolean', default: 'false', description: 'Abaikan klik yang membuat tidak ada tombol aktif.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua tombol dalam grup.' },
            ]"
        />

        <DocProps
            title="ToolbarSeparator"
            :rows="[{ name: '—', type: '—', description: 'Tanpa prop. Garis vertikal di toolbar horizontal dan sebaliknya.' }]"
        />
    </DocPage>
</template>
