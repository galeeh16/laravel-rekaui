<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Rating from '@/components/ui/Rating.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const serviceScore = ref(4);
const draftScore = ref(3.5);

const scoreLabels = ['Belum dinilai', 'Sangat buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat baik'];

const couriers = [
    { name: 'Ekspedisi Nusantara', score: 4.5, reviews: 128 },
    { name: 'Kurir Internal', score: 4, reviews: 342 },
    { name: 'Pos Kilat', score: 3.5, reviews: 57 },
];

const basicCode = `
<script setup>
import { ref } from 'vue';
import Rating from '@/components/ui/Rating.vue';

const serviceScore = ref(4);
<\/script>

<template>
    <Rating v-model="serviceScore" label="Kepuasan layanan persuratan" />

    <p>Nilai: {{ serviceScore }} dari 5</p>
</template>
`;

const halfCode = `
<script setup>
import { ref } from 'vue';
import Rating from '@/components/ui/Rating.vue';

const draftScore = ref(3.5);
<\/script>

<template>
    <Rating
        v-model="draftScore"
        label="Kualitas draf surat"
        :step="0.5"
        size="lg"
        clearable
        hint="Klik nilai yang sama sekali lagi untuk menghapus penilaian."
    />

    <p>Nilai: {{ draftScore }}</p>
</template>
`;

const readonlyCode = `
<template>
    <div v-for="courier in couriers" :key="courier.name">
        <span>{{ courier.name }}</span>
        <Rating :model-value="courier.score" :step="0.5" size="sm" readonly />
        <span>{{ courier.score }} ({{ courier.reviews }} ulasan)</span>
    </div>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar + v-model"
            description="Arahkan kursor untuk pratinjau, klik untuk memilih. Panah kiri/kanan juga mengubah nilai."
            :code="basicCode"
        >
            <div class="flex flex-col gap-3">
                <Rating v-model="serviceScore" label="Kepuasan layanan persuratan" />

                <p class="text-sm text-muted">Nilai: {{ serviceScore }} dari 5 · {{ scoreLabels[Math.round(serviceScore)] }}</p>
            </div>
        </DocExample>

        <DocExample title="Setengah bintang & clearable" description="step 0.5 membagi setiap ikon menjadi dua bagian." :code="halfCode">
            <div class="flex flex-col gap-3">
                <Rating
                    v-model="draftScore"
                    label="Kualitas draf surat"
                    :step="0.5"
                    size="lg"
                    clearable
                    hint="Klik nilai yang sama sekali lagi untuk menghapus penilaian."
                />

                <p class="text-sm text-muted">Nilai: {{ draftScore }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Hanya baca"
            description="readonly untuk menampilkan nilai rata-rata, tanpa tampilan pudar seperti disabled."
            :code="readonlyCode"
        >
            <ul class="flex w-full max-w-md flex-col divide-y divide-line rounded-xl border border-line">
                <li v-for="courier in couriers" :key="courier.name" class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                    <span class="text-sm font-medium">{{ courier.name }}</span>

                    <div class="flex items-center gap-2">
                        <Rating :model-value="courier.score" :step="0.5" size="sm" readonly />
                        <span class="text-sm text-muted tabular-nums">{{ courier.score }} ({{ courier.reviews }})</span>
                    </div>
                </li>
            </ul>
        </DocExample>

        <DocProps
            title="Rating"
            :rows="[
                { name: 'v-model', type: 'number', default: '0', description: 'Nilai terpilih; 0 berarti belum dinilai.' },
                { name: 'length', type: 'number', default: '5', description: 'Jumlah ikon.' },
                { name: 'step', type: 'number', default: '1', description: 'Perubahan terkecil, mis. 0.5 untuk setengah bintang.' },
                { name: 'clearable', type: 'boolean', default: 'false', description: 'Klik nilai yang sama untuk mengembalikan ke 0.' },
                { name: 'hoverable', type: 'boolean', default: 'true', description: 'Pratinjau nilai saat kursor di atas ikon.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Hanya tampilan, tanpa efek pudar.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan dan pudarkan rating.' },
                { name: 'size', type: `'sm' | 'md' | 'lg'`, default: `'md'`, description: 'Ukuran ikon: 16, 24 atau 32px.' },
                { name: 'icon', type: 'string', default: `'star'`, description: 'Nama ikon, mis. heart.' },
                { name: 'label', type: 'string', description: 'Label di atas rating (aria-labelledby).' },
                { name: 'name', type: 'string', description: 'Nama field untuk form HTML biasa.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah rating.' },
            ]"
        />
    </DocPage>
</template>
