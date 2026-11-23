<script setup>
/**
 * Internal building block: the calendar header (year and month navigation) and the month grid(s).
 * Shared by Calendar and DatePicker (single date), and RangeCalendar and DateRangePicker (`range`).
 *
 * Render it inside a reka-ui CalendarRoot / DatePickerCalendar, or a RangeCalendarRoot /
 * DateRangePickerCalendar when `range` is set, and pass the `grid` and `weekDays` slot props along.
 * (The DatePicker* and DateRangePicker* calendar parts are thin aliases of the Calendar* and
 * RangeCalendar* parts, so the same markup works in all four.)
 *
 * Usage:
 * <CalendarRoot v-slot="{ grid, weekDays }" v-model="date">
 *     <CalendarPanel :grid="grid" :week-days="weekDays" />
 * </CalendarRoot>
 */
import {
    CalendarCell,
    CalendarCellTrigger,
    CalendarGrid,
    CalendarGridBody,
    CalendarGridHead,
    CalendarGridRow,
    CalendarHeadCell,
    CalendarHeader,
    CalendarHeading,
    CalendarNext,
    CalendarPrev,
    RangeCalendarCell,
    RangeCalendarCellTrigger,
    RangeCalendarGrid,
    RangeCalendarGridBody,
    RangeCalendarGridHead,
    RangeCalendarGridRow,
    RangeCalendarHeadCell,
    RangeCalendarHeader,
    RangeCalendarHeading,
    RangeCalendarNext,
    RangeCalendarPrev,
} from 'reka-ui';
import { computed } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    /** `grid` slot prop of the calendar root: one entry per visible month. */
    grid: { type: Array, required: true },
    /** `weekDays` slot prop of the calendar root: the weekday names for the header row. */
    weekDays: { type: Array, required: true },
    /** Use the RangeCalendar parts and the range styling (start, end and the days in between). */
    range: { type: Boolean, default: false },
});

const singleParts = {
    Header: CalendarHeader,
    Heading: CalendarHeading,
    Prev: CalendarPrev,
    Next: CalendarNext,
    Grid: CalendarGrid,
    GridHead: CalendarGridHead,
    GridBody: CalendarGridBody,
    GridRow: CalendarGridRow,
    HeadCell: CalendarHeadCell,
    Cell: CalendarCell,
    CellTrigger: CalendarCellTrigger,
};

const rangeParts = {
    Header: RangeCalendarHeader,
    Heading: RangeCalendarHeading,
    Prev: RangeCalendarPrev,
    Next: RangeCalendarNext,
    Grid: RangeCalendarGrid,
    GridHead: RangeCalendarGridHead,
    GridBody: RangeCalendarGridBody,
    GridRow: RangeCalendarGridRow,
    HeadCell: RangeCalendarHeadCell,
    Cell: RangeCalendarCell,
    CellTrigger: RangeCalendarCellTrigger,
};

const parts = computed(() => (props.range ? rangeParts : singleParts));

const previousYear = (date) => date.subtract({ years: 1 });
const nextYear = (date) => date.add({ years: 1 });

const navButtonClass =
    'inline-flex size-8 cursor-pointer items-center justify-center rounded-lg text-muted transition-colors hover:bg-ink/5 hover:text-ink focus-visible:outline-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-40';

/**
 * Day button. `not-data-selected:data-today:text-brand` keeps today's number readable when it is
 * also the selected day (white on the brand background).
 */
const dayClass = [
    'flex size-9 cursor-pointer items-center justify-center rounded-lg text-sm tabular-nums outline-none transition-colors',
    'hover:bg-ink/5 focus-visible:ring-2 focus-visible:ring-brand',
    'data-disabled:pointer-events-none data-disabled:text-muted/40',
    'data-unavailable:cursor-not-allowed data-unavailable:text-danger/70 data-unavailable:line-through',
    'data-today:font-semibold not-data-selected:data-today:text-brand',
];

const singleDayClass = [...dayClass, 'data-outside-view:text-muted/50 data-selected:bg-brand data-selected:text-white data-selected:hover:bg-brand-hover'];

/**
 * Range: the band behind the days lives on the cell (`aria-selected` = inside the chosen range,
 * `data-highlighted` = the preview while choosing the end), the start/end days are solid.
 * Days of the neighbouring months are hidden so the band never shows twice.
 */
const rangeCellClass = [
    'not-has-data-outside-view:aria-selected:bg-brand-soft not-has-data-outside-view:has-data-highlighted:bg-brand-soft',
    'first:rounded-l-lg last:rounded-r-lg',
    'has-data-selection-start:rounded-l-lg has-data-selection-end:rounded-r-lg has-data-highlighted-start:rounded-l-lg has-data-highlighted-end:rounded-r-lg',
];

const rangeDayClass = [
    ...dayClass,
    'data-outside-view:invisible data-highlighted:text-brand-ink data-selected:text-brand-ink',
    'data-selection-start:bg-brand data-selection-start:text-white data-selection-start:hover:bg-brand-hover',
    'data-selection-end:bg-brand data-selection-end:text-white data-selection-end:hover:bg-brand-hover',
];
</script>

<template>
    <component :is="parts.Header" class="flex items-center gap-0.5">
        <component :is="parts.Prev" :prev-page="previousYear" :class="navButtonClass" aria-label="Tahun sebelumnya">
            <Icon name="chevrons-left" class="size-4" />
        </component>
        <component :is="parts.Prev" :class="navButtonClass" aria-label="Bulan sebelumnya">
            <Icon name="chevron-left" class="size-4" />
        </component>

        <component :is="parts.Heading" class="flex-1 text-center text-sm font-medium" />

        <component :is="parts.Next" :class="navButtonClass" aria-label="Bulan berikutnya">
            <Icon name="chevron-right" class="size-4" />
        </component>
        <component :is="parts.Next" :next-page="nextYear" :class="navButtonClass" aria-label="Tahun berikutnya">
            <Icon name="chevrons-right" class="size-4" />
        </component>
    </component>

    <div class="mt-3 flex flex-col gap-x-6 gap-y-4 sm:flex-row">
        <component :is="parts.Grid" v-for="month in grid" :key="month.value.toString()" class="w-full border-collapse select-none">
            <component :is="parts.GridHead">
                <component :is="parts.GridRow" class="flex">
                    <component
                        :is="parts.HeadCell"
                        v-for="day in weekDays"
                        :key="day"
                        class="flex size-9 items-center justify-center text-xs font-normal text-muted"
                    >
                        {{ day }}
                    </component>
                </component>
            </component>

            <component :is="parts.GridBody">
                <component :is="parts.GridRow" v-for="(week, index) in month.rows" :key="`week-${index}`" class="flex">
                    <component :is="parts.Cell" v-for="day in week" :key="day.toString()" :date="day" :class="range ? rangeCellClass : null">
                        <component :is="parts.CellTrigger" :day="day" :month="month.value" :class="range ? rangeDayClass : singleDayClass" />
                    </component>
                </component>
            </component>
        </component>
    </div>
</template>
