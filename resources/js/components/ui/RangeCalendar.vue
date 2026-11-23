<script setup>
/**
 * Built on reka-ui RangeCalendar: an inline calendar to pick a date range (first click = start,
 * second click = end, with a hover preview in between). Need a form field with a popover? Use DateRangePicker.
 *
 * The v-model is `{ start: 'YYYY-MM-DD', end: 'YYYY-MM-DD' }`. While only the start is chosen,
 * `end` is an empty string.
 *
 * Usage: <RangeCalendar v-model="period" :number-of-months="2" :maximum-days="31" />
 */
import { parseDate } from '@internationalized/date';
import { RangeCalendarRoot } from 'reka-ui';
import { computed } from 'vue';
import CalendarPanel from './CalendarPanel.vue';

const model = defineModel({ type: Object, default: () => ({ start: '', end: '' }) });

const props = defineProps({
    /** Earliest selectable date, `YYYY-MM-DD`. */
    minDate: { type: String, default: null },
    /** Latest selectable date, `YYYY-MM-DD`. */
    maxDate: { type: String, default: null },
    /** Longest range that can be picked, in days. */
    maximumDays: { type: Number, default: undefined },
    /** Language of the month and weekday names. */
    locale: { type: String, default: 'id-ID' },
    /** Months shown side by side (stacked on small screens). */
    numberOfMonths: { type: Number, default: 1 },
    /** `(date: string) => boolean`: dates that can't be picked nor be inside the range, e.g. holidays. */
    isDateUnavailable: { type: Function, default: null },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
});

function toCalendarDate(value) {
    try {
        return value ? parseDate(value) : undefined;
    } catch {
        return undefined;
    }
}

const rangeValue = computed({
    get: () => ({ start: toCalendarDate(model.value?.start), end: toCalendarDate(model.value?.end) }),
    set: (value) => {
        model.value = { start: value?.start?.toString() ?? '', end: value?.end?.toString() ?? '' };
    },
});

const minValue = computed(() => toCalendarDate(props.minDate));
const maxValue = computed(() => toCalendarDate(props.maxDate));

const unavailableMatcher = computed(() => (props.isDateUnavailable ? (date) => props.isDateUnavailable(date.toString()) : undefined));
</script>

<template>
    <RangeCalendarRoot
        v-slot="{ weekDays, grid }"
        v-model="rangeValue"
        :locale="locale"
        :min-value="minValue"
        :max-value="maxValue"
        :maximum-days="maximumDays"
        :number-of-months="numberOfMonths"
        :is-date-unavailable="unavailableMatcher"
        :disabled="disabled"
        :readonly="readonly"
        :week-starts-on="1"
        weekday-format="short"
        fixed-weeks
        class="w-fit rounded-xl border border-line bg-surface p-3 text-ink shadow-xs data-disabled:opacity-60"
    >
        <CalendarPanel range :grid="grid" :week-days="weekDays" />
    </RangeCalendarRoot>
</template>
