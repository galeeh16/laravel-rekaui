<script setup>
/**
 * Built on reka-ui MonthRangePicker: pick a start and an end month from a year grid (first click =
 * start, second click = end, with a hover preview). Shown in a popover under a select-like field, or
 * directly with `inline`; the popover closes once both months are chosen.
 *
 * The v-model is `{ start: 'YYYY-MM', end: 'YYYY-MM' }`; `end` is an empty string while only the start
 * is chosen. With `name`, hidden inputs `name[start]` and `name[end]` are rendered for plain form submits.
 *
 * Usage: <MonthRangePicker v-model="form.period" label="Periode laporan" :maximum-months="12" />
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
    /** Earliest selectable month, `YYYY-MM`. */
    minMonth: { type: String, default: null },
    /** Latest selectable month, `YYYY-MM`. */
    maxMonth: { type: String, default: null },
    /** Longest range that can be picked, in months. */
    maximumMonths: { type: Number, default: undefined },
    /** Language of the month names. */
    locale: { type: String, default: 'id-ID' },
    placeholder: { type: String, default: 'Pilih rentang bulan' },
    /** Show the month grid directly instead of a field with a popover. */
    inline: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

/** `YYYY-MM` → the 1st of that month, or undefined when empty or invalid. */
function toMonthDate(value) {
    const match = /^(\d{4})-(\d{2})$/.exec(value ?? '');

    if (!match || Number(match[2]) < 1 || Number(match[2]) > 12) {
        return undefined;
    }

    return new CalendarDate(Number(match[1]), Number(match[2]), 1);
}

function toMonthString(date) {
    return date ? `${String(date.year).padStart(4, '0')}-${String(date.month).padStart(2, '0')}` : '';
}

const rangeValue = computed({
    get: () => ({ start: toMonthDate(model.value?.start), end: toMonthDate(model.value?.end) }),
    set: (value) => {
        model.value = { start: toMonthString(value?.start), end: toMonthString(value?.end) };
    },
});

const minValue = computed(() => toMonthDate(props.minMonth));
const maxValue = computed(() => toMonthDate(props.maxMonth));

/** E.g. "Jan–Mar 2026" or "Nov 2026 – Feb 2027" for id-ID; "Jan 2026 – …" while the end is missing. */
const displayValue = computed(() => {
    const { start, end } = rangeValue.value;

    if (!start) {
        return '';
    }

    const formatter = new DateFormatter(props.locale, { month: 'short', year: 'numeric' });
    const timeZone = getLocalTimeZone();

    return end ? formatter.formatRange(start.toDate(timeZone), end.toDate(timeZone)) : `${formatter.format(start.toDate(timeZone))} – …`;
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
        view="month"
        range
        :min-value="minValue"
        :max-value="maxValue"
        :maximum="maximumMonths"
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
