<script setup>
/**
 * Usage: <Icon name="search" class="size-4" />
 * Available names: see `resources/js/lib/icons.js`.
 */
import { computed } from 'vue';
import { icons } from '@/lib/icons';

const props = defineProps({
    name: { type: String, required: true },
});

const paths = computed(() => icons[props.name] ?? '');
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.75"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        class="shrink-0"
        v-html="paths"
    />
</template>
