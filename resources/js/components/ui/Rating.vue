<script setup>
/**
 * Built on reka-ui Rating (a radio group under the hood: arrow keys change the value).
 * With `step` 0.5 each icon is split in two halves. Sizes: sm, md, lg
 *
 * Usage: <Rating v-model="form.score" label="Kepuasan layanan" />
 *        <Rating v-model="form.score" :step="0.5" clearable />
 *        <Rating :model-value="4.5" :step="0.5" readonly size="sm" />
 */
import { RatingItem, RatingItemIndicator, RatingRoot } from 'reka-ui';
import { useId } from 'vue';
import Icon from './Icon.vue';

/** 0 means no rating yet. */
const model = defineModel({ type: Number, default: 0 });

defineProps({
    /** Number of icons. */
    length: { type: Number, default: 5 },
    /** Smallest change, e.g. 0.5 for half stars. */
    step: { type: Number, default: 1 },
    /** Clicking the current value again resets it to 0. */
    clearable: { type: Boolean, default: false },
    /** Preview the value under the mouse before clicking. */
    hoverable: { type: Boolean, default: true },
    /** Display only, without the dimmed disabled look. */
    readonly: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    size: { type: String, default: 'md' },
    icon: { type: String, default: 'star' },
    label: { type: String, default: null },
    name: { type: String, default: undefined },
    hint: { type: String, default: null },
});

const labelId = useId();

const sizeClasses = {
    sm: 'size-4',
    md: 'size-6',
    lg: 'size-8',
};
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <span v-if="label" :id="labelId" class="text-sm font-medium text-ink">{{ label }}</span>

        <RatingRoot
            v-slot="{ items }"
            v-model="model"
            :length="length"
            :step="step"
            :clearable="clearable"
            :hoverable="hoverable && !readonly"
            :disabled="disabled || readonly"
            :name="name"
            :aria-labelledby="label ? labelId : undefined"
            :aria-readonly="readonly ? 'true' : undefined"
            :class="['flex w-fit items-center', size === 'sm' ? 'gap-0.5' : 'gap-1', disabled && 'opacity-50']"
        >
            <RatingItem v-for="item in items" :key="item" v-slot="{ steps }" :item="item" :class="['relative inline-flex', sizeClasses[size]]">
                <!-- Stacked indicators: a half step sits on top of the full one and covers its left half. -->
                <RatingItemIndicator
                    v-for="ratingStep in steps"
                    :key="ratingStep"
                    :step="ratingStep"
                    :aria-label="`${ratingStep} dari ${length}`"
                    :class="[
                        'group absolute inset-y-0 left-0 flex w-(--reka-rating-item-step-width) overflow-hidden rounded-sm text-ink/20 transition-colors',
                        'z-(--reka-rating-item-step-z-index) opacity-(--reka-rating-item-step-opacity) data-[state=active]:text-warning',
                        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                        disabled || readonly ? 'cursor-default' : 'cursor-pointer',
                    ]"
                >
                    <Icon :name="icon" :class="[sizeClasses[size], 'group-data-[state=active]:fill-current']" />
                </RatingItemIndicator>
            </RatingItem>
        </RatingRoot>

        <p v-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
