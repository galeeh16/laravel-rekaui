<script setup>
/**
 * Variants: segmented (pill background, default), line (underline)
 */
import { TabsList } from 'reka-ui';
import { provide } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'segmented' },
});

provide('tabsVariant', props.variant);
</script>

<template>
    <TabsList
        :class="[
            'data-[orientation=vertical]:flex-col data-[orientation=vertical]:items-stretch',
            variant === 'line'
                ? 'flex gap-1 border-b border-line data-[orientation=vertical]:border-r data-[orientation=vertical]:border-b-0'
                : 'inline-flex w-fit items-center gap-1 rounded-xl bg-ink/6 p-1',
        ]"
    >
        <slot />
    </TabsList>
</template>
