<script setup>
/**
 * Usage: <Icon name="search" class="size-4" />
 * Available names: see `resources/js/lib/icons.js`. An unknown name renders an empty icon of the same size.
 */
import { Icon as LucideIcon } from '@lucide/vue';
import { computed } from 'vue';
import { icons } from '@/lib/icons';

const props = defineProps({
    name: { type: String, required: true },
});

const icon = computed(() => icons[props.name] ?? null);
</script>

<template>
    <component :is="icon" v-if="icon" :size="20" :stroke-width="1.75" aria-hidden="true" class="shrink-0" />
    <LucideIcon v-else :icon-node="[]" :size="20" :stroke-width="1.75" aria-hidden="true" class="shrink-0" />
</template>
