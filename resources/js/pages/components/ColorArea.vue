<script setup>
import { convertToRgb, parseColor } from 'reka-ui';
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import ColorArea from '@/components/ui/ColorArea.vue';
import ColorField from '@/components/ui/ColorField.vue';
import ColorSlider from '@/components/ui/ColorSlider.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const BRAND_COLOR = '#17795a';

const color = ref(BRAND_COLOR);
const brandColor = ref(BRAND_COLOR);
const hueLightnessColor = ref('#2d5bb0');

/** Dark text on light brand colours, white text otherwise. */
const brandTextColor = computed(() => {
    const { r, g, b } = convertToRgb(parseColor(brandColor.value));

    return (0.299 * r + 0.587 * g + 0.114 * b) / 255 > 0.6 ? '#171716' : '#ffffff';
});

const basicCode = `
<script setup>
import { ref } from 'vue';
import ColorArea from '@/components/ui/ColorArea.vue';

const color = ref('#17795a');
<\/script>

<template>
    <ColorArea v-model="color" class="max-w-xs" />

    <p>Nilai: {{ color }}</p>
</template>
`;

const brandCode = `
<script setup>
import { convertToRgb, parseColor } from 'reka-ui';
import { computed, ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import ColorArea from '@/components/ui/ColorArea.vue';
import ColorField from '@/components/ui/ColorField.vue';
import ColorSlider from '@/components/ui/ColorSlider.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const brandColor = ref('#17795a');

// Dark text on light brand colours, white text otherwise.
const brandTextColor = computed(() => {
    const { r, g, b } = convertToRgb(parseColor(brandColor.value));

    return (0.299 * r + 0.587 * g + 0.114 * b) / 255 > 0.6 ? '#171716' : '#ffffff';
});
<\/script>

<template>
    <div class="flex w-full max-w-sm flex-col gap-5">
        <ColorArea v-model="brandColor" />
        <ColorSlider v-model="brandColor" channel="hue" label="Hue" />

        <ColorField v-model="brandColor" label="Hex" />

        <div class="flex items-center gap-3">
            <ColorSwatch :color="brandColor" size="lg" label="Pratinjau warna brand" />

            <span
                class="inline-flex h-10 items-center rounded-xl px-4 text-sm font-medium"
                :style="{ backgroundColor: brandColor, color: brandTextColor }"
            >
                Kirim surat
            </span>

            <Button variant="secondary" size="sm" class="ml-auto" @click="brandColor = '#17795a'">Reset</Button>
        </div>
    </div>
</template>
`;

const channelsCode = `
<script setup>
import { ref } from 'vue';
import ColorArea from '@/components/ui/ColorArea.vue';

const color = ref('#2d5bb0');
<\/script>

<template>
    <ColorArea v-model="color" color-space="hsl" x-channel="hue" y-channel="lightness" class="max-w-xs" />

    <p>Nilai: {{ color }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar + v-model"
            description="Geser titik untuk mengatur saturasi (sumbu x) dan kecerahan (sumbu y). Bisa juga dengan tombol panah; tahan Shift untuk langkah 10."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <ColorArea v-model="color" />

                <p class="flex items-center gap-2 text-sm text-muted">
                    <ColorSwatch :color="color" size="sm" />
                    Nilai: <span class="font-medium text-ink tabular-nums">{{ color }}</span>
                </p>
            </div>
        </DocExample>

        <DocExample
            title="Pilih warna brand"
            description="ColorArea, ColorSlider hue, ColorField, dan ColorSwatch berbagi satu v-model hex, jadi semuanya selalu sinkron."
            :code="brandCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-5">
                <ColorArea v-model="brandColor" />
                <ColorSlider v-model="brandColor" channel="hue" label="Hue" />

                <ColorField v-model="brandColor" label="Hex" />

                <div class="flex items-center gap-3 border-t border-line pt-5">
                    <ColorSwatch :color="brandColor" size="lg" label="Pratinjau warna brand" />

                    <span
                        class="inline-flex h-10 items-center rounded-xl px-4 text-sm font-medium shadow-xs"
                        :style="{ backgroundColor: brandColor, color: brandTextColor }"
                    >
                        Kirim surat
                    </span>

                    <Button variant="secondary" size="sm" class="ml-auto" @click="brandColor = BRAND_COLOR">Reset</Button>
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Kanal lain"
            description="Sumbu bisa diganti, misalnya hue di sumbu x dan lightness di sumbu y dalam ruang warna HSL."
            :code="channelsCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <ColorArea v-model="hueLightnessColor" color-space="hsl" x-channel="hue" y-channel="lightness" />

                <p class="flex items-center gap-2 text-sm text-muted">
                    <ColorSwatch :color="hueLightnessColor" size="sm" />
                    Nilai: <span class="font-medium text-ink tabular-nums">{{ hueLightnessColor }}</span>
                </p>
            </div>
        </DocExample>

        <DocProps
            title="ColorArea"
            :rows="[
                { name: 'v-model', type: 'string', description: 'Warna terpilih dalam format hex, misalnya #17795a.' },
                { name: 'default-value', type: 'string', default: `'#17795a'`, description: 'Warna awal bila tidak memakai v-model.' },
                { name: 'color-space', type: `'hsb' | 'hsl' | 'rgb'`, default: `'hsb'`, description: 'Ruang warna yang dipakai kedua sumbu.' },
                { name: 'x-channel', type: 'string', default: `'saturation'`, description: 'Kanal di sumbu horizontal (hue, saturation, lightness, brightness, red, green, blue).' },
                { name: 'y-channel', type: 'string', default: `'brightness'`, description: 'Kanal di sumbu vertikal.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan area.' },
                { name: '@change-end', type: '(hex: string) => void', description: 'Dipanggil saat selesai menggeser dengan pointer.' },
            ]"
        />
    </DocPage>
</template>
