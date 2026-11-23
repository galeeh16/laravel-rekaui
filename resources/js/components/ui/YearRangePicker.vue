<script setup>
/**
 * Built on reka-ui YearRangePicker: pick a start and an end year from a grid of twelve years (first
 * click = start, second click = end, with a hover preview). Shown in a popover under a select-like
 * field, or directly with `inline`; the popover closes once both years are chosen.
 *
 * The v-model is `{ start: 'YYYY', end: 'YYYY' }`; `end` is an empty string while only the start is
 * chosen. Numbers are accepted as input too; picking always emits strings. With `name`, hidden inputs
 * `name[start]` and `name[end]` are rendered for plain form submits.
 *
 * Usage: <YearRangePicker v-model="form.years" label="Rentang tahun arsip" :maximum-years="5" />
 */
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { computed } from 'vue';
import PeriodPicker from './PeriodPicker.vue';

const model = defineModel({ type: Object, default: () => ({ start: '', end: '' }) });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Base name of the hidden inputs: `name[start]` and `name[end]`. */
    name: { type: String, default: undefined },
    /** Earliest selectable year. */
    minYear: { type: [String, Number], default: null },
    /** Latest selectable year. */
    maxYear: { type: [String, Number], default: null },
    /** Longest range that can be picked, in years. */
    maximumYears: { type: Number, default: undefined },
    /** Language of the year numbers. */
    locale: { type: String, default: 'id-ID' },
    placeholder: { type: String, default: 'Pilih rentang tahun' },
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

const rangeValue = computed({
    get: () => ({ start: toYearDate(model.value?.start), end: toYearDate(model.value?.end) }),
    set: (value) => {
        model.value = { start: value?.start ? String(value.start.year) : '', end: value?.end ? String(value.end.year) : '' };
    },
});

const minValue = computed(() => toYearDate(props.minYear));
const maxValue = computed(() => toYearDate(props.maxYear));

/** E.g. "2020 – 2025"; "2020 – …" while the end is missing. */
const displayValue = computed(() => {
    const { start, end } = rangeValue.value;

    if (!start) {
        return '';
    }

    if (!end) {
        return `${start.year} – …`;
    }

    const timeZone = getLocalTimeZone();

    return new DateFormatter(props.locale, { year: 'numeric' }).formatRange(start.toDate(timeZone), end.toDate(timeZone));
});

const hiddenInputs = computed(() =>
    props.name
        ? [
              { name: `${props.name}[start]`, value: model.value?.start ?? '' },
              { name: `${props.name}[end]`, value: model.value?.end ?? '' },
          ]
        : [],
);
</script>

<template>
    <PeriodPicker
        :id="id"
        v-model="rangeValue"
        view="year"
        range
        :min-value="minValue"
        :max-value="maxValue"
        :maximum="maximumYears"
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
