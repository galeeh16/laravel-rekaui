<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import AspectRatio from '@/components/ui/AspectRatio.vue';
import Button from '@/components/ui/Button.vue';
import Icon from '@/components/ui/Icon.vue';

const ratioOptions = [
    { label: '1 : 1', value: 1 },
    { label: '4 : 3', value: 4 / 3 },
    { label: '16 : 9', value: 16 / 9 },
    { label: '21 : 9', value: 21 / 9 },
];

const selectedRatio = ref(16 / 9);
const selectedLabel = computed(() => ratioOptions.find((option) => option.value === selectedRatio.value)?.label);

const documents = [
    { title: 'Surat Undangan', number: '045/UM/XI/2026', accent: 'fill-brand' },
    { title: 'Surat Keputusan', number: '112/KP/XI/2026', accent: 'fill-info' },
    { title: 'Nota Dinas', number: '078/ND/XI/2026', accent: 'fill-warning' },
];

const bodyLines = [170, 162, 168, 120, 0, 170, 158, 166, 140, 0, 170, 96];

const basicCode = `
<script setup>
import AspectRatio from '@/components/ui/AspectRatio.vue';
<\/script>

<template>
    <AspectRatio :ratio="16 / 9" class="w-full max-w-md">
        <img src="/storage/banner-libur.jpg" alt="Pengumuman libur nasional" class="size-full object-cover" />
    </AspectRatio>
</template>
`;

const ratioCode = `
<script setup>
import { ref } from 'vue';
import AspectRatio from '@/components/ui/AspectRatio.vue';

const ratio = ref(16 / 9);
<\/script>

<template>
    <Button variant="ghost" size="sm" @click="ratio = 1">1 : 1</Button>
    <Button variant="ghost" size="sm" @click="ratio = 4 / 3">4 : 3</Button>
    <Button variant="ghost" size="sm" @click="ratio = 16 / 9">16 : 9</Button>

    <AspectRatio :ratio="ratio" class="w-full max-w-md">
        <div class="flex size-full items-center justify-center bg-brand-soft text-brand-ink">...</div>
    </AspectRatio>
</template>
`;

const documentCode = `
<template>
    <!-- A4 portrait: 210 x 297 mm -->
    <AspectRatio :ratio="210 / 297" class="w-40">
        <svg viewBox="0 0 210 297" class="size-full bg-surface">...</svg>
    </AspectRatio>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Dasar" description="Rasio 16:9 untuk banner atau pratinjau; isi menyesuaikan lebar." :code="basicCode">
            <AspectRatio :ratio="16 / 9" class="w-full max-w-md">
                <div
                    class="flex size-full flex-col justify-between bg-linear-to-br from-tone-mint via-tone-sky to-tone-peach p-6"
                    role="img"
                    aria-label="Banner pengumuman libur nasional"
                >
                    <span class="flex size-10 items-center justify-center rounded-xl bg-surface/70 text-brand">
                        <Icon name="calendar" class="size-5" />
                    </span>
                    <div>
                        <p class="text-xs font-medium tracking-wide text-ink/60 uppercase">Pengumuman</p>
                        <p class="mt-1 text-xl font-semibold tracking-tight text-ink">Libur Nasional &amp; Cuti Bersama 2026</p>
                    </div>
                </div>
            </AspectRatio>
        </DocExample>

        <DocExample title="Ganti rasio" description="Prop ratio reaktif; tinggi ikut berubah sesuai lebar." :code="ratioCode">
            <div class="flex w-full max-w-md flex-col gap-4">
                <div class="flex flex-wrap gap-1">
                    <Button
                        v-for="option in ratioOptions"
                        :key="option.label"
                        :variant="selectedRatio === option.value ? 'soft' : 'ghost'"
                        size="sm"
                        :aria-pressed="selectedRatio === option.value"
                        @click="selectedRatio = option.value"
                    >
                        {{ option.label }}
                    </Button>
                </div>

                <AspectRatio :ratio="selectedRatio" class="w-full">
                    <div class="flex size-full flex-col items-center justify-center gap-2 bg-brand-soft text-brand-ink">
                        <Icon name="image" class="size-8" />
                        <span class="text-sm font-medium">{{ selectedLabel }}</span>
                    </div>
                </AspectRatio>

                <p class="text-sm text-muted">Rasio: {{ selectedRatio.toFixed(2) }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Thumbnail dokumen A4"
            description="Rasio 210/297 menjaga proporsi halaman surat di grid mana pun."
            :code="documentCode"
        >
            <div class="grid w-full max-w-xl grid-cols-2 gap-4 sm:grid-cols-3">
                <figure v-for="document in documents" :key="document.number" class="flex flex-col gap-2">
                    <AspectRatio :ratio="210 / 297" class="w-full rounded-2xl border border-line">
                        <svg viewBox="0 0 210 297" class="size-full bg-surface" role="img" :aria-label="`Pratinjau ${document.title}`">
                            <rect x="20" y="20" width="26" height="26" rx="6" :class="document.accent" />
                            <rect x="56" y="23" width="110" height="7" rx="3.5" class="fill-ink/70" />
                            <rect x="56" y="36" width="78" height="5" rx="2.5" class="fill-ink/25" />
                            <rect x="20" y="56" width="170" height="2" class="fill-ink/60" />
                            <rect x="20" y="72" width="64" height="5" rx="2.5" class="fill-ink/40" />
                            <template v-for="(width, index) in bodyLines" :key="index">
                                <rect v-if="width" x="20" :y="92 + index * 12" :width="width" height="5" rx="2.5" class="fill-ink/15" />
                            </template>
                            <rect x="130" y="248" width="60" height="5" rx="2.5" class="fill-ink/40" />
                            <rect x="130" y="262" width="44" height="4" rx="2" class="fill-ink/20" />
                        </svg>
                    </AspectRatio>
                    <figcaption class="text-sm">
                        <span class="block font-medium text-ink">{{ document.title }}</span>
                        <span class="block text-xs text-muted">{{ document.number }}</span>
                    </figcaption>
                </figure>
            </div>
        </DocExample>

        <DocProps
            title="AspectRatio"
            :rows="[
                { name: 'ratio', type: 'number', default: '16 / 9', description: 'Lebar dibagi tinggi, mis. 1, 4 / 3, 210 / 297.' },
                { name: 'class', type: 'string', description: 'Dipasang di elemen luar; atur lebar di sini (wajib di dalam flex).' },
            ]"
        />
    </DocPage>
</template>
