<script setup>
/**
 * Built on reka-ui DateRangePicker: type the start and end dates segment by segment, or pick the
 * range in the calendar popover (first click = start, second click = end; it closes once both are set).
 *
 * The v-model is `{ start: 'YYYY-MM-DD', end: 'YYYY-MM-DD' }`; a date stays an empty string until it
 * is chosen. With `name`, hidden inputs `name[start]` and `name[end]` are rendered for plain form submits.
 *
 * `format` tokens: DD / D = day, MM / M = month, YYYY = year; anything else is a separator.
 *
 * Usage: <DateRangePicker v-model="form.period" label="Periode laporan" :number-of-months="2" :error="form.errors['period.end']" />
 */
import { parseDate } from '@internationalized/date';
import {
    DateRangePickerAnchor,
    DateRangePickerCalendar,
    DateRangePickerContent,
    DateRangePickerField,
    DateRangePickerRoot,
    DateRangePickerTrigger,
} from 'reka-ui';
import { computed, useId } from 'vue';
import CalendarPanel from './CalendarPanel.vue';
import DateSegments from './DateSegments.vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

const model = defineModel({ type: Object, default: () => ({ start: '', end: '' }) });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Base name of the hidden inputs: `name[start]` and `name[end]`. */
    name: { type: String, default: undefined },
    /** Earliest selectable date, `YYYY-MM-DD`. */
    minDate: { type: String, default: null },
    /** Latest selectable date, `YYYY-MM-DD`. */
    maxDate: { type: String, default: null },
    /** Longest range that can be picked, in days. */
    maximumDays: { type: Number, default: undefined },
    /** Months shown side by side in the popover (stacked on small screens). */
    numberOfMonths: { type: Number, default: 1 },
    /** Display format of both dates, see the tokens above. */
    format: { type: String, default: 'DD/MM/YYYY' },
    /** Language of the calendar (month and weekday names) and the empty-segment placeholders. */
    locale: { type: String, default: 'id-ID' },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const generatedId = useId();
const pickerId = computed(() => props.id ?? generatedId);

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

const navButtonClass =
    'inline-flex size-8 cursor-pointer items-center justify-center rounded-lg text-muted transition-colors hover:bg-ink/5 hover:text-ink focus-visible:outline-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-40';
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="pickerId">{{ label }}</Label>

        <DateRangePickerRoot
            :id="pickerId"
            v-model="rangeValue"
            :locale="locale"
            :min-value="minValue"
            :max-value="maxValue"
            :maximum-days="maximumDays"
            :number-of-months="numberOfMonths"
            :disabled="disabled"
            :week-starts-on="1"
            weekday-format="short"
            fixed-weeks
            close-on-select
        >
            <!-- Anchor the calendar to the whole field rather than the small calendar button. -->
            <DateRangePickerAnchor as-child>
                <DateRangePickerField
                    v-slot="{ segments }"
                    :aria-invalid="error ? 'true' : undefined"
                    :class="[
                        'flex h-11 w-full items-center rounded-xl border bg-surface pr-1.5 pl-3 text-sm text-ink shadow-xs transition',
                        'focus-within:ring-3 data-disabled:cursor-not-allowed data-disabled:opacity-60',
                        error
                            ? 'border-danger focus-within:border-danger focus-within:ring-danger/15'
                            : 'border-line focus-within:border-brand focus-within:ring-brand/15',
                    ]"
                >
                    <DateSegments :segments="segments.start" :format="format" type="start" />
                    <span class="px-2 text-muted" aria-hidden="true">–</span>
                    <DateSegments :segments="segments.end" :format="format" type="end" />

                    <DateRangePickerTrigger :class="['ml-auto', navButtonClass]" aria-label="Buka kalender">
                        <Icon name="calendar" class="size-4.5" />
                    </DateRangePickerTrigger>
                </DateRangePickerField>
            </DateRangePickerAnchor>

            <DateRangePickerContent
                :side-offset="6"
                align="start"
                class="z-50 origin-(--reka-popover-content-transform-origin) rounded-xl border border-line bg-surface p-3 text-ink shadow-lg shadow-black/8 outline-none data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
            >
                <DateRangePickerCalendar v-slot="{ weekDays, grid }">
                    <CalendarPanel range :grid="grid" :week-days="weekDays" />
                </DateRangePickerCalendar>
            </DateRangePickerContent>
        </DateRangePickerRoot>

        <template v-if="name">
            <input type="hidden" :name="`${name}[start]`" :value="model?.start ?? ''" />
            <input type="hidden" :name="`${name}[end]`" :value="model?.end ?? ''" />
        </template>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
