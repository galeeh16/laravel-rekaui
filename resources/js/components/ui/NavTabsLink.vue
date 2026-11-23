<script setup>
import { Link } from '@inertiajs/vue3';
import Count from './Count.vue';

defineProps({
    href: { type: String, default: null },
    active: { type: Boolean, default: false },
    count: { type: Number, default: null },
});
</script>

<template>
    <component
        :is="href ? Link : 'a'"
        :href="href ?? '#'"
        :aria-current="active ? 'page' : undefined"
        :class="[
            'inline-flex items-center gap-2 border-b-2 px-4 pt-1 pb-3 text-[0.9375rem] font-medium whitespace-nowrap transition-colors',
            active ? 'border-brand text-ink' : 'border-transparent text-muted hover:text-ink',
        ]"
        @click="href ? undefined : $event.preventDefault()"
    >
        <slot />
        <Count v-if="count !== null" :variant="active ? 'brand' : 'muted'">{{ count }}</Count>
    </component>
</template>
