<script setup>
/**
 * Tones: peach, sky, mint, cream
 * Usage: <StatCard tone="peach" icon="clock" label="Menunggu" :value="5" caption="surat" />
 */
import Icon from './Icon.vue';

defineProps({
    tone: { type: String, default: 'peach' },
    icon: { type: String, default: null },
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    caption: { type: String, default: null },
});

const toneClasses = {
    peach: 'bg-tone-peach',
    sky: 'bg-tone-sky',
    mint: 'bg-tone-mint',
    cream: 'bg-tone-cream',
};
</script>

<template>
    <div :class="['flex flex-col rounded-2xl p-5', toneClasses[tone]]">
        <div class="flex items-center gap-2 text-sm text-ink/75">
            <Icon v-if="icon" :name="icon" class="size-4.5" />
            {{ label }}
        </div>

        <div class="mt-3 text-3xl font-medium tracking-tight tabular-nums">{{ value }}</div>

        <div v-if="caption" class="mt-auto truncate pt-6 text-sm text-ink/75">{{ caption }}</div>
    </div>
</template>
