<script setup>
/**
 * Built on reka-ui DateRangeField: a start and an end date, each typed segment by segment, in one field.
 * Need a calendar too? Use DateRangePicker.
 *
 * The v-model is `{ start: 'YYYY-MM-DD', end: 'YYYY-MM-DD' }`; a date stays an empty string until
 * all of its segments are filled in. With `name`, hidden inputs `name[start]` and `name[end]` are
 * rendered for plain form submits.
 *
 * `format` tokens: DD / D = day, MM / M = month, YYYY = year; anything else is a separator.
 *
 * Usage: <DateRangeField v-model="form.period" label="Periode laporan" :error="form.errors['period.end']" />
 */
import { parseDate } from '@internationalized/date';
import { DateRangeFieldRoot } from 'reka-ui';
import { computed, useId } from 'vue';
import DateSegments from './DateSegments.vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

const model = defineModel({ type: Object, default: () => ({ start: '', end: '' }) });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Base name of the hidden inputs: `name[start]` and `name[end]`. */
    name: { type: String, default: undefined },
    /** Earliest valid date, `YYYY-MM-DD`. */
    minDate: { type: String, default: null },
    /** Latest valid date, `YYYY-MM-DD`. */
    maxDate: { type: String, default: null },
    /** Display format of both dates, see the tokens above. */
    format: { type: String, default: 'DD/MM/YYYY' },
    /** Language of the empty-segment placeholders. */
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

const rangeValue = computed({
    get: () => ({ start: toCalendarDate(model.value?.start), end: toCalendarDate(model.value?.end) }),
    set: (value) => {
        model.value = { start: value?.start?.toString() ?? '', end: value?.end?.toString() ?? '' };
    },
});

const minValue = computed(() => toCalendarDate(props.minDate));
const maxValue = computed(() => toCalendarDate(props.maxDate));
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="fieldId">{{ label }}</Label>

        <DateRangeFieldRoot
            :id="fieldId"
            v-slot="{ segments, isInvalid }"
            v-model="rangeValue"
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

            <DateSegments :segments="segments.start" :format="format" type="start" />
            <span class="px-2 text-muted" aria-hidden="true">–</span>
            <DateSegments :segments="segments.end" :format="format" type="end" />
        </DateRangeFieldRoot>

        <template v-if="name">
            <input type="hidden" :name="`${name}[start]`" :value="model?.start ?? ''" />
            <input type="hidden" :name="`${name}[end]`" :value="model?.end ?? ''" />
        </template>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
