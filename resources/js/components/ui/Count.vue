<script setup>
/**
 * Small numeric bubble used in tabs, chips and navigation.
 * Variants: muted, dark, brand, inverse
 */
defineProps({
    variant: { type: String, default: 'muted' },
});

const variantClasses = {
    muted: 'bg-ink/8 text-ink/80',
    dark: 'bg-ink text-canvas',
    brand: 'bg-brand text-white',
    inverse: 'bg-canvas/20 text-canvas',
};
</script>

<template>
    <span
        :class="[
            'inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-xs font-semibold tabular-nums',
            variantClasses[variant],
        ]"
    >
        <slot />
    </span>
</template>
