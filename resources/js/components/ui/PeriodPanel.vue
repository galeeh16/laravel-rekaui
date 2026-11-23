<script setup>
/**
 * Internal building block: a 3 × 4 grid of months (one year per page) or years (twelve per page),
 * with previous/next navigation. Built on reka-ui MonthPicker, MonthRangePicker, YearPicker and
 * YearRangePicker, picked by `view` + `range`. Used by PeriodPicker.
 *
 * The v-model is an @internationalized/date value (`{ start, end }` with `range`); the public
 * wrappers (MonthPicker, YearPicker, …) convert it to and from strings.
 *
 * Usage: <PeriodPanel v-model="calendarDate" view="year" :min-value="minValue" />
 */
import {
    MonthPickerCell,
    MonthPickerCellTrigger,
    MonthPickerGrid,
    MonthPickerGridBody,
    MonthPickerGridRow,
    MonthPickerHeader,
    MonthPickerHeading,
    MonthPickerNext,
    MonthPickerPrev,
    MonthPickerRoot,
    MonthRangePickerCell,
    MonthRangePickerCellTrigger,
    MonthRangePickerGrid,
    MonthRangePickerGridBody,
    MonthRangePickerGridRow,
    MonthRangePickerHeader,
    MonthRangePickerHeading,
    MonthRangePickerNext,
    MonthRangePickerPrev,
    MonthRangePickerRoot,
    YearPickerCell,
    YearPickerCellTrigger,
    YearPickerGrid,
    YearPickerGridBody,
    YearPickerGridRow,
    YearPickerHeader,
    YearPickerHeading,
    YearPickerNext,
    YearPickerPrev,
    YearPickerRoot,
    YearRangePickerCell,
    YearRangePickerCellTrigger,
    YearRangePickerGrid,
    YearRangePickerGridBody,
    YearRangePickerGridRow,
    YearRangePickerHeader,
    YearRangePickerHeading,
    YearRangePickerNext,
    YearRangePickerPrev,
    YearRangePickerRoot,
} from 'reka-ui';
import { computed } from 'vue';
import Icon from './Icon.vue';

const model = defineModel({ type: Object, default: undefined });

const props = defineProps({
    /** `month` (a year of months per page) or `year` (twelve years per page). */
    view: { type: String, default: 'month' },
    /** Pick a start and an end instead of a single value. */
    range: { type: Boolean, default: false },
    minValue: { type: Object, default: undefined },
    maxValue: { type: Object, default: undefined },
    /** Range only: the longest range, in months (month view) or years (year view). */
    maximum: { type: Number, default: undefined },
    locale: { type: String, default: 'id-ID' },
    disabled: { type: Boolean, default: false },
});

const monthLabels = { calendarLabel: 'Pilih bulan', prevLabel: 'Tahun sebelumnya', nextLabel: 'Tahun berikutnya' };
const yearLabels = { calendarLabel: 'Pilih tahun', prevLabel: 'Tahun-tahun sebelumnya', nextLabel: 'Tahun-tahun berikutnya' };

/** reka-ui parts per view; the cell trigger takes its date as `month` or `year`. */
const partsByView = {
    month: {
        ...monthLabels,
        cellProp: 'month',
        Root: MonthPickerRoot,
        Header: MonthPickerHeader,
        Heading: MonthPickerHeading,
        Prev: MonthPickerPrev,
        Next: MonthPickerNext,
        Grid: MonthPickerGrid,
        GridBody: MonthPickerGridBody,
        GridRow: MonthPickerGridRow,
        Cell: MonthPickerCell,
        CellTrigger: MonthPickerCellTrigger,
    },
    'month-range': {
        ...monthLabels,
        cellProp: 'month',
        Root: MonthRangePickerRoot,
        Header: MonthRangePickerHeader,
        Heading: MonthRangePickerHeading,
        Prev: MonthRangePickerPrev,
        Next: MonthRangePickerNext,
        Grid: MonthRangePickerGrid,
        GridBody: MonthRangePickerGridBody,
        GridRow: MonthRangePickerGridRow,
        Cell: MonthRangePickerCell,
        CellTrigger: MonthRangePickerCellTrigger,
    },
    year: {
        ...yearLabels,
        cellProp: 'year',
        Root: YearPickerRoot,
        Header: YearPickerHeader,
        Heading: YearPickerHeading,
        Prev: YearPickerPrev,
        Next: YearPickerNext,
        Grid: YearPickerGrid,
        GridBody: YearPickerGridBody,
        GridRow: YearPickerGridRow,
        Cell: YearPickerCell,
        CellTrigger: YearPickerCellTrigger,
    },
    'year-range': {
        ...yearLabels,
        cellProp: 'year',
        Root: YearRangePickerRoot,
        Header: YearRangePickerHeader,
        Heading: YearRangePickerHeading,
        Prev: YearRangePickerPrev,
        Next: YearRangePickerNext,
        Grid: YearRangePickerGrid,
        GridBody: YearRangePickerGridBody,
        GridRow: YearRangePickerGridRow,
        Cell: YearRangePickerCell,
        CellTrigger: YearRangePickerCellTrigger,
    },
};

const parts = computed(() => partsByView[props.range ? `${props.view}-range` : props.view]);

/** Single pickers can't be cleared by clicking the selected cell; ranges take a view-specific maximum length. */
const rootProps = computed(() => {
    const common = {
        locale: props.locale,
        minValue: props.minValue,
        maxValue: props.maxValue,
        disabled: props.disabled,
        calendarLabel: parts.value.calendarLabel,
    };

    if (!props.range) {
        return { ...common, preventDeselect: true };
    }

    return { ...common, [props.view === 'year' ? 'maximumYears' : 'maximumMonths']: props.maximum };
});

const navButtonClass =
    'inline-flex size-8 cursor-pointer items-center justify-center rounded-lg text-muted transition-colors hover:bg-ink/5 hover:text-ink focus-visible:outline-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-40';

const triggerClass = [
    'flex h-10 w-16 cursor-pointer items-center justify-center rounded-lg text-sm tabular-nums outline-none transition-colors',
    'hover:bg-ink/5 focus-visible:ring-2 focus-visible:ring-brand',
    'data-disabled:pointer-events-none data-disabled:text-muted/40',
    'data-unavailable:cursor-not-allowed data-unavailable:text-danger/70 data-unavailable:line-through',
    'data-today:font-semibold not-data-selected:data-today:text-brand',
];

const singleTriggerClass = [...triggerClass, 'data-selected:bg-brand data-selected:text-white data-selected:hover:bg-brand-hover'];

/** Range: the band lives on the cell (`aria-selected` = in range, `data-highlighted` = preview), start/end are solid. */
const rangeCellClass = [
    'aria-selected:bg-brand-soft has-data-highlighted:bg-brand-soft first:rounded-l-lg last:rounded-r-lg',
    'has-data-selection-start:rounded-l-lg has-data-selection-end:rounded-r-lg has-data-highlighted-start:rounded-l-lg has-data-highlighted-end:rounded-r-lg',
];

const rangeTriggerClass = [
    ...triggerClass,
    'data-highlighted:text-brand-ink data-selected:text-brand-ink',
    'data-selection-start:bg-brand data-selection-start:text-white data-selection-start:hover:bg-brand-hover',
    'data-selection-end:bg-brand data-selection-end:text-white data-selection-end:hover:bg-brand-hover',
];
</script>

<template>
    <component :is="parts.Root" v-slot="{ grid }" v-model="model" v-bind="rootProps">
        <component :is="parts.Header" class="flex items-center gap-0.5">
            <component :is="parts.Prev" :class="navButtonClass" :aria-label="parts.prevLabel">
                <Icon name="chevron-left" class="size-4" />
            </component>

            <component :is="parts.Heading" class="flex-1 text-center text-sm font-medium tabular-nums" />

            <component :is="parts.Next" :class="navButtonClass" :aria-label="parts.nextLabel">
                <Icon name="chevron-right" class="size-4" />
            </component>
        </component>

        <component :is="parts.Grid" class="mt-3 w-full border-collapse select-none">
            <component :is="parts.GridBody" class="flex flex-col gap-1">
                <component :is="parts.GridRow" v-for="(row, index) in grid.rows" :key="`row-${index}`" class="flex">
                    <component :is="parts.Cell" v-for="cell in row" :key="cell.toString()" :date="cell" :class="range ? rangeCellClass : null">
                        <component
                            :is="parts.CellTrigger"
                            v-bind="{ [parts.cellProp]: cell }"
                            :class="range ? rangeTriggerClass : singleTriggerClass"
                        />
                    </component>
                </component>
            </component>
        </component>
    </component>
</template>
