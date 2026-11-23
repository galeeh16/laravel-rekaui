<script setup>
/**
 * Built on reka-ui TimeRangeField: a start and an end time, each typed segment by segment, in one field.
 *
 * The v-model is `{ start: 'HH:mm', end: 'HH:mm' }` (24-hour, `HH:mm:ss` with granularity="second");
 * a time stays an empty string until all of its segments are filled in. With `name`, hidden inputs
 * `name[start]` and `name[end]` are rendered for plain form submits.
 *
 * Usage: <TimeRangeField v-model="form.meeting_time" label="Jam rapat" min-time="07:00" max-time="17:00" />
 */
import { parseTime } from '@internationalized/date';
import { TimeRangeFieldRoot } from 'reka-ui';
import { computed, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';
import TimeSegments from './TimeSegments.vue';

const model = defineModel({ type: Object, default: () => ({ start: '', end: '' }) });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Base name of the hidden inputs: `name[start]` and `name[end]`. */
    name: { type: String, default: undefined },
    /** Earliest valid time, `HH:mm`. */
    minTime: { type: String, default: null },
    /** Latest valid time, `HH:mm`. */
    maxTime: { type: String, default: null },
    /** Smallest editable unit: `hour`, `minute` or `second`. */
    granularity: { type: String, default: 'minute' },
    /** Display only: 24 (13.30) or 12 (1.30 PM). */
    hourCycle: { type: Number, default: 24 },
    /** Minutes added or removed per Arrow Up/Down press, e.g. 15. */
    minuteStep: { type: Number, default: 1 },
    /** Language of the separators and the empty-segment placeholders. */
    locale: { type: String, default: 'id-ID' },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const generatedId = useId();
const fieldId = computed(() => props.id ?? generatedId);

function toTime(value) {
    try {
        return value ? parseTime(value) : undefined;
    } catch {
        return undefined;
    }
}

/** `HH:mm`, or `HH:mm:ss` when seconds are editable; empty string for a missing time. */
function toTimeString(time) {
    if (!time) {
        return '';
    }

    const pad = (number) => String(number).padStart(2, '0');
    const hoursAndMinutes = `${pad(time.hour)}:${pad(time.minute)}`;

    return props.granularity === 'second' ? `${hoursAndMinutes}:${pad(time.second)}` : hoursAndMinutes;
}

const rangeValue = computed({
    get: () => ({ start: toTime(model.value?.start), end: toTime(model.value?.end) }),
    set: (value) => {
        model.value = { start: toTimeString(value?.start), end: toTimeString(value?.end) };
    },
});

const minValue = computed(() => toTime(props.minTime));
const maxValue = computed(() => toTime(props.maxTime));
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="fieldId">{{ label }}</Label>

        <TimeRangeFieldRoot
            :id="fieldId"
            v-slot="{ segments, isInvalid }"
            v-model="rangeValue"
            :locale="locale"
            :min-value="minValue"
            :max-value="maxValue"
            :granularity="granularity"
            :hour-cycle="hourCycle"
            :step="{ minute: minuteStep }"
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
            <Icon name="clock" :class="['mr-1.5 size-4.5', isInvalid ? 'text-danger' : 'text-muted']" />

            <TimeSegments :segments="segments.start" type="start" />
            <span class="px-2 text-muted" aria-hidden="true">–</span>
            <TimeSegments :segments="segments.end" type="end" />
        </TimeRangeFieldRoot>

        <template v-if="name">
            <input type="hidden" :name="`${name}[start]`" :value="model?.start ?? ''" />
            <input type="hidden" :name="`${name}[end]`" :value="model?.end ?? ''" />
        </template>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
