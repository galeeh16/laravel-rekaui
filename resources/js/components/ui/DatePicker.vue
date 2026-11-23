<script setup>
/**
 * Built on reka-ui DatePicker: type the date segment by segment, or pick it from the calendar.
 * The v-model is always a `YYYY-MM-DD` string (ready to send to Laravel), whatever the display format.
 *
 * `format` controls how the field is displayed. Tokens:
 *   DD = day, 2 digits (01)     D = day (1)
 *   MM = month, 2 digits (01)   M = month (1)
 *   YYYY = year
 * Anything else is shown as a separator, e.g. 'DD/MM/YYYY' (default), 'DD-MM-YYYY', 'YYYY-MM-DD', 'MM/DD/YYYY'.
 *
 * Usage: <DatePicker v-model="form.birth_date" label="Tanggal lahir" format="DD-MM-YYYY" :max-date="'2026-11-22'" />
 */
import { parseDate } from '@internationalized/date';
import { DatePickerAnchor, DatePickerCalendar, DatePickerContent, DatePickerField, DatePickerRoot, DatePickerTrigger } from 'reka-ui';
import { computed, useId } from 'vue';
import CalendarPanel from './CalendarPanel.vue';
import DateSegments from './DateSegments.vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    name: { type: String, default: undefined },
    /** Earliest selectable date, `YYYY-MM-DD`. */
    minDate: { type: String, default: null },
    /** Latest selectable date, `YYYY-MM-DD`. */
    maxDate: { type: String, default: null },
    /** Display format, see the tokens above. */
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

const dateValue = computed({
    get: () => toCalendarDate(model.value),
    set: (value) => {
        model.value = value ? value.toString() : '';
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

        <DatePickerRoot
            :id="pickerId"
            v-model="dateValue"
            :name="name"
            :locale="locale"
            :min-value="minValue"
            :max-value="maxValue"
            :disabled="disabled"
            :week-starts-on="1"
            weekday-format="short"
            fixed-weeks
            close-on-select
            prevent-deselect
        >
            <!-- Anchor the calendar to the whole field rather than the small calendar button. -->
            <DatePickerAnchor as-child>
                <DatePickerField
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
                    <DateSegments :segments="segments" :format="format" />

                    <DatePickerTrigger :class="['ml-auto', navButtonClass]" aria-label="Buka kalender">
                        <Icon name="calendar" class="size-4.5" />
                    </DatePickerTrigger>
                </DatePickerField>
            </DatePickerAnchor>

            <DatePickerContent
                :side-offset="6"
                align="start"
                class="z-50 origin-(--reka-popover-content-transform-origin) rounded-xl border border-line bg-surface p-3 text-ink shadow-lg shadow-black/8 outline-none data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
            >
                <DatePickerCalendar v-slot="{ weekDays, grid }">
                    <CalendarPanel :grid="grid" :week-days="weekDays" />
                </DatePickerCalendar>
            </DatePickerContent>
        </DatePickerRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
