<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import DatePicker from '@/components/ui/DatePicker.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const letterDate = ref('2026-11-23');
const deadline = ref('');
const meetingDate = ref('');
const sentDate = ref('');

const sentDateError = computed(() => (sentDate.value ? null : 'Tanggal kirim wajib diisi.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import DatePicker from '@/components/ui/DatePicker.vue';

const letterDate = ref('2026-11-23');
<\/script>

<template>
    <DatePicker v-model="letterDate" label="Tanggal surat" />

    <p>Nilai v-model: {{ letterDate || '—' }}</p>
</template>
`;

const optionsCode = `
<template>
    <!-- Hanya hari ini sampai akhir 2026, ditampilkan dengan tanda hubung -->
    <DatePicker
        v-model="deadline"
        label="Batas waktu balasan"
        format="DD-MM-YYYY"
        min-date="2026-11-23"
        max-date="2026-12-31"
    />

    <!-- Kalender berbahasa Inggris, urutan bulan/hari/tahun -->
    <DatePicker v-model="meetingDate" label="Meeting date" format="MM/DD/YYYY" locale="en-US" />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import DatePicker from '@/components/ui/DatePicker.vue';

const form = useForm({ sent_date: '' });
<\/script>

<template>
    <DatePicker
        v-model="form.sent_date"
        label="Tanggal kirim"
        name="sent_date"
        max-date="2026-11-23"
        hint="Tidak boleh setelah hari ini."
        :error="form.errors.sent_date"
    />
</template>

// app/Http/Requests/StoreLetterRequest.php
public function rules(): array
{
    return [
        'sent_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Ketik tanggal per segmen atau klik ikon kalender. Panah ganda di kalender berpindah tahun, panah tunggal berpindah bulan. v-model berupa string YYYY-MM-DD."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <DatePicker v-model="letterDate" label="Tanggal surat" />

                <p class="text-sm text-muted">Nilai v-model: {{ letterDate || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Format, batas & bahasa"
            description="format mengatur tampilan field (v-model tetap YYYY-MM-DD), min-date/max-date membatasi pilihan, locale mengganti bahasa kalender."
            :code="optionsCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-4">
                <DatePicker v-model="deadline" label="Batas waktu balasan" format="DD-MM-YYYY" min-date="2026-11-23" max-date="2026-12-31" />
                <DatePicker v-model="meetingDate" label="Meeting date" format="MM/DD/YYYY" locale="en-US" />

                <p class="text-sm text-muted">Nilai v-model: {{ deadline || '—' }} · {{ meetingDate || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan useForm Inertia, hint, dan pesan error dari form.errors. Karena nilainya selalu YYYY-MM-DD, di server cukup date_format:Y-m-d."
            :code="formCode"
        >
            <DatePicker
                v-model="sentDate"
                label="Tanggal kirim"
                name="sent_date"
                max-date="2026-11-23"
                hint="Tidak boleh setelah hari ini."
                :error="sentDateError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="DatePicker"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Tanggal, format YYYY-MM-DD (kosong jika belum dipilih).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'format', type: 'string', default: `'DD/MM/YYYY'`, description: 'Urutan dan pemisah segmen. Token: DD, D, MM, M, YYYY.' },
                { name: 'min-date', type: 'string', description: 'Tanggal paling awal yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'max-date', type: 'string', description: 'Tanggal paling akhir yang bisa dipilih, YYYY-MM-DD.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa kalender (nama bulan/hari) dan placeholder segmen.' },
                { name: 'name', type: 'string', description: 'Nama input tersembunyi (nilai YYYY-MM-DD) untuk submit form biasa.' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field dan kalender.' },
            ]"
        />
    </DocPage>
</template>
