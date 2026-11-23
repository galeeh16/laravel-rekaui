<script setup>
/**
 * Internal building block shared by MonthPicker, MonthRangePicker, YearPicker and YearRangePicker:
 * label, a select-like trigger showing `display-value`, the PeriodPanel in a popover (or `inline`),
 * hidden inputs and the hint/error text. The popover closes once a value (or a full range) is picked.
 *
 * The v-model is an @internationalized/date value (`{ start, end }` with `range`); the public
 * wrappers convert it to and from strings and format `display-value`.
 *
 * Usage: <PeriodPicker v-model="calendarDate" view="month" label="Bulan" :display-value="'November 2026'" />
 */
import { PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger } from 'reka-ui';
import { computed, ref, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';
import PeriodPanel from './PeriodPanel.vue';

const model = defineModel({ type: Object, default: undefined });

const props = defineProps({
    /** `month` or `year`, see PeriodPanel. */
    view: { type: String, default: 'month' },
    range: { type: Boolean, default: false },
    minValue: { type: Object, default: undefined },
    maxValue: { type: Object, default: undefined },
    /** Range only: the longest range, in months or years. */
    maximum: { type: Number, default: undefined },
    locale: { type: String, default: 'id-ID' },
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Text in the trigger for the current value; `placeholder` is shown when it is empty. */
    displayValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Pilih…' },
    /** Hidden inputs for plain form submits: `[{ name, value }]`. */
    hiddenInputs: { type: Array, default: () => [] },
    /** Show the grid directly instead of a field with a popover. */
    inline: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const open = ref(false);
const generatedId = useId();
const pickerId = computed(() => props.id ?? generatedId);

const panelProps = computed(() => ({
    view: props.view,
    range: props.range,
    minValue: props.minValue,
    maxValue: props.maxValue,
    maximum: props.maximum,
    locale: props.locale,
    disabled: props.disabled,
}));

function updateValue(value) {
    model.value = value;

    const isComplete = props.range ? Boolean(value?.start && value?.end) : Boolean(value);

    if (isComplete) {
        open.value = false;
    }
}

/** On open, focus the selected (or current) cell instead of the first button, the "previous" arrow. */
function focusInitialCell(event) {
    const content = event.target;
    const cell =
        content.querySelector('[data-selected]') ?? content.querySelector('[data-today]') ?? content.querySelector('[data-value]:not([data-disabled])');

    if (cell) {
        event.preventDefault();
        cell.focus();
    }
}
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="inline ? undefined : pickerId">{{ label }}</Label>

        <PeriodPanel
            v-if="inline"
            :id="pickerId"
            v-bind="panelProps"
            :model-value="model"
            :class="[
                'w-fit rounded-xl border bg-surface p-3 text-ink shadow-xs data-disabled:opacity-60',
                error ? 'border-danger' : 'border-line',
            ]"
            @update:model-value="updateValue"
        />

        <PopoverRoot v-else v-model:open="open">
            <PopoverTrigger
                :id="pickerId"
                :disabled="disabled"
                :aria-invalid="error ? 'true' : undefined"
                :class="[
                    'flex h-11 w-full cursor-pointer items-center gap-2 rounded-xl border bg-surface px-3 text-left text-sm text-ink shadow-xs outline-none transition',
                    'focus-visible:ring-3 disabled:cursor-not-allowed disabled:opacity-60',
                    error
                        ? 'border-danger focus-visible:border-danger focus-visible:ring-danger/15'
                        : 'border-line focus-visible:border-brand focus-visible:ring-brand/15 data-[state=open]:border-brand',
                ]"
            >
                <Icon name="calendar" class="size-4.5 text-muted" />
                <span :class="['flex-1 truncate tabular-nums', displayValue ? null : 'text-muted/80']">{{ displayValue || placeholder }}</span>
                <Icon name="chevron-down" class="size-4 text-muted" />
            </PopoverTrigger>

            <PopoverPortal>
                <PopoverContent
                    :side-offset="6"
                    align="start"
                    class="z-50 origin-(--reka-popover-content-transform-origin) rounded-xl border border-line bg-surface p-3 text-ink shadow-lg shadow-black/8 outline-none data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
                    @open-auto-focus="focusInitialCell"
                >
                    <PeriodPanel v-bind="panelProps" :model-value="model" @update:model-value="updateValue" />
                </PopoverContent>
            </PopoverPortal>
        </PopoverRoot>

        <input v-for="input in hiddenInputs" :key="input.name" type="hidden" :name="input.name" :value="input.value" />

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
