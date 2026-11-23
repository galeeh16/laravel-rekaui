<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';
import ColorSwatchPicker from '@/components/ui/ColorSwatchPicker.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const palette = [
    { value: '#17795a', label: 'Hijau brand' },
    { value: '#2d5bb0', label: 'Biru' },
    { value: '#7c3aed', label: 'Ungu' },
    { value: '#db2777', label: 'Merah muda' },
    { value: '#c03a26', label: 'Merah' },
    { value: '#d97706', label: 'Oranye' },
    { value: '#8a6410', label: 'Cokelat emas' },
    { value: '#171716', label: 'Hitam' },
];

const labelColors = ['#e2f3ee', '#e1e9f6', '#f6ecd2', '#fbe3de', '#f8e3d4', '#dfe4ec'];

const brandColor = ref('#17795a');
const categoryColors = ref(['#17795a', '#2d5bb0']);
const labelColor = ref('#e1e9f6');

const brandColorName = computed(() => palette.find((color) => color.value === brandColor.value)?.label ?? '—');

const basicCode = `
<script setup>
import { ref } from 'vue';
import ColorSwatchPicker from '@/components/ui/ColorSwatchPicker.vue';

const palette = [
    { value: '#17795a', label: 'Hijau brand' },
    { value: '#2d5bb0', label: 'Biru' },
    { value: '#7c3aed', label: 'Ungu' },
    // ...
];

const brandColor = ref('#17795a');
<\/script>

<template>
    <ColorSwatchPicker v-model="brandColor" :colors="palette" label="Warna brand" />

    <p>Dipilih: {{ brandColor }}</p>
</template>
`;

const multipleCode = `
<script setup>
import { ref } from 'vue';
import ColorSwatchPicker from '@/components/ui/ColorSwatchPicker.vue';

const palette = ['#17795a', '#2d5bb0', '#7c3aed', '#db2777', '#c03a26', '#d97706'];
const categoryColors = ref(['#17795a', '#2d5bb0']);
<\/script>

<template>
    <ColorSwatchPicker v-model="categoryColors" :colors="palette" multiple shape="square" />

    <p>Dipilih: {{ categoryColors.join(', ') }}</p>
</template>
`;

const sizesCode = `
<script setup>
import { ref } from 'vue';
import ColorSwatchPicker from '@/components/ui/ColorSwatchPicker.vue';

const palette = ['#17795a', '#2d5bb0', '#7c3aed', '#db2777', '#c03a26', '#d97706'];
const labelColor = ref('#e1e9f6');
<\/script>

<template>
    <ColorSwatchPicker
        v-model="labelColor"
        :colors="['#e2f3ee', '#e1e9f6', '#f6ecd2', '#fbe3de', '#f8e3d4', '#dfe4ec']"
        label="Warna label surat"
        size="lg"
        shape="square"
    />

    <ColorSwatchPicker model-value="#17795a" :colors="palette" size="sm" label="Nonaktif" disabled />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar + v-model"
            description="Klik swatch atau pakai tombol panah lalu Enter. v-model berisi kode hex dari warna yang dipilih."
            :code="basicCode"
        >
            <div class="flex flex-col gap-4">
                <ColorSwatchPicker v-model="brandColor" :colors="palette" label="Warna brand" />

                <p class="flex items-center gap-2 text-sm text-muted">
                    <ColorSwatch :color="brandColor" size="sm" />
                    Dipilih: <span class="font-medium text-ink tabular-nums">{{ brandColor || '—' }}</span>
                    <span>({{ brandColorName }})</span>
                </p>
            </div>
        </DocExample>

        <DocExample
            title="Banyak pilihan"
            description="Dengan multiple, v-model berupa array dan setiap klik menambah atau melepas warna."
            :code="multipleCode"
        >
            <div class="flex flex-col gap-4">
                <ColorSwatchPicker v-model="categoryColors" :colors="palette" multiple shape="square" aria-label="Warna kategori" />

                <p class="text-sm text-muted">
                    Dipilih: <span class="font-medium text-ink tabular-nums">{{ categoryColors.join(', ') || '—' }}</span>
                </p>
            </div>
        </DocExample>

        <DocExample title="Ukuran & nonaktif" description="Ukuran dan bentuk sama dengan ColorSwatch; disabled mengunci semua swatch." :code="sizesCode">
            <div class="flex flex-col gap-6">
                <ColorSwatchPicker v-model="labelColor" :colors="labelColors" label="Warna label surat" size="lg" shape="square" />
                <ColorSwatchPicker model-value="#17795a" :colors="palette" size="sm" label="Nonaktif" disabled />
            </div>
        </DocExample>

        <DocProps
            title="ColorSwatchPicker"
            :rows="[
                { name: 'v-model', type: 'string | string[]', description: 'Hex warna yang dipilih (array untuk multiple), selalu huruf kecil.' },
                { name: 'colors', type: 'string[] | { value, label }[]', description: 'Daftar warna; label opsional dipakai untuk tooltip dan screen reader.' },
                { name: 'multiple', type: 'boolean', default: 'false', description: 'Boleh memilih lebih dari satu warna.' },
                { name: 'label', type: 'string', description: 'Label grup di atas swatch.' },
                { name: 'size', type: `'sm' | 'md' | 'lg'`, default: `'md'`, description: 'Ukuran swatch.' },
                { name: 'shape', type: `'square' | 'circle'`, default: `'circle'`, description: 'Bentuk swatch.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua swatch.' },
            ]"
        />
    </DocPage>
</template>
