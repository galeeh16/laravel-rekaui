<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import TimeField from '@/components/ui/TimeField.vue';

const meetingTime = ref('09:30');
const serviceTime = ref('');
const callTime = ref('14:00');
const sentTime = ref('08:15:30');
const deliveryTime = ref('');

const deliveryTimeError = computed(() => (deliveryTime.value ? null : 'Jam pengiriman wajib diisi.'));

const basicCode = `
<script setup>
import { ref } from 'vue';
import TimeField from '@/components/ui/TimeField.vue';

const meetingTime = ref('09:30');
<\/script>

<template>
    <TimeField v-model="meetingTime" label="Jam rapat" />

    <p>Nilai v-model: {{ meetingTime || '—' }}</p>
</template>
`;

const optionsCode = `
<template>
    <!-- Jam layanan 08.00–16.00, panah atas/bawah melompat 15 menit -->
    <TimeField v-model="serviceTime" label="Jam layanan" min-time="08:00" max-time="16:00" :minute-step="15" />

    <!-- Tampilan 12 jam; v-model tetap 24 jam: '14:00' -->
    <TimeField v-model="callTime" label="Call time" :hour-cycle="12" locale="en-US" />

    <!-- Dengan detik; v-model menjadi 'HH:mm:ss' -->
    <TimeField v-model="sentTime" label="Waktu kirim" granularity="second" />
</template>
`;

const formCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import TimeField from '@/components/ui/TimeField.vue';

const form = useForm({ delivery_time: '' });
<\/script>

<template>
    <TimeField
        v-model="form.delivery_time"
        label="Jam pengiriman"
        name="delivery_time"
        hint="Format 24 jam."
        :error="form.errors.delivery_time"
    />
</template>

// app/Http/Requests/StoreDeliveryRequest.php
public function rules(): array
{
    return [
        'delivery_time' => ['required', 'date_format:H:i'],
    ];
}
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Ketik jam dan menit, atau pakai panah atas/bawah. Tampilan mengikuti locale (09.30 untuk id-ID), v-model selalu HH:mm."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <TimeField v-model="meetingTime" label="Jam rapat" />

                <p class="text-sm text-muted">Nilai v-model: {{ meetingTime || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Batas, langkah, 12 jam & detik"
            description="min-time/max-time menandai jam di luar batas (ikon merah), minute-step mengatur lompatan panah, hour-cycle dan granularity hanya mengubah tampilan."
            :code="optionsCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-4">
                <TimeField v-model="serviceTime" label="Jam layanan" min-time="08:00" max-time="16:00" :minute-step="15" />
                <TimeField v-model="callTime" label="Call time" :hour-cycle="12" locale="en-US" />
                <TimeField v-model="sentTime" label="Waktu kirim" granularity="second" />

                <p class="text-sm text-muted">Nilai v-model: {{ serviceTime || '—' }} · {{ callTime || '—' }} · {{ sentTime || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Form + validasi Laravel"
            description="Dengan useForm Inertia dan pesan error dari form.errors. Nilai HH:mm cocok dengan date_format:H:i (pakai H:i:s untuk granularity second)."
            :code="formCode"
        >
            <TimeField
                v-model="deliveryTime"
                label="Jam pengiriman"
                name="delivery_time"
                hint="Format 24 jam."
                :error="deliveryTimeError"
                class="w-full max-w-xs"
            />
        </DocExample>

        <DocProps
            title="TimeField"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Waktu HH:mm (HH:mm:ss untuk granularity second), kosong sampai semua segmen terisi.' },
                { name: 'label', type: 'string', description: 'Label di atas field.' },
                { name: 'min-time', type: 'string', description: 'Waktu paling awal yang valid, HH:mm.' },
                { name: 'max-time', type: 'string', description: 'Waktu paling akhir yang valid, HH:mm.' },
                { name: 'granularity', type: `'hour' | 'minute' | 'second'`, default: `'minute'`, description: 'Segmen terkecil yang bisa diubah.' },
                { name: 'hour-cycle', type: '12 | 24', default: '24', description: 'Tampilan 12 atau 24 jam (v-model tetap 24 jam).' },
                { name: 'minute-step', type: 'number', default: '1', description: 'Lompatan menit saat menekan panah atas/bawah.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Bahasa pemisah dan placeholder segmen.' },
                { name: 'name', type: 'string', description: 'Nama input tersembunyi berisi nilai v-model, untuk submit form biasa.' },
                { name: 'id', type: 'string', description: 'Id field; otomatis jika kosong.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah field.' },
                { name: 'error', type: 'string', description: 'Pesan error; menggantikan hint dan memberi border merah.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan field.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Field hanya bisa dibaca.' },
            ]"
        />
    </DocPage>
</template>
