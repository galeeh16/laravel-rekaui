<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import DateRangePicker from '@/components/ui/DateRangePicker.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const leave = ref({ start: '2026-12-21', end: '2026-12-23' });
const reportPeriod = ref({ start: '', end: '' });
const filterPeriod = ref({ start: '', end: '' });

const filterPeriodError = computed(() => (filterPeriod.value.start && filterPeriod.value.end ? null : 'Periode wajib diisi lengkap.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import DateRangePicker from '@/components/ui/DateRangePicker.vue';

const leave = ref({ start: '2026-12-21', end: '2026-12-23' });
<\/script>

<template>
    <DateRangePicker v-model="leave" label="Tanggal cuti" />

    <p>Mulai: {{ leave.start || '—' }} · Selesai: {{ leave.end || '—' }}</p>
</template>
`;

const monthsCode = `
<template>
    <DateRangePicker
        v-model="reportPeriod"
        label="Periode laporan"
        :number-of-months="2"
        :maximum-days="31"
        max-date="2026-12-31"
    />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import DateRangePicker from '@/components/ui/DateRangePicker.vue';

const form = useForm({ period: { start: '', end: '' } });
<\/script>

<template>
    <DateRangePicker
        v-model="form.period"
        label="Filter surat masuk"
        name="period"
        hint="Rentang tanggal surat diterima."
        :error="form.errors['period.start'] || form.errors['period.end']"
    />
</template>

// app/Http/Requests/FilterLettersRequest.php
public function rules(): array
{
    return [
        'period.start' => ['required', 'date_format:Y-m-d'],
        'period.end' => ['required', 'date_format:Y-m-d', 'after_or_equal:period.start'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Ketik kedua tanggal, atau klik ikon kalender lalu pilih tanggal mulai dan selesai; popover menutup setelah rentang lengkap. v-model berupa { start, end } string YYYY-MM-DD."
            :code="basicCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <DateRangePicker v-model="leave" label="Tanggal cuti" />

                <p class="text-sm text-muted">Mulai: {{ leave.start || '—' }} · Selesai: {{ leave.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Dua bulan, maksimal 31 hari"
            description="number-of-months menampilkan dua bulan di popover; maximum-days membatasi panjang rentang."
            :code="monthsCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <DateRangePicker v-model="reportPeriod" label="Periode laporan" :number-of-months="2" :maximum-days="31" max-date="2026-12-31" />

                <p class="text-sm text-muted">Nilai v-model: {{ reportPeriod.start || '—' }} s.d. {{ reportPeriod.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan name, field merender input tersembunyi period[start] dan period[end]. Di server validasi tiap tanggal dengan date_format:Y-m-d."
            :code="formCode"
        >
            <DateRangePicker
                v-model="filterPeriod"
                label="Filter surat masuk"
                name="period"
                hint="Rentang tanggal surat diterima."
                :error="filterPeriodError"
                class="w-full max-w-sm"
            />
        </DocExample>

        <DocProps
            title="DateRangePicker"
            :rows="[
                { name: 'v-model', type: '{ start: string, end: string }', default: `{ start: '', end: '' }`, description: 'Rentang, tiap tanggal YYYY-MM-DD (string kosong jika belum dipilih).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'format', type: 'string', default: `'DD/MM/YYYY'`, description: 'Urutan dan pemisah segmen kedua tanggal. Token: DD, D, MM, M, YYYY.' },
                { name: 'min-date', type: 'string', description: 'Tanggal paling awal yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'max-date', type: 'string', description: 'Tanggal paling akhir yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'maximum-days', type: 'number', description: 'Panjang rentang maksimal, dalam hari.' },
                { name: 'number-of-months', type: 'number', default: '1', description: 'Jumlah bulan di popover kalender.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa kalender dan placeholder segmen.' },
                { name: 'name', type: 'string', description: 'Nama dasar input tersembunyi: name[start] dan name[end].' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field dan kalender.' },
            ]"
        />
    </DocPage>
</template>
