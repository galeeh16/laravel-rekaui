<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import MonthRangePicker from '@/components/ui/MonthRangePicker.vue';

const quarter = ref({ start: '2026-07', end: '2026-09' });
const budgetPeriod = ref({ start: '', end: '' });
const reportPeriod = ref({ start: '', end: '' });

const reportPeriodError = computed(() => (reportPeriod.value.start && reportPeriod.value.end ? null : 'Periode laporan wajib dipilih lengkap.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import MonthRangePicker from '@/components/ui/MonthRangePicker.vue';

const quarter = ref({ start: '2026-07', end: '2026-09' });
<\/script>

<template>
    <MonthRangePicker v-model="quarter" label="Triwulan" />

    <p>Mulai: {{ quarter.start || '—' }} · Selesai: {{ quarter.end || '—' }}</p>
</template>
`;

const inlineCode = `
<template>
    <MonthRangePicker v-model="budgetPeriod" inline :maximum-months="6" max-month="2026-12" />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import MonthRangePicker from '@/components/ui/MonthRangePicker.vue';

const form = useForm({ period: { start: '', end: '' } });
<\/script>

<template>
    <MonthRangePicker
        v-model="form.period"
        label="Periode laporan"
        name="period"
        :maximum-months="12"
        hint="Maksimal 12 bulan."
        :error="form.errors['period.start'] || form.errors['period.end']"
    />
</template>

// app/Http/Requests/ExportReportRequest.php
public function rules(): array
{
    return [
        'period.start' => ['required', 'date_format:Y-m'],
        'period.end' => ['required', 'date_format:Y-m', 'after_or_equal:period.start'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik pertama memilih bulan mulai, klik kedua bulan selesai; popover menutup setelah rentang lengkap. v-model berupa { start, end } string YYYY-MM."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <MonthRangePicker v-model="quarter" label="Triwulan" />

                <p class="text-sm text-muted">Mulai: {{ quarter.start || '—' }} · Selesai: {{ quarter.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Inline, maksimal 6 bulan"
            description="Grid tampil langsung dengan inline. Setelah bulan mulai dipilih, hanya 6 bulan di sekitarnya yang bisa dipilih."
            :code="inlineCode"
        >
            <div class="flex flex-col items-center gap-3">
                <MonthRangePicker v-model="budgetPeriod" inline :maximum-months="6" max-month="2026-12" />

                <p class="text-sm text-muted">Nilai v-model: {{ budgetPeriod.start || '—' }} s.d. {{ budgetPeriod.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan name, field merender input tersembunyi period[start] dan period[end]. Di server validasi dengan date_format:Y-m."
            :code="formCode"
        >
            <MonthRangePicker
                v-model="reportPeriod"
                label="Periode laporan"
                name="period"
                :maximum-months="12"
                hint="Maksimal 12 bulan."
                :error="reportPeriodError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="MonthRangePicker"
            :rows="[
                { name: 'v-model', type: '{ start: string, end: string }', default: `{ start: '', end: '' }`, description: 'Rentang bulan, tiap bulan YYYY-MM (end kosong selama belum dipilih).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'min-month', type: 'string', description: 'Bulan paling awal yang bisa dipilih, YYYY-MM.' },
                { name: 'max-month', type: 'string', description: 'Bulan paling akhir yang bisa dipilih, YYYY-MM.' },
                { name: 'maximum-months', type: 'number', description: 'Panjang rentang maksimal, dalam bulan.' },
                { name: 'placeholder', type: 'string', default: `'Pilih rentang bulan'`, description: 'Teks saat belum ada rentang terpilih.' },
                { name: 'inline', type: 'boolean', default: 'false', description: 'Tampilkan grid langsung, tanpa field dan popover.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa nama bulan.' },
                { name: 'name', type: 'string', description: 'Nama dasar input tersembunyi: name[start] dan name[end].' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
            ]"
        />
    </DocPage>
</template>
