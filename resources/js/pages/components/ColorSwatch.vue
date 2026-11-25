<script setup>
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const sizes = ['sm', 'md', 'lg'];

/** Light-mode values of the design tokens in resources/css/app.css. */
const tokens = [
    { name: 'brand', value: '#17795a', label: 'Hijau brand' },
    { name: 'brand-hover', value: '#136349', label: 'Hijau brand (hover)' },
    { name: 'brand-soft', value: '#e2f3ee', label: 'Hijau brand lembut' },
    { name: 'brand-ink', value: '#0f5c44', label: 'Teks brand' },
    { name: 'info', value: '#2d5bb0', label: 'Biru info' },
    { name: 'warning', value: '#8a6410', label: 'Kuning peringatan' },
    { name: 'danger', value: '#c03a26', label: 'Merah bahaya' },
    { name: 'ink', value: '#171716', label: 'Teks utama' },
];

const transparencies = [
    { value: '#17795a', caption: '100%' },
    { value: '#17795a99', caption: '60%' },
    { value: '#17795a33', caption: '20%' },
    { value: '', caption: 'Kosong' },
];

const sizesCode = `
<script setup>
import ColorSwatch from '@/components/ui/ColorSwatch.vue';
<\/script>

<template>
    <ColorSwatch color="#17795a" size="sm" />
    <ColorSwatch color="#17795a" />
    <ColorSwatch color="#17795a" size="lg" />

    <ColorSwatch color="#17795a" size="sm" shape="circle" />
    <ColorSwatch color="#17795a" shape="circle" />
    <ColorSwatch color="#17795a" size="lg" shape="circle" />
</template>
`;

const paletteCode = `
<script setup>
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const tokens = [
    { name: 'brand', value: '#17795a', label: 'Hijau brand' },
    { name: 'brand-soft', value: '#e2f3ee', label: 'Hijau brand lembut' },
    // ...
];
<\/script>

<template>
    <div v-for="token in tokens" :key="token.name" class="flex items-center gap-3">
        <ColorSwatch :color="token.value" :label="token.label" size="lg" />

        <div>
            <p class="font-medium">{{ token.name }}</p>
            <p class="text-muted">{{ token.value }}</p>
        </div>
    </div>
</template>
`;

const transparencyCode = `
<template>
    <ColorSwatch color="#17795a" size="lg" />
    <ColorSwatch color="#17795a99" size="lg" />
    <ColorSwatch color="#17795a33" size="lg" />
    <ColorSwatch color="" size="lg" />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Ukuran & bentuk" description="Tiga ukuran (sm, md, lg) dalam bentuk kotak atau lingkaran." :code="sizesCode">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-4">
                    <ColorSwatch v-for="size in sizes" :key="size" color="#17795a" :size="size" />
                </div>

                <div class="flex items-center gap-4">
                    <ColorSwatch v-for="size in sizes" :key="size" color="#17795a" :size="size" shape="circle" />
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Palet token"
            description="Menampilkan palet warna; prop label menjadi nama yang dibacakan screen reader (default: nama warna otomatis dari reka-ui)."
            :code="paletteCode"
        >
            <div class="grid w-full max-w-2xl grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-4">
                <div v-for="token in tokens" :key="token.name" class="flex items-center gap-3">
                    <ColorSwatch :color="token.value" :label="token.label" size="lg" />

                    <div class="min-w-0 text-sm">
                        <p class="truncate font-medium text-ink">{{ token.name }}</p>
                        <p class="text-muted tabular-nums">{{ token.value }}</p>
                    </div>
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Transparan & kosong"
            description="Warna dengan alpha digambar di atas papan catur; warna kosong ditandai garis diagonal."
            :code="transparencyCode"
        >
            <div class="flex items-start gap-5">
                <div v-for="item in transparencies" :key="item.caption" class="flex flex-col items-center gap-2">
                    <ColorSwatch :color="item.value" size="lg" />
                    <span class="text-xs text-muted">{{ item.caption }}</span>
                </div>
            </div>
        </DocExample>

        <DocProps
            title="ColorSwatch"
            :rows="[
                { name: 'color', type: 'string', default: `''`, description: 'Warna dalam format hex (#rgb, #rrggbb, #rrggbbaa), rgb(), hsl(), atau hsb().' },
                { name: 'label', type: 'string', description: 'Nama aksesibel; default nama warna otomatis dari reka-ui.' },
                { name: 'size', type: `'sm' | 'md' | 'lg'`, default: `'md'`, description: 'Ukuran swatch: 20, 32, atau 48 px.' },
                { name: 'shape', type: `'square' | 'circle'`, default: `'square'`, description: 'Bentuk swatch.' },
            ]"
        />
    </DocPage>
</template>
