<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Slider from '@/components/ui/Slider.vue';

const compressionQuality = ref(80);
const committedQuality = ref(80);
const pageRange = ref([3, 12]);
const retentionMonths = ref(24);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Slider from '@/components/ui/Slider.vue';

const compressionQuality = ref(80);
const committedQuality = ref(80);
<\/script>

<template>
    <Slider
        v-model="compressionQuality"
        label="Kualitas kompresi PDF"
        show-value
        :format-value="(value) => value + '%'"
        hint="Semakin tinggi, semakin besar ukuran lampiran."
        @value-commit="(value) => (committedQuality = value)"
    />

    <p>v-model: {{ compressionQuality }} · disimpan: {{ committedQuality }}</p>
</template>
`;

const rangeCode = `
<script setup>
import { ref } from 'vue';
import Slider from '@/components/ui/Slider.vue';

const pageRange = ref([3, 12]);
<\/script>

<template>
    <Slider
        v-model="pageRange"
        label="Halaman yang dicetak"
        :min="1"
        :max="40"
        :min-steps-between-thumbs="1"
        show-value
    />

    <p>Cetak halaman {{ pageRange[0] }} sampai {{ pageRange[1] }}</p>
</template>
`;

const stepCode = `
<template>
    <Slider
        v-model="retentionMonths"
        label="Masa retensi arsip"
        :min="6"
        :max="120"
        :step="6"
        show-value
        :format-value="(value) => value + ' bulan'"
    />

    <Slider :model-value="60" label="Kuota penyimpanan (dikunci admin)" show-value disabled />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="v-model berupa angka. value-commit hanya terpanggil saat thumb dilepas atau tombol panah ditekan, cocok untuk menyimpan ke server."
            :code="basicCode"
        >
            <div class="flex w-full max-w-md flex-col gap-4">
                <Slider
                    v-model="compressionQuality"
                    label="Kualitas kompresi PDF"
                    show-value
                    :format-value="(value) => value + '%'"
                    hint="Semakin tinggi, semakin besar ukuran lampiran."
                    @value-commit="(value) => (committedQuality = value)"
                />

                <p class="text-sm text-muted">v-model: {{ compressionQuality }} · disimpan: {{ committedQuality }}</p>
            </div>
        </DocExample>

        <DocExample title="Rentang" description="Berikan array dua angka untuk dua thumb; min-steps-between-thumbs menjaga jaraknya." :code="rangeCode">
            <div class="flex w-full max-w-md flex-col gap-4">
                <Slider v-model="pageRange" label="Halaman yang dicetak" :min="1" :max="40" :min-steps-between-thumbs="1" show-value />

                <p class="text-sm text-muted">Cetak halaman {{ pageRange[0] }} sampai {{ pageRange[1] }}</p>
            </div>
        </DocExample>

        <DocExample title="Step & nonaktif" description="Nilai melompat per step; Shift + panah atau Page Up/Down melompat 10 step." :code="stepCode">
            <div class="flex w-full max-w-md flex-col gap-8">
                <Slider
                    v-model="retentionMonths"
                    label="Masa retensi arsip"
                    :min="6"
                    :max="120"
                    :step="6"
                    show-value
                    :format-value="(value) => value + ' bulan'"
                />

                <Slider :model-value="60" label="Kuota penyimpanan (dikunci admin)" show-value disabled />
            </div>
        </DocExample>

        <DocProps
            title="Slider"
            :rows="[
                { name: 'v-model', type: 'number | number[]', description: 'Angka untuk satu thumb, array untuk rentang (satu thumb per nilai).' },
                { name: 'min', type: 'number', default: '0', description: 'Nilai terkecil.' },
                { name: 'max', type: 'number', default: '100', description: 'Nilai terbesar.' },
                { name: 'step', type: 'number', default: '1', description: 'Besar lompatan nilai.' },
                { name: 'min-steps-between-thumbs', type: 'number', default: '0', description: 'Jarak minimal (dalam step) antar thumb pada mode rentang.' },
                { name: 'orientation', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Arah slider; vertikal setinggi 11rem.' },
                { name: 'label', type: 'string', description: 'Label di atas slider, juga dipakai sebagai nama thumb.' },
                { name: 'show-value', type: 'boolean', default: 'false', description: 'Tampilkan nilai saat ini di sebelah label.' },
                { name: 'format-value', type: '(value: number) => string', description: 'Format teks nilai, juga untuk aria-valuetext.' },
                { name: 'name', type: 'string', description: 'Nama field tersembunyi untuk form HTML biasa.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan slider.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah slider.' },
                { name: '@value-commit', type: '(value: number | number[]) => void', description: 'Nilai akhir setelah thumb dilepas.' },
            ]"
        />
    </DocPage>
</template>
