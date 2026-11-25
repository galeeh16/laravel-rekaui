<script setup>
/**
 * Variants: neutral, success, danger, info, warning
 * Usage: <Badge variant="danger" dot>Terlambat</Badge>
 */
defineProps({
    variant: { type: String, default: 'neutral' },
    dot: { type: Boolean, default: false },
});

const variantClasses = {
    neutral: 'bg-ink/6 text-ink',
    success: 'bg-brand-soft text-brand-ink',
    danger: 'bg-danger-soft text-danger',
    info: 'bg-info-soft text-info',
    warning: 'bg-warning-soft text-warning',
};
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-sm font-medium whitespace-nowrap',
            variantClasses[variant],
        ]"
    >
        <span v-if="dot" class="size-1.5 rounded-full bg-current" aria-hidden="true" />
        <slot />
    </span>
</template>
