<script setup>
/**
 * Built on reka-ui MonthPicker: pick a month from a grid of the twelve months of a year (arrows on
 * the sides change the year). Shown in a popover under a select-like field, or directly with `inline`.
 *
 * The v-model is a `YYYY-MM` string, ready for Laravel's `date_format:Y-m`.
 *
 * Usage: <MonthPicker v-model="form.period" label="Periode laporan" max-month="2026-11" :error="form.errors.period" />
 */
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { computed } from 'vue';
import PeriodPicker from './PeriodPicker.vue';

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Name of a hidden input carrying the v-model value, for plain form submits. */
    name: { type: String, default: undefined },
    /** Earliest selectable month, `YYYY-MM`. */
    minMonth: { type: String, default: null },
    /** Latest selectable month, `YYYY-MM`. */
    maxMonth: { type: String, default: null },
    /** Language of the month names. */
    locale: { type: String, default: 'id-ID' },
    placeholder: { type: String, default: 'Pilih bulan' },
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
    return `${String(date.year).padStart(4, '0')}-${String(date.month).padStart(2, '0')}`;
}

const monthValue = computed({
    get: () => toMonthDate(model.value),
    set: (value) => {
        model.value = value ? toMonthString(value) : '';
    },
});

const minValue = computed(() => toMonthDate(props.minMonth));
const maxValue = computed(() => toMonthDate(props.maxMonth));

/** E.g. "November 2026" for id-ID. */
const displayValue = computed(() =>
    monthValue.value ? new DateFormatter(props.locale, { month: 'long', year: 'numeric' }).format(monthValue.value.toDate(getLocalTimeZone())) : '',
);

const hiddenInputs = computed(() => (props.name ? [{ name: props.name, value: model.value }] : []));
</script>

<template>
    <PeriodPicker
        :id="id"
        v-model="monthValue"
        view="month"
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
