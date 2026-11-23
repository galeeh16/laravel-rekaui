<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import YearPicker from '@/components/ui/YearPicker.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const archiveYear = ref('2026');
const fiscalYear = ref(2024);
const budgetYear = ref('');

const budgetYearError = computed(() => (budgetYear.value ? null : 'Tahun anggaran wajib dipilih.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import YearPicker from '@/components/ui/YearPicker.vue';

const archiveYear = ref('2026');
<\/script>

<template>
    <YearPicker v-model="archiveYear" label="Tahun arsip" />

    <p>Nilai v-model: {{ archiveYear || '—' }}</p>
</template>
`;

const inlineCode = `
<script setup>
import { ref } from 'vue';
import YearPicker from '@/components/ui/YearPicker.vue';

// Angka juga diterima; setelah dipilih, nilainya menjadi string 'YYYY'.
const fiscalYear = ref(2024);
<\/script>

<template>
    <YearPicker v-model="fiscalYear" inline min-year="2015" :max-year="2026" />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import YearPicker from '@/components/ui/YearPicker.vue';

const form = useForm({ budget_year: '' });
<\/script>

<template>
    <YearPicker
        v-model="form.budget_year"
        label="Tahun anggaran"
        name="budget_year"
        min-year="2020"
        hint="Mulai 2020."
        :error="form.errors.budget_year"
    />
</template>

// app/Http/Requests/StoreBudgetRequest.php
public function rules(): array
{
    return [
        'budget_year' => ['required', 'digits:4', 'integer', 'min:2020'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik field untuk membuka grid dua belas tahun; panah di kiri/kanan berpindah ke dua belas tahun sebelumnya/berikutnya. v-model berupa string YYYY."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <YearPicker v-model="archiveYear" label="Tahun arsip" />

                <p class="text-sm text-muted">Nilai v-model: {{ archiveYear || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Inline + batas tahun"
            description="Dengan inline, grid tampil langsung. Tahun di luar min-year/max-year nonaktif. v-model awal boleh berupa angka."
            :code="inlineCode"
        >
            <div class="flex flex-col items-center gap-3">
                <YearPicker v-model="fiscalYear" inline min-year="2015" :max-year="2026" />

                <p class="text-sm text-muted">Nilai v-model: {{ fiscalYear || '—' }} ({{ typeof fiscalYear }})</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan useForm Inertia dan pesan error dari form.errors. Nilai YYYY divalidasi dengan digits:4 (atau date_format:Y)."
            :code="formCode"
        >
            <YearPicker
                v-model="budgetYear"
                label="Tahun anggaran"
                name="budget_year"
                min-year="2020"
                hint="Mulai 2020."
                :error="budgetYearError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="YearPicker"
            :rows="[
                { name: 'v-model', type: 'string | number', default: `''`, description: 'Tahun terpilih; memilih tahun selalu menghasilkan string YYYY.' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'min-year', type: 'string | number', description: 'Tahun paling awal yang bisa dipilih.' },
                { name: 'max-year', type: 'string | number', description: 'Tahun paling akhir yang bisa dipilih.' },
                { name: 'placeholder', type: 'string', default: `'Pilih tahun'`, description: 'Teks saat belum ada tahun terpilih.' },
                { name: 'inline', type: 'boolean', default: 'false', description: 'Tampilkan grid langsung, tanpa field dan popover.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa label aksesibilitas dan format tahun.' },
                { name: 'name', type: 'string', description: 'Nama input tersembunyi berisi nilai v-model, untuk submit form biasa.' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
            ]"
        />
    </DocPage>
</template>
