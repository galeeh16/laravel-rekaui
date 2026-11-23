<script setup>
/**
 * Built on reka-ui Calendar: an inline month calendar to pick one date, with keyboard navigation
 * (arrows, Page Up/Down, Home/End). The v-model is a `YYYY-MM-DD` string, ready to send to Laravel.
 * Need a form field with a popover instead? Use DatePicker.
 *
 * `is-date-unavailable` receives each date as a `YYYY-MM-DD` string; unavailable dates are struck
 * through and can't be picked (e.g. public holidays).
 *
 * Usage: <Calendar v-model="meetingDate" min-date="2026-11-01" :is-date-unavailable="(date) => holidays.includes(date)" />
 */
import { parseDate } from '@internationalized/date';
import { CalendarRoot } from 'reka-ui';
import { computed } from 'vue';
import CalendarPanel from './CalendarPanel.vue';

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    /** Earliest selectable date, `YYYY-MM-DD`. */
    minDate: { type: String, default: null },
    /** Latest selectable date, `YYYY-MM-DD`. */
    maxDate: { type: String, default: null },
    /** Language of the month and weekday names. */
    locale: { type: String, default: 'id-ID' },
    /** Months shown side by side (stacked on small screens). */
    numberOfMonths: { type: Number, default: 1 },
    /** `(date: string) => boolean`: dates that can't be picked, e.g. holidays. */
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

const dateValue = computed({
    get: () => toCalendarDate(model.value),
    set: (value) => {
        model.value = value ? value.toString() : '';
    },
});

const minValue = computed(() => toCalendarDate(props.minDate));
const maxValue = computed(() => toCalendarDate(props.maxDate));

const unavailableMatcher = computed(() => (props.isDateUnavailable ? (date) => props.isDateUnavailable(date.toString()) : undefined));
</script>

<template>
    <CalendarRoot
        v-slot="{ weekDays, grid }"
        v-model="dateValue"
        :locale="locale"
        :min-value="minValue"
        :max-value="maxValue"
        :number-of-months="numberOfMonths"
        :is-date-unavailable="unavailableMatcher"
        :disabled="disabled"
        :readonly="readonly"
        :week-starts-on="1"
        weekday-format="short"
        fixed-weeks
        prevent-deselect
        class="w-fit rounded-xl border border-line bg-surface p-3 text-ink shadow-xs data-disabled:opacity-60"
    >
        <CalendarPanel :grid="grid" :week-days="weekDays" />
    </CalendarRoot>
</template>
