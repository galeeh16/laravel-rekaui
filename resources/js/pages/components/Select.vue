<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Select from '@/components/ui/Select.vue';

const status = ref('draft');

const statusOptions = [
    { value: 'draft', label: 'Draf' },
    { value: 'review', label: 'Menunggu paraf' },
    { value: 'signed', label: 'Sudah ditandatangani' },
    { value: 'sent', label: 'Terkirim' },
];

const unit = ref(undefined);

const unitOptions = [
    {
        label: 'Sekretariat',
        options: [
            { value: 'tu', label: 'Tata Usaha' },
            { value: 'kepegawaian', label: 'Kepegawaian' },
        ],
    },
    {
        label: 'Bidang',
        options: [
            { value: 'keuangan', label: 'Keuangan' },
            { value: 'perencanaan', label: 'Perencanaan' },
            { value: 'hukum', label: 'Hukum (sedang reorganisasi)', disabled: true },
        ],
    },
];

const perPage = ref(10);
const year = ref(undefined);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Select from '@/components/ui/Select.vue';

const status = ref('draft');
<\/script>

<template>
    <Select
        v-model="status"
        label="Status surat"
        :options="[
            { value: 'draft', label: 'Draf' },
            { value: 'review', label: 'Menunggu paraf' },
            { value: 'signed', label: 'Sudah ditandatangani' },
            { value: 'sent', label: 'Terkirim' },
        ]"
    />

    <p>Nilai: {{ status }}</p>
</template>
`;

const groupCode = `
<script setup>
import { ref } from 'vue';
import Select from '@/components/ui/Select.vue';

const unit = ref();

const unitOptions = [
    {
        label: 'Sekretariat',
        options: [
            { value: 'tu', label: 'Tata Usaha' },
            { value: 'kepegawaian', label: 'Kepegawaian' },
        ],
    },
    {
        label: 'Bidang',
        options: [
            { value: 'keuangan', label: 'Keuangan' },
            { value: 'perencanaan', label: 'Perencanaan' },
            { value: 'hukum', label: 'Hukum (sedang reorganisasi)', disabled: true },
        ],
    },
];
<\/script>

<template>
    <Select v-model="unit" label="Unit tujuan" placeholder="Pilih unit" :options="unitOptions" required />
</template>
`;

const sizeCode = `
<template>
    <span id="per-page-label">Baris per halaman</span>
    <Select v-model="perPage" size="sm" :options="[10, 25, 50]" class="w-20" aria-labelledby="per-page-label" />

    <Select
        v-model="form.year"
        label="Tahun arsip"
        :options="[2026, 2025, 2024]"
        :error="form.errors.year"
    />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Dasar" description="Opsi berupa { value, label }; navigasi keyboard dan typeahead sudah bawaan." :code="basicCode">
            <div class="flex w-full max-w-xs flex-col gap-3">
                <Select v-model="status" label="Status surat" :options="statusOptions" />

                <p class="text-sm text-muted">Nilai: {{ status }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Grup & opsi nonaktif"
            description="Kelompokkan opsi dengan { label, options }; opsi dengan disabled: true tidak bisa dipilih."
            :code="groupCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <Select v-model="unit" label="Unit tujuan" placeholder="Pilih unit" :options="unitOptions" required />

                <p class="text-sm text-muted">Nilai: {{ unit ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Ukuran kecil & error"
            description="size=&quot;sm&quot; untuk toolbar atau footer tabel; opsi boleh berupa angka. error menampilkan pesan validasi."
            :code="sizeCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-6">
                <div class="flex items-center gap-2 text-sm text-muted">
                    <span id="doc-per-page-label">Baris per halaman</span>
                    <Select v-model="perPage" size="sm" :options="[10, 25, 50]" class="w-20" aria-labelledby="doc-per-page-label" />
                </div>

                <Select
                    v-model="year"
                    label="Tahun arsip"
                    :options="[2026, 2025, 2024]"
                    :error="year ? null : 'Tahun arsip wajib dipilih.'"
                />
            </div>
        </DocExample>

        <DocProps
            title="Select"
            :rows="[
                { name: 'v-model', type: 'string | number', description: 'Nilai opsi yang dipilih.' },
                { name: 'options', type: 'Array<{ value, label, disabled? } | { label, options } | string | number>', default: '[]', description: 'Daftar opsi; entri { label, options } menjadi grup berjudul.' },
                { name: 'label', type: 'string', description: 'Label di atas trigger.' },
                { name: 'placeholder', type: 'string', default: `'Pilih…'`, description: 'Teks saat belum ada pilihan.' },
                { name: 'size', type: `'sm' | 'md'`, default: `'md'`, description: 'Tinggi trigger (32px / 44px).' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'id', type: 'string', description: 'Id trigger; otomatis bila kosong.' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Wajib dipilih; label diberi tanda bintang.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan select.' },
                { name: 'error', type: 'string', description: 'Pesan error; trigger berbingkai merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah select.' },
            ]"
        />
    </DocPage>
</template>
