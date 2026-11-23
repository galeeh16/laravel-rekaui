<script setup>
import { TabsTrigger } from 'reka-ui';
import { inject } from 'vue';
import Icon from './Icon.vue';

defineProps({
    value: { type: [String, Number], required: true },
    icon: { type: String, default: null },
    disabled: { type: Boolean, default: false },
});

const variant = inject('tabsVariant', 'segmented');
</script>

<template>
    <TabsTrigger
        :value="value"
        :disabled="disabled"
        :class="[
            'inline-flex cursor-pointer items-center justify-center gap-2 text-sm font-medium whitespace-nowrap text-muted transition-colors hover:text-ink',
            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-50',
            variant === 'line'
                ? '-mb-px border-b-2 border-transparent px-4 pt-1 pb-3 data-[state=active]:border-brand data-[state=active]:text-ink'
                : 'h-8 rounded-lg px-3 data-[state=active]:bg-surface data-[state=active]:text-ink data-[state=active]:shadow-xs',
        ]"
    >
        <Icon v-if="icon" :name="icon" class="size-4" />
        <slot />
    </TabsTrigger>
</template>
