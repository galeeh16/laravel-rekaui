<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Listbox from '@/components/ui/Listbox.vue';

const templates = [
    { value: 'undangan', label: 'Surat undangan', hint: 'Rapat, acara, sosialisasi', icon: 'calendar' },
    { value: 'dinas', label: 'Surat dinas', hint: 'Korespondensi resmi antarinstansi', icon: 'file-text' },
    { value: 'edaran', label: 'Surat edaran', hint: 'Pemberitahuan ke seluruh pegawai', icon: 'send' },
    { value: 'tugas', label: 'Surat tugas', hint: 'Penugasan pegawai', icon: 'user' },
    { value: 'keputusan', label: 'Surat keputusan', hint: 'Hanya untuk pimpinan', icon: 'lock', disabled: true },
];

const template = ref('undangan');

const staff = [
    {
        label: 'Bidang Keuangan',
        options: [
            { value: 'siti', label: 'Siti Rahmawati', hint: 'Kepala Bidang' },
            { value: 'dewi', label: 'Dewi Lestari', hint: 'Analis Anggaran' },
            { value: 'agus', label: 'Agus Setiawan', hint: 'Bendahara' },
        ],
    },
    {
        label: 'Bidang Perencanaan',
        options: [
            { value: 'andi', label: 'Andi Pratama', hint: 'Kepala Bidang' },
            { value: 'rina', label: 'Rina Marlina', hint: 'Perencana Ahli Muda' },
            { value: 'fajar', label: 'Fajar Nugroho', hint: 'Analis Data' },
        ],
    },
];

const recipients = ref(['dewi']);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Listbox from '@/components/ui/Listbox.vue';

const templates = [
    { value: 'undangan', label: 'Surat undangan', hint: 'Rapat, acara, sosialisasi', icon: 'calendar' },
    { value: 'dinas', label: 'Surat dinas', hint: 'Korespondensi resmi antarinstansi', icon: 'file-text' },
    { value: 'edaran', label: 'Surat edaran', hint: 'Pemberitahuan ke seluruh pegawai', icon: 'send' },
    { value: 'keputusan', label: 'Surat keputusan', hint: 'Hanya untuk pimpinan', icon: 'lock', disabled: true },
];

const template = ref('undangan');
<\/script>

<template>
    <Listbox v-model="template" label="Template surat" :options="templates" />

    <p>Nilai: {{ template }}</p>
</template>
`;

const multipleCode = `
<script setup>
import { ref } from 'vue';
import Listbox from '@/components/ui/Listbox.vue';

const staff = [
    {
        label: 'Bidang Keuangan',
        options: [
            { value: 'siti', label: 'Siti Rahmawati', hint: 'Kepala Bidang' },
            { value: 'dewi', label: 'Dewi Lestari', hint: 'Analis Anggaran' },
        ],
    },
    {
        label: 'Bidang Perencanaan',
        options: [
            { value: 'andi', label: 'Andi Pratama', hint: 'Kepala Bidang' },
            { value: 'rina', label: 'Rina Marlina', hint: 'Perencana Ahli Muda' },
        ],
    },
];

const recipients = ref(['dewi']);
<\/script>

<template>
    <Listbox
        v-model="recipients"
        label="Penerima disposisi"
        :options="staff"
        search-placeholder="Cari nama atau jabatan…"
        multiple
        searchable
    />

    <p>Nilai: {{ recipients }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Daftar pilihan yang selalu terlihat. Gunakan panah atas/bawah atau ketik huruf awal untuk berpindah; opsi bisa punya ikon dan hint."
            :code="basicCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Listbox v-model="template" label="Template surat" :options="templates" />

                <p class="text-sm text-muted">Nilai: {{ template ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Banyak pilihan + pencarian"
            description="multiple membuat v-model berupa array; searchable menambahkan kolom pencarian yang juga mencocokkan hint."
            :code="multipleCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Listbox
                    v-model="recipients"
                    label="Penerima disposisi"
                    :options="staff"
                    search-placeholder="Cari nama atau jabatan…"
                    multiple
                    searchable
                />

                <p class="text-sm text-muted">Nilai: {{ recipients.join(', ') || '—' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Listbox"
            :rows="[
                { name: 'v-model', type: 'string | number | array', description: 'Nilai terpilih; array bila multiple.' },
                { name: 'options', type: 'Array<{ value, label, hint?, icon?, disabled? } | { label, options } | string | number>', description: 'Daftar opsi (wajib); entri { label, options } menjadi grup berjudul.' },
                { name: 'label', type: 'string', description: 'Judul daftar.' },
                { name: 'multiple', type: 'boolean', default: 'false', description: 'Boleh memilih banyak opsi.' },
                { name: 'searchable', type: 'boolean', default: 'false', description: 'Tambahkan kolom pencarian di atas daftar.' },
                { name: 'search-placeholder', type: 'string', default: `'Cari…'`, description: 'Placeholder kolom pencarian.' },
                { name: 'empty-text', type: 'string', default: `'Tidak ada hasil.'`, description: 'Teks saat pencarian tidak menemukan opsi.' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Wajib dipilih; label diberi tanda bintang.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan daftar.' },
                { name: 'error', type: 'string', description: 'Pesan error; bingkai merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah daftar.' },
            ]"
        />
    </DocPage>
</template>
