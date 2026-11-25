<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import TimeRangeField from '@/components/ui/TimeRangeField.vue';

const meetingTime = ref({ start: '09:00', end: '10:30' });
const serviceHours = ref({ start: '08:00', end: '15:00' });
const agendaTime = ref({ start: '', end: '' });

const agendaTimeError = computed(() => (agendaTime.value.start && agendaTime.value.end ? null : 'Jam mulai dan selesai wajib diisi.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import TimeRangeField from '@/components/ui/TimeRangeField.vue';

const meetingTime = ref({ start: '09:00', end: '10:30' });
<\/script>

<template>
    <TimeRangeField v-model="meetingTime" label="Jam rapat" />

    <p>Mulai: {{ meetingTime.start || '—' }} · Selesai: {{ meetingTime.end || '—' }}</p>
</template>
`;

const limitsCode = `
<template>
    <TimeRangeField
        v-model="serviceHours"
        label="Jam layanan loket"
        min-time="07:00"
        max-time="17:00"
        :minute-step="30"
    />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import TimeRangeField from '@/components/ui/TimeRangeField.vue';

const form = useForm({ agenda_time: { start: '', end: '' } });
<\/script>

<template>
    <TimeRangeField
        v-model="form.agenda_time"
        label="Waktu agenda"
        name="agenda_time"
        hint="Jam selesai harus setelah jam mulai."
        :error="form.errors['agenda_time.start'] || form.errors['agenda_time.end']"
    />
</template>

// app/Http/Requests/StoreAgendaRequest.php
public function rules(): array
{
    return [
        'agenda_time.start' => ['required', 'date_format:H:i'],
        'agenda_time.end' => ['required', 'date_format:H:i', 'after:agenda_time.start'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Jam mulai dan selesai dalam satu field. v-model berupa { start, end } string HH:mm."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <TimeRangeField v-model="meetingTime" label="Jam rapat" />

                <p class="text-sm text-muted">Mulai: {{ meetingTime.start || '—' }} · Selesai: {{ meetingTime.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Batas jam & langkah 30 menit"
            description="Waktu di luar min-time/max-time, atau jam selesai sebelum jam mulai, ditandai ikon merah. minute-step mengatur lompatan panah atas/bawah."
            :code="limitsCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <TimeRangeField v-model="serviceHours" label="Jam layanan loket" min-time="07:00" max-time="17:00" :minute-step="30" />

                <p class="text-sm text-muted">Nilai v-model: {{ serviceHours.start || '—' }} – {{ serviceHours.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan name, field merender input tersembunyi agenda_time[start] dan agenda_time[end]. Di server validasi dengan date_format:H:i."
            :code="formCode"
        >
            <TimeRangeField
                v-model="agendaTime"
                label="Waktu agenda"
                name="agenda_time"
                hint="Jam selesai harus setelah jam mulai."
                :error="agendaTimeError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="TimeRangeField"
            :rows="[
                { name: 'v-model', type: '{ start: string, end: string }', default: `{ start: '', end: '' }`, description: 'Rentang waktu, tiap waktu HH:mm (HH:mm:ss untuk granularity second).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'min-time', type: 'string', description: 'Waktu paling awal yang valid, HH:mm.' },
                { name: 'max-time', type: 'string', description: 'Waktu paling akhir yang valid, HH:mm.' },
                { name: 'granularity', type: `'hour' | 'minute' | 'second'`, default: `'minute'`, description: 'Segmen terkecil yang bisa diubah.' },
                { name: 'hour-cycle', type: '12 | 24', default: '24', description: 'Tampilan 12 atau 24 jam (v-model tetap 24 jam).' },
                { name: 'minute-step', type: 'number', default: '1', description: 'Lompatan menit saat menekan panah atas/bawah.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa pemisah dan placeholder segmen.' },
                { name: 'name', type: 'string', description: 'Nama dasar input tersembunyi: name[start] dan name[end].' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Field hanya bisa dibaca.' },
            ]"
        />
    </DocPage>
</template>
