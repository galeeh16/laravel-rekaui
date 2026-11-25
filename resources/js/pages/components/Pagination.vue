<script setup>
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import Pagination from '@/components/ui/Pagination.vue';

const subjects = ['Undangan rapat koordinasi', 'Permohonan data anggaran', 'Laporan kegiatan bulanan', 'Nota dinas pengadaan', 'Surat tugas perjalanan dinas'];

const letters = Array.from({ length: 47 }, (_, index) => ({
    number: `${String(index + 1).padStart(3, '0')}/SK/XI/2026`,
    subject: subjects[index % subjects.length],
}));

const itemsPerPage = 5;
const page = ref(1);

const visibleLetters = computed(() => letters.slice((page.value - 1) * itemsPerPage, page.value * itemsPerPage));
const firstShown = computed(() => (page.value - 1) * itemsPerPage + 1);
const lastShown = computed(() => Math.min(page.value * itemsPerPage, letters.length));

const archivePage = ref(12);

const clientCode = `
<script setup>
import { computed, ref } from 'vue';
import Pagination from '@/components/ui/Pagination.vue';

const props = defineProps({ letters: { type: Array, required: true } });

const itemsPerPage = 5;
const page = ref(1);

const visibleLetters = computed(() =>
    props.letters.slice((page.value - 1) * itemsPerPage, page.value * itemsPerPage),
);
<\/script>

<template>
    <ul>
        <li v-for="letter in visibleLetters" :key="letter.number">{{ letter.subject }}</li>
    </ul>

    <Pagination v-model:page="page" :total="letters.length" :items-per-page="itemsPerPage" />

    <p>Halaman {{ page }}</p>
</template>
`;

const siblingCode = `
<template>
    <!-- 240 arsip, 10 per halaman = 24 halaman; 2 tombol di setiap sisi halaman aktif. -->
    <Pagination v-model:page="archivePage" :total="240" :items-per-page="10" :sibling-count="2" />
</template>
`;

const laravelCode = `
// app/Http/Controllers/LetterController.php
public function index(Request $request): Response
{
    return Inertia::render('letters/Index', [
        // LengthAwarePaginator: data, current_page, last_page, per_page, total, first_page_url, ...
        'letters' => Letter::query()
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString(),
    ]);
}

<!-- resources/js/pages/letters/Index.vue -->
<script setup>
import Pagination from '@/components/ui/Pagination.vue';

defineProps({ letters: { type: Object, required: true } });
<\/script>

<template>
    <table>
        <tr v-for="letter in letters.data" :key="letter.id">...</tr>
    </table>

    <p>Menampilkan {{ letters.from }}–{{ letters.to }} dari {{ letters.total }} surat</p>

    <!-- Each page click is an Inertia visit to ?page=N, keeping filters in the query string. -->
    <Pagination :paginator="letters" />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Client-side + v-model:page"
            description="Tanpa paginator: berikan total dan items-per-page, lalu potong data sendiri berdasarkan halaman aktif."
            :code="clientCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-4">
                <ul class="flex flex-col divide-y divide-line rounded-xl border border-line">
                    <li v-for="letter in visibleLetters" :key="letter.number" class="flex items-center justify-between gap-3 px-4 py-2.5 text-sm">
                        <span class="truncate">{{ letter.subject }}</span>
                        <span class="shrink-0 font-mono text-xs text-muted">{{ letter.number }}</span>
                    </li>
                </ul>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <span class="text-sm text-muted">Menampilkan {{ firstShown }}–{{ lastShown }} dari {{ letters.length }} surat</span>
                    <Pagination v-model:page="page" :total="letters.length" :items-per-page="itemsPerPage" />
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Banyak halaman"
            description="Halaman pertama dan terakhir selalu tampil; sibling-count mengatur jumlah tombol di sekitar halaman aktif."
            :code="siblingCode"
        >
            <div class="flex flex-col items-center gap-3">
                <Pagination v-model:page="archivePage" :total="240" :items-per-page="10" :sibling-count="2" />
                <p class="text-sm text-muted">Halaman {{ archivePage }} dari 24</p>
            </div>
        </DocExample>

        <DocExample
            title="Data dari Laravel (Inertia)"
            description="Kirim hasil ->paginate() sebagai prop; setiap perpindahan halaman menjadi kunjungan Inertia ke ?page=N dengan query string lain tetap terjaga."
            :code="laravelCode"
        >
            <div class="flex max-w-md flex-col items-center gap-4 text-center">
                <p class="text-sm text-muted">
                    Mode ini membutuhkan data dari server, jadi contohnya ada di tab Code. Lihat penggunaan nyatanya di tabel surat
                    keluar pada Dashboard.
                </p>
                <Button :href="route('dashboard')" variant="secondary" icon="layout-grid" icon-trailing="arrow-right">Buka Dashboard</Button>
            </div>
        </DocExample>

        <DocProps
            title="Pagination"
            :rows="[
                { name: 'paginator', type: 'LengthAwarePaginator (JSON)', description: 'Hasil ->paginate() Laravel. Jika diisi, pindah halaman memakai Inertia dan prop lain diabaikan.' },
                { name: 'v-model:page', type: 'number', default: '1', description: 'Mode client-side: halaman aktif.' },
                { name: 'total', type: 'number', default: '0', description: 'Mode client-side: jumlah seluruh item.' },
                { name: 'items-per-page', type: 'number', default: '10', description: 'Mode client-side: jumlah item per halaman.' },
                { name: 'sibling-count', type: 'number', default: '1', description: 'Jumlah tombol halaman di setiap sisi halaman aktif.' },
            ]"
        />
    </DocPage>
</template>
