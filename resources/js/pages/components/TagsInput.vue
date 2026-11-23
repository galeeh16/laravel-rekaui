<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import TagsInput from '@/components/ui/TagsInput.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const carbonCopies = ref(['Sekretariat', 'Bagian Keuangan']);
const keywords = ref(['anggaran', 'rapat koordinasi']);

const basicCode = `
<script setup>
import { ref } from 'vue';
import TagsInput from '@/components/ui/TagsInput.vue';

const carbonCopies = ref(['Sekretariat', 'Bagian Keuangan']);
<\/script>

<template>
    <TagsInput
        v-model="carbonCopies"
        label="Tembusan"
        placeholder="Tambah unit lalu tekan Enter…"
        hint="Enter atau koma untuk menambah; Backspace untuk menghapus tag terakhir."
    />

    <p>v-model: {{ JSON.stringify(carbonCopies) }}</p>
</template>
`;

const limitCode = `
<script setup>
import { ref } from 'vue';
import TagsInput from '@/components/ui/TagsInput.vue';

const keywords = ref(['anggaran', 'rapat koordinasi']);
<\/script>

<template>
    <TagsInput
        v-model="keywords"
        label="Kata kunci arsip"
        placeholder="Tambah kata kunci…"
        :max="5"
        clearable
        hint="Tempel teks berisi koma untuk menambah beberapa sekaligus. Maksimal 5."
    />

    <p>{{ keywords.length }} / 5 kata kunci</p>
</template>
`;

const statesCode = `
<template>
    <TagsInput :model-value="['Direktur Utama']" label="Penerima" error="Penerima eksternal wajib diisi." />

    <TagsInput :model-value="['Arsip', 'Rahasia']" label="Label (terkunci)" disabled />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar + v-model"
            description="Ketik lalu tekan Enter atau koma. Duplikat ditolak; teks yang belum di-Enter ikut ditambahkan saat field kehilangan fokus."
            :code="basicCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <TagsInput
                    v-model="carbonCopies"
                    label="Tembusan"
                    placeholder="Tambah unit lalu tekan Enter…"
                    hint="Enter atau koma untuk menambah; Backspace untuk menghapus tag terakhir."
                />

                <p class="font-mono text-sm text-muted">v-model: {{ JSON.stringify(carbonCopies) }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Batas & hapus semua"
            description="max membatasi jumlah tag; clearable menampilkan tombol untuk mengosongkan semuanya."
            :code="limitCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <TagsInput
                    v-model="keywords"
                    label="Kata kunci arsip"
                    placeholder="Tambah kata kunci…"
                    :max="5"
                    clearable
                    hint="Tempel teks berisi koma untuk menambah beberapa sekaligus. Maksimal 5."
                />

                <p class="text-sm text-muted">{{ keywords.length }} / 5 kata kunci</p>
            </div>
        </DocExample>

        <DocExample title="Error & nonaktif" :code="statesCode">
            <div class="grid w-full max-w-xl gap-5 sm:grid-cols-2">
                <TagsInput :model-value="['Direktur Utama']" label="Penerima" error="Penerima eksternal wajib diisi." />
                <TagsInput :model-value="['Arsip', 'Rahasia']" label="Label (terkunci)" disabled />
            </div>
        </DocExample>

        <DocProps
            title="TagsInput"
            :rows="[
                { name: 'v-model', type: 'string[]', default: '[]', description: 'Daftar tag.' },
                { name: 'label', type: 'string', description: 'Label di atas field, terhubung ke input.' },
                { name: 'placeholder', type: 'string', default: `'Ketik lalu tekan Enter…'`, description: 'Placeholder input; disembunyikan saat max tercapai.' },
                { name: 'max', type: 'number', default: '0', description: 'Jumlah tag maksimal; 0 berarti tanpa batas.' },
                { name: 'delimiter', type: 'string | RegExp', default: `','`, description: 'Karakter yang juga menambahkan tag.' },
                { name: 'duplicate', type: 'boolean', default: 'false', description: 'Izinkan tag yang sama lebih dari sekali.' },
                { name: 'add-on-paste', type: 'boolean', default: 'true', description: 'Pecah teks yang ditempel berdasarkan delimiter.' },
                { name: 'add-on-blur', type: 'boolean', default: 'true', description: 'Tambahkan teks yang diketik saat field kehilangan fokus.' },
                { name: 'clearable', type: 'boolean', default: 'false', description: 'Tampilkan tombol × untuk menghapus semua tag.' },
                { name: 'name', type: 'string', description: 'Nama field tersembunyi untuk form HTML biasa.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
                { name: 'error', type: 'string', description: 'Pesan error; border berubah merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
            ]"
        />
    </DocPage>
</template>
