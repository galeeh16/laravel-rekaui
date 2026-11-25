<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Combobox from '@/components/ui/Combobox.vue';

const units = [
    { value: 'tu', label: 'Tata Usaha' },
    { value: 'kepegawaian', label: 'Kepegawaian' },
    { value: 'keuangan', label: 'Keuangan' },
    { value: 'perencanaan', label: 'Perencanaan & Program' },
    { value: 'hukum', label: 'Hukum & Organisasi' },
    { value: 'humas', label: 'Hubungan Masyarakat' },
    { value: 'umum', label: 'Umum & Perlengkapan' },
    { value: 'ti', label: 'Teknologi Informasi' },
];

const unit = ref('keuangan');

const officials = [
    {
        label: 'Pimpinan',
        options: [
            { value: 'kadis', label: 'Dr. Ratna Wulandari', hint: 'Kepala Dinas' },
            { value: 'sekdis', label: 'Budi Santoso, S.E.', hint: 'Sekretaris Dinas' },
        ],
    },
    {
        label: 'Kepala Bidang',
        options: [
            { value: 'kabid-keu', label: 'Siti Rahmawati, M.Ak.', hint: 'Kabid Keuangan' },
            { value: 'kabid-ren', label: 'Andi Pratama, S.T.', hint: 'Kabid Perencanaan' },
            { value: 'kabid-hk', label: 'Yohanes Siregar, S.H.', hint: 'Kabid Hukum', disabled: true },
        ],
    },
];

const signer = ref(undefined);

const ccRecipients = ref(['kadis', 'kabid-keu']);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Combobox from '@/components/ui/Combobox.vue';

const units = [
    { value: 'tu', label: 'Tata Usaha' },
    { value: 'kepegawaian', label: 'Kepegawaian' },
    { value: 'keuangan', label: 'Keuangan' },
    // ...
];

const unit = ref('keuangan');
<\/script>

<template>
    <Combobox v-model="unit" label="Unit tujuan" placeholder="Cari unit…" :options="units" />

    <p>Nilai: {{ unit }}</p>
</template>
`;

const groupCode = `
<script setup>
import { ref } from 'vue';
import Combobox from '@/components/ui/Combobox.vue';

const officials = [
    {
        label: 'Pimpinan',
        options: [
            { value: 'kadis', label: 'Dr. Ratna Wulandari', hint: 'Kepala Dinas' },
            { value: 'sekdis', label: 'Budi Santoso, S.E.', hint: 'Sekretaris Dinas' },
        ],
    },
    {
        label: 'Kepala Bidang',
        options: [
            { value: 'kabid-keu', label: 'Siti Rahmawati, M.Ak.', hint: 'Kabid Keuangan' },
            { value: 'kabid-hk', label: 'Yohanes Siregar, S.H.', hint: 'Kabid Hukum', disabled: true },
        ],
    },
];

const signer = ref();
<\/script>

<template>
    <Combobox
        v-model="signer"
        label="Penandatangan"
        placeholder="Cari nama atau jabatan…"
        :options="officials"
        hint="Coba ketik &quot;kabid&quot;."
        required
    />
</template>
`;

const multipleCode = `
<script setup>
import { ref } from 'vue';
import Combobox from '@/components/ui/Combobox.vue';

const ccRecipients = ref(['kadis', 'kabid-keu']);
<\/script>

<template>
    <Combobox
        v-model="ccRecipients"
        label="Tembusan"
        placeholder="Tambah penerima…"
        :options="officials"
        multiple
    />

    <p>Nilai: {{ ccRecipients }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Ketik untuk menyaring daftar (tidak peka huruf besar/kecil). Input menampilkan label opsi yang dipilih."
            :code="basicCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Combobox v-model="unit" label="Unit tujuan" placeholder="Cari unit…" :options="units" />

                <p class="text-sm text-muted">Nilai: {{ unit ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Grup & keterangan"
            description="Opsi bisa dikelompokkan dan diberi hint; pencarian juga mencocokkan hint, misalnya jabatan."
            :code="groupCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Combobox
                    v-model="signer"
                    label="Penandatangan"
                    placeholder="Cari nama atau jabatan…"
                    :options="officials"
                    hint="Coba ketik &quot;kabid&quot;."
                    required
                />

                <p class="text-sm text-muted">Nilai: {{ signer ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Banyak pilihan"
            description="Dengan multiple, v-model berupa array dan pilihan tampil sebagai chip. Backspace di kolom kosong menghapus chip terakhir."
            :code="multipleCode"
        >
            <div class="flex w-full max-w-md flex-col gap-3">
                <Combobox v-model="ccRecipients" label="Tembusan" placeholder="Tambah penerima…" :options="officials" multiple />

                <p class="text-sm text-muted">Nilai: {{ ccRecipients.join(', ') || '—' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Combobox"
            :rows="[
                { name: 'v-model', type: 'string | number | array', description: 'Nilai terpilih; array bila multiple.' },
                { name: 'options', type: 'Array<{ value, label, hint?, disabled? } | { label, options } | string | number>', default: '[]', description: 'Daftar opsi; entri { label, options } menjadi grup berjudul.' },
                { name: 'label', type: 'string', description: 'Label di atas input.' },
                { name: 'placeholder', type: 'string', default: `'Cari…'`, description: 'Placeholder kolom pencarian.' },
                { name: 'empty-text', type: 'string', default: `'Tidak ada hasil.'`, description: 'Teks saat pencarian tidak cocok dengan opsi mana pun.' },
                { name: 'multiple', type: 'boolean', default: 'false', description: 'Boleh memilih banyak opsi (tampil sebagai chip).' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'id', type: 'string', description: 'Id input; otomatis bila kosong.' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Wajib diisi; label diberi tanda bintang.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan combobox.' },
                { name: 'error', type: 'string', description: 'Pesan error; kolom berbingkai merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah kolom.' },
            ]"
        />
    </DocPage>
</template>
