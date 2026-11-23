<script setup>
/**
 * Built on reka-ui Progress (role="progressbar" with aria-valuenow / aria-valuemax).
 * A `null` value means indeterminate (the bar pulses).
 * Variants: brand, info, warning, danger
 * Sizes: sm, md, lg
 *
 * Usage: <Progress v-model="uploaded" label="Lampiran_SK.pdf" show-value />
 *        <Progress :model-value="45" :max="60" variant="info" />
 */
import { ProgressIndicator, ProgressRoot } from 'reka-ui';
import { computed, useId } from 'vue';

const model = defineModel({ type: Number, default: null });

const props = defineProps({
    max: { type: Number, default: 100 },
    label: { type: String, default: null },
    /** Show the percentage next to the label. */
    showValue: { type: Boolean, default: false },
    variant: { type: String, default: 'brand' },
    size: { type: String, default: 'md' },
});

const labelId = useId();

const variantClasses = {
    brand: 'bg-brand',
    info: 'bg-info',
    warning: 'bg-warning',
    danger: 'bg-danger',
};

const sizeClasses = {
    sm: 'h-1.5',
    md: 'h-2',
    lg: 'h-3',
};

const percentage = computed(() => {
    if (model.value === null || model.value === undefined) {
        return null;
    }

    return Math.min(100, Math.max(0, (model.value / props.max) * 100));
});
</script>

<template>
    <div class="flex w-full flex-col gap-2">
        <div v-if="label || showValue" class="flex items-center justify-between gap-3 text-sm">
            <span :id="labelId" class="truncate font-medium text-ink">{{ label }}</span>
            <span v-if="showValue && percentage !== null" class="text-muted tabular-nums">{{ Math.round(percentage) }}%</span>
        </div>

        <ProgressRoot
            v-model="model"
            :max="max"
            :aria-labelledby="label ? labelId : undefined"
            :class="['relative w-full overflow-hidden rounded-full bg-ink/8', sizeClasses[size]]"
        >
            <ProgressIndicator
                :class="[
                    'size-full rounded-full',
                    variantClasses[variant],
                    percentage === null ? 'animate-pulse' : 'transition-transform duration-500 ease-out',
                ]"
                :style="percentage === null ? undefined : { transform: `translateX(-${100 - percentage}%)` }"
            />
        </ProgressRoot>
    </div>
</template>
