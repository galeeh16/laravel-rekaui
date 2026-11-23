<script setup>
/**
 * Built on reka-ui YearPicker: pick a year from a grid of twelve years (arrows on the sides page
 * through them). Shown in a popover under a select-like field, or directly with `inline`.
 *
 * The v-model is a `YYYY` string, ready for Laravel's `digits:4` / `date_format:Y`. A number
 * (e.g. 2026) is accepted as input too; picking a year always emits a string.
 *
 * Usage: <YearPicker v-model="form.archive_year" label="Tahun arsip" min-year="2015" :max-year="2026" />
 */
import { CalendarDate } from '@internationalized/date';
import { computed } from 'vue';
import PeriodPicker from './PeriodPicker.vue';

const model = defineModel({ type: [String, Number], default: '' });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Name of a hidden input carrying the v-model value, for plain form submits. */
    name: { type: String, default: undefined },
    /** Earliest selectable year. */
    minYear: { type: [String, Number], default: null },
    /** Latest selectable year. */
    maxYear: { type: [String, Number], default: null },
    /** Language of the year numbers (e.g. the era for non-Gregorian locales). */
    locale: { type: String, default: 'id-ID' },
    placeholder: { type: String, default: 'Pilih tahun' },
    /** Show the year grid directly instead of a field with a popover. */
    inline: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

/** `YYYY` (string or number) → January 1st of that year, or undefined when empty or invalid. */
function toYearDate(value) {
    return /^\d{4}$/.test(String(value ?? '')) ? new CalendarDate(Number(value), 1, 1) : undefined;
}

const yearValue = computed({
    get: () => toYearDate(model.value),
    set: (value) => {
        model.value = value ? String(value.year) : '';
    },
});

const minValue = computed(() => toYearDate(props.minYear));
const maxValue = computed(() => toYearDate(props.maxYear));

const displayValue = computed(() => (yearValue.value ? String(yearValue.value.year) : ''));

const hiddenInputs = computed(() => (props.name ? [{ name: props.name, value: model.value ?? '' }] : []));
</script>

<template>
    <PeriodPicker
        :id="id"
        v-model="yearValue"
        view="year"
        :min-value="minValue"
        :max-value="maxValue"
        :locale="locale"
        :label="label"
        :display-value="displayValue"
        :placeholder="placeholder"
        :hidden-inputs="hiddenInputs"
        :inline="inline"
        :disabled="disabled"
        :error="error"
        :hint="hint"
    />
</template>
