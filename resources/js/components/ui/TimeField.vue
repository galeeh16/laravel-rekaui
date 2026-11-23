<script setup>
/**
 * Built on reka-ui TimeField: a time typed segment by segment (hour, minute, optionally second).
 * Arrow Up/Down change the focused segment, Left/Right move between segments.
 *
 * The v-model is an `HH:mm` string (24-hour, `HH:mm:ss` with granularity="second"), ready for Laravel's
 * `date_format:H:i`. It stays empty until every segment is filled in. How the field *looks*
 * (separator, 12/24-hour) follows `locale` and `hour-cycle`; the v-model format does not change.
 *
 * Usage: <TimeField v-model="form.start_time" label="Jam mulai" min-time="07:00" max-time="17:00" :minute-step="15" />
 */
import { parseTime } from '@internationalized/date';
import { TimeFieldRoot } from 'reka-ui';
import { computed, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';
import TimeSegments from './TimeSegments.vue';

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Name of a hidden input carrying the v-model value, for plain form submits. */
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

/** `HH:mm`, or `HH:mm:ss` when seconds are editable. */
function toTimeString(time) {
    const pad = (number) => String(number).padStart(2, '0');
    const hoursAndMinutes = `${pad(time.hour)}:${pad(time.minute)}`;

    return props.granularity === 'second' ? `${hoursAndMinutes}:${pad(time.second)}` : hoursAndMinutes;
}

const timeValue = computed({
    get: () => toTime(model.value),
    set: (value) => {
        model.value = value ? toTimeString(value) : '';
    },
});

const minValue = computed(() => toTime(props.minTime));
const maxValue = computed(() => toTime(props.maxTime));
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="fieldId">{{ label }}</Label>

        <TimeFieldRoot
            :id="fieldId"
            v-slot="{ segments, isInvalid }"
            v-model="timeValue"
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

            <TimeSegments :segments="segments" />
        </TimeFieldRoot>

        <input v-if="name" type="hidden" :name="name" :value="model" />

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
