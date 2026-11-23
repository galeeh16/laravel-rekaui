<script setup>
/**
 * Built on reka-ui Slider: drag a thumb or click the track; arrow keys move one step,
 * Shift + arrows / Page Up/Down move ten steps, Home/End jump to min/max.
 * Give v-model a number for one thumb, or an array (e.g. [5, 20]) for a range with two thumbs.
 *
 * Usage: <Slider v-model="form.quality" label="Kualitas" show-value :format-value="(value) => value + '%'" />
 *        <Slider v-model="pageRange" label="Rentang halaman" :min="1" :max="40" :min-steps-between-thumbs="1" />
 */
import { SliderRange, SliderRoot, SliderThumb, SliderTrack } from 'reka-ui';
import { computed, useId } from 'vue';

const model = defineModel({ type: [Number, Array], default: undefined });

const props = defineProps({
    min: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    step: { type: Number, default: 1 },
    /** Range mode: minimum number of steps between two thumbs. */
    minStepsBetweenThumbs: { type: Number, default: 0 },
    orientation: { type: String, default: 'horizontal' },
    label: { type: String, default: null },
    /** Show the current value (through `format-value`) next to the label. */
    showValue: { type: Boolean, default: false },
    /** Turns a value into display text, also used as the thumb's aria-valuetext. */
    formatValue: { type: Function, default: (value) => String(value) },
    name: { type: String, default: undefined },
    disabled: { type: Boolean, default: false },
    hint: { type: String, default: null },
});

const emit = defineEmits(['valueCommit']);

const labelId = useId();

/** reka-ui always works with an array (one entry per thumb); a number v-model stays a number. */
const thumbValues = computed({
    get: () => (Array.isArray(model.value) ? model.value : [model.value ?? props.min]),
    set: (values) => {
        model.value = Array.isArray(model.value) ? values : values[0];
    },
});

const valueText = computed(() => thumbValues.value.map((value) => props.formatValue(value)).join(' – '));

function thumbLabel(index) {
    const name = props.label ?? 'Nilai';
    const count = thumbValues.value.length;

    if (count === 1) {
        return name;
    }

    if (count === 2) {
        return index === 0 ? `${name} minimum` : `${name} maksimum`;
    }

    return `${name} ${index + 1}`;
}

/** Fired once when the user releases the thumb or presses a key, not on every drag move. */
function commit(values) {
    emit('valueCommit', Array.isArray(model.value) ? values : values[0]);
}
</script>

<template>
    <div class="flex flex-col gap-3">
        <div v-if="label || showValue" class="flex items-center justify-between gap-3">
            <span v-if="label" :id="labelId" class="text-sm font-medium text-ink">{{ label }}</span>
            <span v-if="showValue" class="text-sm text-muted tabular-nums">{{ valueText }}</span>
        </div>

        <SliderRoot
            v-model="thumbValues"
            :min="min"
            :max="max"
            :step="step"
            :min-steps-between-thumbs="minStepsBetweenThumbs"
            :orientation="orientation"
            :name="name"
            :disabled="disabled"
            :aria-labelledby="label ? labelId : undefined"
            class="relative flex touch-none items-center select-none data-disabled:pointer-events-none data-disabled:opacity-50 data-[orientation=horizontal]:h-5 data-[orientation=horizontal]:w-full data-[orientation=vertical]:h-44 data-[orientation=vertical]:w-5 data-[orientation=vertical]:flex-col"
            @value-commit="commit"
        >
            <SliderTrack
                class="relative grow overflow-hidden rounded-full bg-ink/10 data-[orientation=horizontal]:h-1.5 data-[orientation=vertical]:w-1.5"
            >
                <SliderRange class="absolute rounded-full bg-brand data-[orientation=horizontal]:h-full data-[orientation=vertical]:w-full" />
            </SliderTrack>

            <SliderThumb
                v-for="(value, index) in thumbValues"
                :key="index"
                :aria-label="thumbLabel(index)"
                :aria-valuetext="formatValue(value)"
                class="block size-5 cursor-grab rounded-full border-2 border-brand bg-surface shadow-sm transition-shadow hover:ring-4 hover:ring-brand/15 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand active:cursor-grabbing"
            />
        </SliderRoot>

        <p v-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
