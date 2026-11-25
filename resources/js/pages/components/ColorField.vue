<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import ColorField from '@/components/ui/ColorField.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const hexColor = ref('#17795a');
const rgbColor = ref('#17795a');

const hexCode = `
<script setup>
import { ref } from 'vue';
import ColorField from '@/components/ui/ColorField.vue';

const color = ref('#17795a');
<\/script>

<template>
    <ColorField
        v-model="color"
        label="Warna brand"
        hint="Ketik kode hex lalu tekan Enter; panah atas/bawah menggeser nilainya."
    />

    <p>Nilai: {{ color }}</p>
</template>
`;

const channelCode = `
<script setup>
import { ref } from 'vue';
import ColorField from '@/components/ui/ColorField.vue';
import ColorSwatch from '@/components/ui/ColorSwatch.vue';

const color = ref('#17795a');
<\/script>

<template>
    <div class="grid grid-cols-3 gap-3">
        <ColorField v-model="color" channel="red" label="Merah" />
        <ColorField v-model="color" channel="green" label="Hijau" />
        <ColorField v-model="color" channel="blue" label="Biru" />
    </div>

    <ColorSwatch :color="color" size="lg" />
</template>
`;

const statesCode = `
<template>
    <ColorField
        label="Warna aksen"
        default-value="#c03a26"
        error="Warna terlalu mirip dengan warna status bahaya."
    />

    <ColorField label="Warna sistem" default-value="#17795a" hint="Diatur oleh admin." disabled />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Hex + v-model"
            description="Nilai disimpan saat menekan Enter atau keluar dari input; input yang tidak valid kembali ke warna terakhir."
            :code="hexCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <ColorField
                    v-model="hexColor"
                    label="Warna brand"
                    hint="Ketik kode hex lalu tekan Enter; panah atas/bawah menggeser nilainya."
                />

                <p class="text-sm text-muted">
                    Nilai: <span class="font-medium text-ink tabular-nums">{{ hexColor }}</span>
                </p>
            </div>
        </DocExample>

        <DocExample
            title="Per kanal"
            description="Dengan prop channel, input berisi angka satu kanal saja. Beberapa field bisa berbagi satu v-model."
            :code="channelCode"
        >
            <div class="flex w-full max-w-md flex-col gap-4">
                <div class="grid grid-cols-3 gap-3">
                    <ColorField v-model="rgbColor" channel="red" label="Merah" />
                    <ColorField v-model="rgbColor" channel="green" label="Hijau" />
                    <ColorField v-model="rgbColor" channel="blue" label="Biru" />
                </div>

                <p class="flex items-center gap-3 text-sm text-muted">
                    <ColorSwatch :color="rgbColor" size="lg" />
                    Nilai: <span class="font-medium text-ink tabular-nums">{{ rgbColor }}</span>
                </p>
            </div>
        </DocExample>

        <DocExample title="Error & nonaktif" description="Pesan error menggantikan hint, sama seperti Input." :code="statesCode">
            <div class="flex w-full max-w-xs flex-col gap-5">
                <ColorField label="Warna aksen" default-value="#c03a26" error="Warna terlalu mirip dengan warna status bahaya." />
                <ColorField label="Warna sistem" default-value="#17795a" hint="Diatur oleh admin." disabled />
            </div>
        </DocExample>

        <DocProps
            title="ColorField"
            :rows="[
                { name: 'v-model', type: 'string', description: 'Warna dalam format hex, misalnya #17795a.' },
                { name: 'default-value', type: 'string', default: `'#17795a'`, description: 'Warna awal bila tidak memakai v-model.' },
                { name: 'label', type: 'string', description: 'Label di atas input.' },
                {
                    name: 'channel',
                    type: `'hue' | 'saturation' | 'lightness' | 'brightness' | 'red' | 'green' | 'blue' | 'alpha'`,
                    description: 'Ubah satu kanal sebagai angka, bukan kode hex.',
                },
                { name: 'color-space', type: `'hsl' | 'hsb'`, default: `'hsl'`, description: 'Untuk channel saturation: saturasi HSL atau HSB.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah input.' },
                { name: 'error', type: 'string', description: 'Pesan error; memberi border merah dan menggantikan hint.' },
                { name: 'name', type: 'string', description: 'Nama field untuk form HTML biasa (nilai hex).' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan input.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Input hanya bisa dibaca.' },
                { name: 'id', type: 'string', description: 'Id input; dibuat otomatis bila kosong.' },
            ]"
        />
    </DocPage>
</template>
