<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import DateField from '@/components/ui/DateField.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const letterDate = ref('2026-11-23');
const receivedDate = ref('2026-11-20');
const dispositionDate = ref('');

const dispositionError = computed(() => (dispositionDate.value ? null : 'Tanggal disposisi wajib diisi.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import DateField from '@/components/ui/DateField.vue';

const letterDate = ref('2026-11-23');
<\/script>

<template>
    <DateField v-model="letterDate" label="Tanggal surat" />

    <p>Nilai v-model: {{ letterDate || '—' }}</p>
</template>
`;

const formatCode = `
<template>
    <DateField v-model="receivedDate" label="DD-MM-YYYY" format="DD-MM-YYYY" />
    <DateField v-model="receivedDate" label="YYYY-MM-DD" format="YYYY-MM-DD" />
    <DateField v-model="receivedDate" label="D/M/YYYY" format="D/M/YYYY" />

    <!-- Tampilan berbeda, nilai v-model tetap sama: '2026-11-20' -->
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import DateField from '@/components/ui/DateField.vue';

const form = useForm({ disposition_date: '' });
<\/script>

<template>
    <DateField
        v-model="form.disposition_date"
        label="Tanggal disposisi"
        name="disposition_date"
        min-date="2026-01-01"
        max-date="2026-12-31"
        hint="Hanya tanggal di tahun 2026."
        :error="form.errors.disposition_date"
    />
</template>

// app/Http/Requests/StoreDispositionRequest.php
public function rules(): array
{
    return [
        'disposition_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:2026-01-01', 'before_or_equal:2026-12-31'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Ketik angka per segmen, atau pakai panah atas/bawah untuk mengubah dan kiri/kanan untuk berpindah segmen. v-model terisi string YYYY-MM-DD setelah semua segmen lengkap."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <DateField v-model="letterDate" label="Tanggal surat" />

                <p class="text-sm text-muted">Nilai v-model: {{ letterDate || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Format tampilan"
            description="format hanya mengubah urutan dan pemisah segmen; v-model tetap YYYY-MM-DD."
            :code="formatCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-4">
                <DateField v-model="receivedDate" label="DD-MM-YYYY" format="DD-MM-YYYY" />
                <DateField v-model="receivedDate" label="YYYY-MM-DD" format="YYYY-MM-DD" />
                <DateField v-model="receivedDate" label="D/M/YYYY" format="D/M/YYYY" />

                <p class="text-sm text-muted">Nilai v-model: {{ receivedDate || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan useForm Inertia, hint, dan pesan error dari form.errors. Di server cukup validasi date_format:Y-m-d. Tanggal di luar batas ditandai ikon merah."
            :code="formCode"
        >
            <DateField
                v-model="dispositionDate"
                label="Tanggal disposisi"
                name="disposition_date"
                min-date="2026-01-01"
                max-date="2026-12-31"
                hint="Hanya tanggal di tahun 2026."
                :error="dispositionError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="DateField"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Tanggal, format YYYY-MM-DD (kosong sampai semua segmen terisi).' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'format', type: 'string', default: `'DD/MM/YYYY'`, description: 'Urutan dan pemisah segmen. Token: DD, D, MM, M, YYYY.' },
                { name: 'min-date', type: 'string', description: 'Tanggal paling awal yang valid, YYYY-MM-DD.' },
                { name: 'max-date', type: 'string', description: 'Tanggal paling akhir yang valid, YYYY-MM-DD.' },
                { name: 'name', type: 'string', description: 'Nama input tersembunyi (nilai YYYY-MM-DD) untuk submit form biasa.' },
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
