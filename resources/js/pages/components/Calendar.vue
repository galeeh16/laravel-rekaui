<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Calendar from '@/components/ui/Calendar.vue';

const meetingDate = ref('2026-11-25');
const agendaDate = ref('');
const handoverDate = ref('2026-12-02');

/** Example holidays (Christmas and the collective leave around it). */
const holidays = ['2026-12-24', '2026-12-25', '2026-12-26'];

const basicCode = `
<script setup>
import { ref } from 'vue';
import Calendar from '@/components/ui/Calendar.vue';

const meetingDate = ref('2026-11-25');
<\/script>

<template>
    <Calendar v-model="meetingDate" />

    <p>Tanggal rapat: {{ meetingDate || '—' }}</p>
</template>

<!--
    v-model selalu string YYYY-MM-DD, jadi langsung cocok dengan validasi Laravel:
    'meeting_date' => ['required', 'date_format:Y-m-d'],
-->
`;

const limitsCode = `
<script setup>
import { ref } from 'vue';
import Calendar from '@/components/ui/Calendar.vue';

const agendaDate = ref('');
const holidays = ['2026-12-24', '2026-12-25', '2026-12-26'];
<\/script>

<template>
    <Calendar
        v-model="agendaDate"
        min-date="2026-11-23"
        max-date="2027-01-31"
        :is-date-unavailable="(date) => holidays.includes(date)"
    />
</template>

<!--
    Tetap validasi ulang di server:
    'agenda_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:2026-11-23', 'before_or_equal:2027-01-31'],
-->
`;

const monthsCode = `
<script setup>
import { ref } from 'vue';
import Calendar from '@/components/ui/Calendar.vue';

const handoverDate = ref('2026-12-02');
<\/script>

<template>
    <Calendar v-model="handoverDate" :number-of-months="2" />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik tanggal, atau fokus ke kalender lalu pakai tombol panah, Page Up/Down, Home/End. v-model berupa string YYYY-MM-DD."
            :code="basicCode"
        >
            <div class="flex flex-col items-center gap-3">
                <Calendar v-model="meetingDate" />

                <p class="text-sm text-muted">Tanggal rapat: {{ meetingDate || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Batas tanggal & hari libur"
            description="Tanggal di luar min-date/max-date nonaktif. Tanggal dari is-date-unavailable (menerima string YYYY-MM-DD) dicoret dan tidak bisa dipilih."
            :code="limitsCode"
        >
            <div class="flex flex-col items-center gap-3">
                <Calendar
                    v-model="agendaDate"
                    min-date="2026-11-23"
                    max-date="2027-01-31"
                    :is-date-unavailable="(date) => holidays.includes(date)"
                />

                <p class="text-sm text-muted">Tanggal agenda: {{ agendaDate || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Dua bulan"
            description="number-of-months menampilkan beberapa bulan berdampingan (bertumpuk di layar kecil)."
            :code="monthsCode"
        >
            <Calendar v-model="handoverDate" :number-of-months="2" />
        </DocExample>

        <DocProps
            title="Calendar"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Tanggal terpilih, format YYYY-MM-DD (kosong jika belum dipilih).' },
                { name: 'min-date', type: 'string', description: 'Tanggal paling awal yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'max-date', type: 'string', description: 'Tanggal paling akhir yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'is-date-unavailable', type: '(date: string) => boolean', description: 'Tanggal yang tidak tersedia (mis. hari libur); dicoret dan tidak bisa dipilih.' },
                { name: 'number-of-months', type: 'number', default: '1', description: 'Jumlah bulan yang tampil berdampingan.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa nama bulan dan hari.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan seluruh kalender.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Kalender bisa dinavigasi, tetapi tanggal tidak bisa diubah.' },
            ]"
        />
    </DocPage>
</template>
