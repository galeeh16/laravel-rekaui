<script setup>
/**
 * Filter pill, rendered as an Inertia link.
 * Usage: <Chip :href="route('dashboard', { status: 'draft' })" :active="true" :count="2">Draf</Chip>
 */
import { Link } from '@inertiajs/vue3';
import Count from './Count.vue';

defineProps({
    href: { type: String, required: true },
    active: { type: Boolean, default: false },
    count: { type: Number, default: null },
});
</script>

<template>
    <Link
        :href="href"
        preserve-scroll
        :aria-current="active ? 'page' : undefined"
        :class="[
            'inline-flex h-10 items-center gap-2 rounded-xl px-4 text-sm font-medium whitespace-nowrap transition-colors',
            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
            active ? 'bg-ink text-canvas' : 'border border-line bg-surface text-ink hover:bg-canvas',
        ]"
    >
        <slot />
        <Count v-if="count !== null" :variant="active ? 'inverse' : 'muted'">{{ count }}</Count>
    </Link>
</template>
