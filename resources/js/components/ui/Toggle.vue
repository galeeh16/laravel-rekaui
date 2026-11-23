<script setup>
/**
 * Built on reka-ui Toggle: a button that stays pressed (aria-pressed, data-state="on") until clicked again.
 * Variants: outline (bordered, default), ghost
 * Sizes: sm, md, icon-sm (32px), icon-md (36px), icon (40px)
 *
 * Usage: <Toggle v-model="isImportant" icon="star">Penting</Toggle>
 *        <Toggle v-model="isConfidential" icon="lock" size="icon" aria-label="Rahasia" />   (icon only: always add aria-label)
 */
import { Toggle } from 'reka-ui';
import { computed } from 'vue';
import Icon from './Icon.vue';

const model = defineModel({ type: Boolean, default: false });

const props = defineProps({
    icon: { type: String, default: null },
    variant: { type: String, default: 'outline' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
});

const variantClasses = {
    outline: 'border border-line bg-surface shadow-xs hover:bg-canvas data-[state=on]:border-brand/30',
    ghost: 'hover:bg-ink/5',
};

const sizeClasses = {
    sm: 'h-8 gap-1.5 rounded-lg px-3 text-sm',
    md: 'h-10 gap-2 rounded-xl px-4 text-sm',
    'icon-sm': 'size-8 rounded-lg',
    'icon-md': 'size-9 rounded-lg',
    icon: 'size-10 rounded-xl',
};

const iconClass = computed(() => (['sm', 'icon-sm', 'icon-md'].includes(props.size) ? 'size-4' : 'size-4.5'));
</script>

<template>
    <Toggle
        v-slot="{ pressed }"
        v-model="model"
        :disabled="disabled"
        :class="[
            'inline-flex shrink-0 cursor-pointer items-center justify-center font-medium whitespace-nowrap text-ink transition-colors',
            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-50',
            'data-[state=on]:bg-brand-soft data-[state=on]:text-brand-ink',
            variantClasses[variant],
            sizeClasses[size],
        ]"
    >
        <Icon v-if="icon" :name="icon" :class="iconClass" />
        <slot :pressed="pressed" />
    </Toggle>
</template>
