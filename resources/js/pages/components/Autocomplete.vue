<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Autocomplete from '@/components/ui/Autocomplete.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const city = ref('');

const cities = ['Bandung', 'Bekasi', 'Bogor', 'Cirebon', 'Denpasar', 'Jakarta', 'Makassar', 'Medan', 'Semarang', 'Surabaya', 'Yogyakarta'];

const subject = ref('');

const commonSubjects = [
    'Undangan rapat koordinasi',
    'Undangan sosialisasi',
    'Permohonan data',
    'Permohonan izin',
    'Laporan kegiatan',
    'Pemberitahuan libur',
];

/** Pretend server-side search: results arrive after a short delay. */
const agencyDirectory = [
    { value: 'Badan Kepegawaian Daerah', hint: 'BKD' },
    { value: 'Badan Pendapatan Daerah', hint: 'Bapenda' },
    { value: 'Badan Perencanaan Pembangunan Daerah', hint: 'Bappeda' },
    { value: 'Dinas Kesehatan', hint: 'Dinkes' },
    { value: 'Dinas Pendidikan', hint: 'Disdik' },
    { value: 'Dinas Pekerjaan Umum', hint: 'DPU' },
    { value: 'Dinas Perhubungan', hint: 'Dishub' },
    { value: 'Inspektorat Daerah', hint: 'Itda' },
];

const agency = ref('');
const agencyResults = ref([]);
const isSearching = ref(false);
let searchTimeout = null;

watch(agency, (query) => {
    clearTimeout(searchTimeout);

    if (!query) {
        agencyResults.value = [];
        isSearching.value = false;

        return;
    }

    isSearching.value = true;
    searchTimeout = setTimeout(() => {
        const keyword = query.toLowerCase();
        agencyResults.value = agencyDirectory.filter(
            (item) => item.value.toLowerCase().includes(keyword) || item.hint.toLowerCase().includes(keyword),
        );
        isSearching.value = false;
    }, 600);
});

onBeforeUnmount(() => clearTimeout(searchTimeout));

const basicCode = `
<script setup>
import { ref } from 'vue';
import Autocomplete from '@/components/ui/Autocomplete.vue';

const city = ref('');
<\/script>

<template>
    <Autocomplete
        v-model="city"
        label="Kota tujuan"
        placeholder="Ketik nama kota"
        :suggestions="['Bandung', 'Bekasi', 'Bogor', 'Cirebon', 'Denpasar', 'Jakarta', /* ... */]"
    />

    <p>Nilai: {{ city }}</p>
</template>
`;

const emptyCode = `
<template>
    <Autocomplete
        v-model="form.subject"
        label="Perihal"
        placeholder="Mis. Undangan rapat koordinasi"
        :suggestions="commonSubjects"
        empty-text="Tidak ada saran, teks tetap bisa dipakai."
        :error="form.errors.subject"
    />
</template>
`;

const serverCode = `
<script setup>
import { useHttp } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';
import Autocomplete from '@/components/ui/Autocomplete.vue';

const agency = ref('');
const results = ref([]);
const http = useHttp({ q: '' });
let searchTimeout = null;

watch(agency, (query) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        http.cancel();
        http.q = query;
        http.get(route('agencies.search'), {
            onSuccess: (data) => (results.value = data),
        });
    }, 300);
});
<\/script>

<template>
    <Autocomplete
        v-model="agency"
        label="Instansi penerima"
        icon="search"
        placeholder="Cari instansi…"
        :suggestions="results"
        :loading="http.processing"
        ignore-filter
        empty-text="Instansi tidak ditemukan."
    />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Berbeda dengan Combobox, nilainya teks bebas: saran hanya membantu mengisi. Daftar tersembunyi bila tidak ada yang cocok."
            :code="basicCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Autocomplete v-model="city" label="Kota tujuan" placeholder="Ketik nama kota" :suggestions="cities" />

                <p class="text-sm text-muted">Nilai: {{ city || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Teks saat kosong"
            description="empty-text menampilkan pesan ketika tidak ada saran yang cocok, alih-alih menyembunyikan daftar."
            :code="emptyCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Autocomplete
                    v-model="subject"
                    label="Perihal"
                    placeholder="Mis. Undangan rapat koordinasi"
                    :suggestions="commonSubjects"
                    empty-text="Tidak ada saran, teks tetap bisa dipakai."
                />

                <p class="text-sm text-muted">Nilai: {{ subject || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Pencarian di server"
            description="Dengan ignore-filter, saran ditampilkan apa adanya dan loading menampilkan spinner. Di demo ini respons server disimulasikan."
            :code="serverCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Autocomplete
                    v-model="agency"
                    label="Instansi penerima"
                    icon="search"
                    placeholder="Cari instansi…"
                    :suggestions="agencyResults"
                    :loading="isSearching"
                    ignore-filter
                    empty-text="Instansi tidak ditemukan."
                />

                <p class="text-sm text-muted">Nilai: {{ agency || '—' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Autocomplete"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Teks yang diketik atau saran yang dipilih.' },
                { name: 'suggestions', type: 'Array<string | { value, hint? }>', default: '[]', description: 'Daftar saran; hint tampil di sisi kanan.' },
                { name: 'label', type: 'string', description: 'Label di atas input.' },
                { name: 'placeholder', type: 'string', description: 'Placeholder input.' },
                { name: 'icon', type: 'string', description: 'Ikon di awal input, mis. search.' },
                { name: 'empty-text', type: 'string', description: 'Pesan saat tidak ada saran cocok; kosong = daftar disembunyikan.' },
                { name: 'loading', type: 'boolean', default: 'false', description: 'Tampilkan spinner dan teks Mencari….' },
                { name: 'ignore-filter', type: 'boolean', default: 'false', description: 'Jangan saring saran di browser (penyaringan di server).' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Wajib diisi; label diberi tanda bintang.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan input.' },
                { name: 'error', type: 'string', description: 'Pesan error; kolom berbingkai merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah kolom.' },
            ]"
        />
    </DocPage>
</template>
