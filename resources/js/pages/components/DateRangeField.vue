<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import DateRangeField from '@/components/ui/DateRangeField.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const assignment = ref({ start: '2026-11-23', end: '2026-11-27' });
const archivePeriod = ref({ start: '2026-01-01', end: '2026-06-30' });
const reportPeriod = ref({ start: '', end: '' });

const reportPeriodError = computed(() => (reportPeriod.value.start && reportPeriod.value.end ? null : 'Periode laporan wajib diisi lengkap.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import DateRangeField from '@/components/ui/DateRangeField.vue';

const assignment = ref({ start: '2026-11-23', end: '2026-11-27' });
<\/script>

<template>
    <DateRangeField v-model="assignment" label="Masa tugas" />

    <p>Mulai: {{ assignment.start || '—' }} · Selesai: {{ assignment.end || '—' }}</p>
</template>
`;

const formatCode = `
<template>
    <DateRangeField
        v-model="archivePeriod"
        label="Periode arsip"
        format="DD-MM-YYYY"
        min-date="2020-01-01"
        max-date="2026-12-31"
    />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import DateRangeField from '@/components/ui/DateRangeField.vue';

const form = useForm({ period: { start: '', end: '' } });
<\/script>

<template>
    <DateRangeField
        v-model="form.period"
        label="Periode laporan"
        name="period"
        hint="Tanggal selesai tidak boleh sebelum tanggal mulai."
        :error="form.errors['period.start'] || form.errors['period.end']"
    />
</template>

// app/Http/Requests/StoreReportRequest.php
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
            description="Dua tanggal dalam satu field; tab atau panah kanan berpindah dari tanggal mulai ke tanggal selesai. v-model berupa { start, end } string YYYY-MM-DD."
            :code="basicCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <DateRangeField v-model="assignment" label="Masa tugas" />

                <p class="text-sm text-muted">Mulai: {{ assignment.start || '—' }} · Selesai: {{ assignment.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Format & batas tanggal"
            description="format berlaku untuk kedua tanggal. Tanggal di luar batas, atau tanggal selesai sebelum tanggal mulai, ditandai ikon merah."
            :code="formatCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <DateRangeField v-model="archivePeriod" label="Periode arsip" format="DD-MM-YYYY" min-date="2020-01-01" max-date="2026-12-31" />

                <p class="text-sm text-muted">Nilai v-model: {{ archivePeriod.start || '—' }} s.d. {{ archivePeriod.end || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan name, field merender input tersembunyi period[start] dan period[end]. Di server validasi tiap tanggal dengan date_format:Y-m-d."
            :code="formCode"
        >
            <DateRangeField
                v-model="reportPeriod"
                label="Periode laporan"
                name="period"
                hint="Tanggal selesai tidak boleh sebelum tanggal mulai."
                :error="reportPeriodError"
                class="w-full max-w-sm"
            />
        </DocExample>

        <DocProps
            title="DateRangeField"
            :rows="[
                { name: 'v-model', type: '{ start: string, end: string }', default: `{ start: '', end: '' }`, description: 'Rentang, tiap tanggal YYYY-MM-DD (string kosong sampai segmennya lengkap).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'format', type: 'string', default: `'DD/MM/YYYY'`, description: 'Urutan dan pemisah segmen kedua tanggal. Token: DD, D, MM, M, YYYY.' },
                { name: 'min-date', type: 'string', description: 'Tanggal paling awal yang valid, YYYY-MM-DD.' },
                { name: 'max-date', type: 'string', description: 'Tanggal paling akhir yang valid, YYYY-MM-DD.' },
                { name: 'name', type: 'string', description: 'Nama dasar input tersembunyi: name[start] dan name[end].' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa placeholder segmen kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Field hanya bisa dibaca.' },
            ]"
        />
    </DocPage>
</template>
