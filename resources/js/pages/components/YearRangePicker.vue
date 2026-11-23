<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import YearRangePicker from '@/components/ui/YearRangePicker.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const archiveYears = ref({ start: '2021', end: '2025' });
const retentionYears = ref({ start: '', end: '' });
const strategicPlan = ref({ start: '', end: '' });

const strategicPlanError = computed(() => (strategicPlan.value.start && strategicPlan.value.end ? null : 'Periode renstra wajib dipilih lengkap.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import YearRangePicker from '@/components/ui/YearRangePicker.vue';

const archiveYears = ref({ start: '2021', end: '2025' });
<\/script>

<template>
    <YearRangePicker v-model="archiveYears" label="Rentang tahun arsip" />

    <p>Mulai: {{ archiveYears.start || '—' }} · Selesai: {{ archiveYears.end || '—' }}</p>
</template>
`;

const inlineCode = `
<template>
    <YearRangePicker v-model="retentionYears" inline :maximum-years="5" min-year="2015" max-year="2030" />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import YearRangePicker from '@/components/ui/YearRangePicker.vue';

const form = useForm({ plan_years: { start: '', end: '' } });
<\/script>

<template>
    <YearRangePicker
        v-model="form.plan_years"
        label="Periode renstra"
        name="plan_years"
        :maximum-years="5"
        hint="Maksimal 5 tahun."
        :error="form.errors['plan_years.start'] || form.errors['plan_years.end']"
    />
</template>

// app/Http/Requests/StoreStrategicPlanRequest.php
public function rules(): array
{
    return [
        'plan_years.start' => ['required', 'digits:4', 'integer'],
        'plan_years.end' => ['required', 'digits:4', 'integer', 'gte:plan_years.start'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik pertama memilih tahun mulai, klik kedua tahun selesai; popover menutup setelah rentang lengkap. v-model berupa { start, end } string YYYY."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <YearRangePicker v-model="archiveYears" label="Rentang tahun arsip" />

                <p class="text-sm text-muted">Mulai: {{ archiveYears.start || '—' }} · Selesai: {{ archiveYears.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Inline, maksimal 5 tahun"
            description="Grid tampil langsung dengan inline. Setelah tahun mulai dipilih, hanya 5 tahun di sekitarnya yang bisa dipilih."
            :code="inlineCode"
        >
            <div class="flex flex-col items-center gap-3">
                <YearRangePicker v-model="retentionYears" inline :maximum-years="5" min-year="2015" max-year="2030" />

                <p class="text-sm text-muted">Nilai v-model: {{ retentionYears.start || '—' }} s.d. {{ retentionYears.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan name, field merender input tersembunyi plan_years[start] dan plan_years[end]. Di server validasi dengan digits:4."
            :code="formCode"
        >
            <YearRangePicker
                v-model="strategicPlan"
                label="Periode renstra"
                name="plan_years"
                :maximum-years="5"
                hint="Maksimal 5 tahun."
                :error="strategicPlanError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="YearRangePicker"
            :rows="[
                { name: 'v-model', type: '{ start: string, end: string }', default: `{ start: '', end: '' }`, description: 'Rentang tahun, tiap tahun YYYY (angka juga diterima sebagai nilai awal).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'min-year', type: 'string | number', description: 'Tahun paling awal yang bisa dipilih.' },
                { name: 'max-year', type: 'string | number', description: 'Tahun paling akhir yang bisa dipilih.' },
                { name: 'maximum-years', type: 'number', description: 'Panjang rentang maksimal, dalam tahun.' },
                { name: 'placeholder', type: 'string', default: `'Pilih rentang tahun'`, description: 'Teks saat belum ada rentang terpilih.' },
                { name: 'inline', type: 'boolean', default: 'false', description: 'Tampilkan grid langsung, tanpa field dan popover.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa label aksesibilitas dan format tahun.' },
                { name: 'name', type: 'string', description: 'Nama dasar input tersembunyi: name[start] dan name[end].' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
            ]"
        />
    </DocPage>
</template>
