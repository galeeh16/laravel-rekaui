<script setup>
/**
 * Built on reka-ui DateField: a date typed segment by segment (day, month, year), without a calendar.
 * Arrow keys change the focused segment, Left/Right move between segments. Need a calendar too? Use DatePicker.
 *
 * The v-model is always a `YYYY-MM-DD` string (ready to send to Laravel), whatever the display format.
 * It stays empty until every segment is filled in.
 *
 * `format` tokens:
 *   DD = day, 2 digits (01)     D = day (1)
 *   MM = month, 2 digits (01)   M = month (1)
 *   YYYY = year
 * Anything else is shown as a separator, e.g. 'DD/MM/YYYY' (default), 'DD-MM-YYYY', 'YYYY-MM-DD'.
 *
 * Usage: <DateField v-model="form.letter_date" label="Tanggal surat" name="letter_date" :error="form.errors.letter_date" />
 */
import { parseDate } from '@internationalized/date';
import { DateFieldRoot } from 'reka-ui';
import { computed, useId } from 'vue';
import DateSegments from './DateSegments.vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Name of the hidden input (value `YYYY-MM-DD`) for plain form submits. */
    name: { type: String, default: undefined },
    /** Earliest valid date, `YYYY-MM-DD`. */
    minDate: { type: String, default: null },
    /** Latest valid date, `YYYY-MM-DD`. */
    maxDate: { type: String, default: null },
    /** Display format, see the tokens above. */
    format: { type: String, default: 'DD/MM/YYYY' },
    /** Language of the empty-segment placeholders (e.g. hh/bb/tttt). */
    locale: { type: String, default: 'id-ID' },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const generatedId = useId();
const fieldId = computed(() => props.id ?? generatedId);

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
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="fieldId">{{ label }}</Label>

        <DateFieldRoot
            :id="fieldId"
            v-slot="{ segments, isInvalid }"
            v-model="dateValue"
            :name="name"
            :locale="locale"
            :min-value="minValue"
            :max-value="maxValue"
            :disabled="disabled"
            :readonly="readonly"
            :aria-invalid="error ? 'true' : undefined"
            :class="[
                'flex h-11 w-full items-center rounded-xl border bg-surface px-3 text-sm text-ink shadow-xs transition',
                'focus-within:ring-3 data-disabled:cursor-not-allowed data-disabled:opacity-60',
                error
                    ? 'border-danger focus-within:border-danger focus-within:ring-danger/15'
                    : 'border-line focus-within:border-brand focus-within:ring-brand/15',
            ]"
        >
            <Icon name="calendar" :class="['mr-1.5 size-4.5', isInvalid ? 'text-danger' : 'text-muted']" />

            <DateSegments :segments="segments" :format="format" />
        </DateFieldRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
