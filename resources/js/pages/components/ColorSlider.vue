<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import ColorSlider from '@/components/ui/ColorSlider.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const hueColor = ref('#17795a');
const hslColor = ref('#17795a');
const alphaColor = ref('#17795a');

const hueCode = `
<script setup>
import { ref } from 'vue';
import ColorSlider from '@/components/ui/ColorSlider.vue';

const color = ref('#17795a');
<\/script>

<template>
    <ColorSlider v-model="color" channel="hue" label="Hue" />

    <p>Nilai: {{ color }}</p>
</template>
`;

const hslCode = `
<script setup>
import { ref } from 'vue';
import ColorSlider from '@/components/ui/ColorSlider.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const color = ref('#17795a');
<\/script>

<template>
    <ColorSlider v-model="color" channel="hue" label="Hue" />
    <ColorSlider v-model="color" channel="saturation" label="Saturation" />
    <ColorSlider v-model="color" channel="lightness" label="Lightness" />

    <ColorSwatch :color="color" size="lg" />
</template>
`;

const alphaCode = `
<script setup>
import { ref } from 'vue';
import ColorSlider from '@/components/ui/ColorSlider.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const color = ref('#17795a');
<\/script>

<template>
    <ColorSlider v-model="color" channel="alpha" label="Opasitas" />

    <ColorSwatch :color="color" size="lg" />
    <p>Nilai: {{ color }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Hue + v-model"
            description="Geser atau pakai tombol panah; Shift, Page Up, dan Page Down melompat 10 langkah. Label menampilkan nilai kanal saat ini."
            :code="hueCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-4">
                <ColorSlider v-model="hueColor" channel="hue" label="Hue" />

                <p class="flex items-center gap-2 text-sm text-muted">
                    <ColorSwatch :color="hueColor" size="sm" />
                    Nilai: <span class="font-medium text-ink tabular-nums">{{ hueColor }}</span>
                </p>
            </div>
        </DocExample>

        <DocExample
            title="Kanal HSL"
            description="Beberapa slider dengan satu v-model. Gradien tiap track ikut berubah mengikuti warna yang sedang dipilih."
            :code="hslCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-5">
                <ColorSlider v-model="hslColor" channel="hue" label="Hue" />
                <ColorSlider v-model="hslColor" channel="saturation" label="Saturation" />
                <ColorSlider v-model="hslColor" channel="lightness" label="Lightness" />

                <div class="flex items-center gap-3 border-t border-line pt-5">
                    <ColorSwatch :color="hslColor" size="lg" />
                    <span class="text-sm font-medium text-ink tabular-nums">{{ hslColor }}</span>
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Alpha"
            description="Kanal alpha digambar di atas papan catur; nilainya menjadi hex 8 digit, misalnya #17795a80."
            :code="alphaCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-4">
                <ColorSlider v-model="alphaColor" channel="alpha" label="Opasitas" />

                <p class="flex items-center gap-3 text-sm text-muted">
                    <ColorSwatch :color="alphaColor" size="lg" />
                    Nilai: <span class="font-medium text-ink tabular-nums">{{ alphaColor }}</span>
                </p>
            </div>
        </DocExample>

        <DocProps
            title="ColorSlider"
            :rows="[
                { name: 'v-model', type: 'string', description: 'Warna dalam format hex; kanal alpha menghasilkan hex 8 digit.' },
                {
                    name: 'channel',
                    type: `'hue' | 'saturation' | 'lightness' | 'brightness' | 'red' | 'green' | 'blue' | 'alpha'`,
                    default: `'hue'`,
                    description: 'Kanal warna yang diatur slider.',
                },
                { name: 'color-space', type: `'hsl' | 'hsb'`, default: `'hsl'`, description: 'Untuk kanal saturation: saturasi HSL atau HSB.' },
                { name: 'default-value', type: 'string', default: `'#17795a'`, description: 'Warna awal bila tidak memakai v-model.' },
                { name: 'label', type: 'string', description: 'Label di atas slider, lengkap dengan nilai kanalnya.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan slider.' },
                { name: '@change-end', type: '(hex: string) => void', description: 'Dipanggil saat selesai menggeser.' },
            ]"
        />
    </DocPage>
</template>
