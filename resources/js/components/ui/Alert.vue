<script setup>
/**
 * Variants: success, danger, info, warning
 * Usage: <Alert variant="success">Data tersimpan.</Alert>
 */
import { computed } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    variant: { type: String, default: 'info' },
});

const variants = {
    success: { classes: 'bg-brand-soft text-brand-ink', icon: 'circle-check' },
    danger: { classes: 'bg-danger-soft text-danger', icon: 'triangle-alert' },
    info: { classes: 'bg-info-soft text-info', icon: 'info' },
    warning: { classes: 'bg-warning-soft text-warning', icon: 'triangle-alert' },
};

const current = computed(() => variants[props.variant] ?? variants.info);
</script>

<template>
    <div role="status" :class="['flex items-start gap-2.5 rounded-xl px-4 py-3 text-sm', current.classes]">
        <Icon :name="current.icon" class="mt-px size-4.5" />
        <div><slot /></div>
    </div>
</template>
