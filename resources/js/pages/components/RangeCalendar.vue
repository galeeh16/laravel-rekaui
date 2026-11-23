<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import RangeCalendar from '@/components/ui/RangeCalendar.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const leave = ref({ start: '2026-11-25', end: '2026-11-27' });
const reportPeriod = ref({ start: '', end: '' });

/** Example holidays (Christmas and the collective leave around it). */
const holidays = ['2026-12-24', '2026-12-25', '2026-12-26'];

const basicCode = `
<script setup>
import { ref } from 'vue';
import RangeCalendar from '@/components/ui/RangeCalendar.vue';

const leave = ref({ start: '2026-11-25', end: '2026-11-27' });
<\/script>

<template>
    <RangeCalendar v-model="leave" />

    <p>Cuti: {{ leave.start || '—' }} s.d. {{ leave.end || '—' }}</p>
</template>

<!--
    v-model berupa { start, end } string YYYY-MM-DD. Validasi di Laravel:
    'leave.start' => ['required', 'date_format:Y-m-d'],
    'leave.end' => ['required', 'date_format:Y-m-d', 'after_or_equal:leave.start'],
-->
`;

const monthsCode = `
<script setup>
import { ref } from 'vue';
import RangeCalendar from '@/components/ui/RangeCalendar.vue';

const reportPeriod = ref({ start: '', end: '' });
const holidays = ['2026-12-24', '2026-12-25', '2026-12-26'];
<\/script>

<template>
    <RangeCalendar
        v-model="reportPeriod"
        :number-of-months="2"
        :maximum-days="31"
        :is-date-unavailable="(date) => holidays.includes(date)"
    />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik pertama memilih tanggal mulai, klik kedua tanggal selesai; arahkan kursor untuk melihat pratinjau rentang. Selama tanggal selesai belum dipilih, end berisi string kosong."
            :code="basicCode"
        >
            <div class="flex flex-col items-center gap-3">
                <RangeCalendar v-model="leave" />

                <p class="text-sm text-muted">Cuti: {{ leave.start || '—' }} s.d. {{ leave.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Dua bulan, maksimal 31 hari"
            description="Cocok untuk periode laporan. Setelah tanggal mulai dipilih, hanya 31 hari di sekitarnya yang bisa dipilih; rentang juga tidak boleh melewati hari libur."
            :code="monthsCode"
        >
            <div class="flex flex-col items-center gap-3">
                <RangeCalendar
                    v-model="reportPeriod"
                    :number-of-months="2"
                    :maximum-days="31"
                    :is-date-unavailable="(date) => holidays.includes(date)"
                />

                <p class="text-sm text-muted">Periode: {{ reportPeriod.start || '—' }} s.d. {{ reportPeriod.end || '—' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="RangeCalendar"
            :rows="[
                { name: 'v-model', type: '{ start: string, end: string }', default: `{ start: '', end: '' }`, description: 'Rentang terpilih, tiap tanggal YYYY-MM-DD (string kosong jika belum dipilih).' },
                { name: 'min-date', type: 'string', description: 'Tanggal paling awal yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'max-date', type: 'string', description: 'Tanggal paling akhir yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'maximum-days', type: 'number', description: 'Panjang rentang maksimal, dalam hari.' },
                { name: 'is-date-unavailable', type: '(date: string) => boolean', description: 'Tanggal yang tidak tersedia; tidak bisa dipilih maupun berada di dalam rentang.' },
                { name: 'number-of-months', type: 'number', default: '1', description: 'Jumlah bulan yang tampil berdampingan.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa nama bulan dan hari.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan seluruh kalender.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Kalender bisa dinavigasi, tetapi rentang tidak bisa diubah.' },
            ]"
        />
    </DocPage>
</template>
