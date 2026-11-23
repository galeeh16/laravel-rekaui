<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import MonthPicker from '@/components/ui/MonthPicker.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const reportMonth = ref('2026-11');
const recapMonth = ref('2026-10');
const payrollMonth = ref('');

const payrollMonthError = computed(() => (payrollMonth.value ? null : 'Bulan rekap wajib dipilih.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import MonthPicker from '@/components/ui/MonthPicker.vue';

const reportMonth = ref('2026-11');
<\/script>

<template>
    <MonthPicker v-model="reportMonth" label="Periode laporan" />

    <p>Nilai v-model: {{ reportMonth || '—' }}</p>
</template>
`;

const inlineCode = `
<template>
    <MonthPicker v-model="recapMonth" inline min-month="2026-01" max-month="2026-11" />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import MonthPicker from '@/components/ui/MonthPicker.vue';

const form = useForm({ recap_month: '' });
<\/script>

<template>
    <MonthPicker
        v-model="form.recap_month"
        label="Bulan rekap surat"
        name="recap_month"
        max-month="2026-11"
        hint="Bulan berjalan atau sebelumnya."
        :error="form.errors.recap_month"
    />
</template>

// app/Http/Requests/RecapRequest.php
public function rules(): array
{
    return [
        'recap_month' => ['required', 'date_format:Y-m'],
    ];
}

// Di controller: Carbon::createFromFormat('Y-m', $request->recap_month)->startOfMonth()
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik field untuk membuka grid dua belas bulan; panah di kiri/kanan berpindah tahun. Popover menutup setelah bulan dipilih. v-model berupa string YYYY-MM."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <MonthPicker v-model="reportMonth" label="Periode laporan" />

                <p class="text-sm text-muted">Nilai v-model: {{ reportMonth || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Inline + batas bulan"
            description="Dengan inline, grid tampil langsung tanpa popover. Bulan di luar min-month/max-month nonaktif."
            :code="inlineCode"
        >
            <div class="flex flex-col items-center gap-3">
                <MonthPicker v-model="recapMonth" inline min-month="2026-01" max-month="2026-11" />

                <p class="text-sm text-muted">Nilai v-model: {{ recapMonth || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan useForm Inertia dan pesan error dari form.errors. Nilai YYYY-MM cocok dengan date_format:Y-m."
            :code="formCode"
        >
            <MonthPicker
                v-model="payrollMonth"
                label="Bulan rekap surat"
                name="recap_month"
                max-month="2026-11"
                hint="Bulan berjalan atau sebelumnya."
                :error="payrollMonthError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="MonthPicker"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Bulan terpilih, format YYYY-MM (kosong jika belum dipilih).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'min-month', type: 'string', description: 'Bulan paling awal yang bisa dipilih, YYYY-MM.' },
                { name: 'max-month', type: 'string', description: 'Bulan paling akhir yang bisa dipilih, YYYY-MM.' },
                { name: 'placeholder', type: 'string', default: `'Pilih bulan'`, description: 'Teks saat belum ada bulan terpilih.' },
                { name: 'inline', type: 'boolean', default: 'false', description: 'Tampilkan grid langsung, tanpa field dan popover.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa nama bulan.' },
                { name: 'name', type: 'string', description: 'Nama input tersembunyi berisi nilai v-model, untuk submit form biasa.' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
            ]"
        />
    </DocPage>
</template>
