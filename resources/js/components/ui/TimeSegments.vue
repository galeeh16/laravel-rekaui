<script setup>
/**
 * Internal building block: the editable hour / minute (/ second, AM-PM) segments of a time field,
 * in the locale's order and with its separators (e.g. `09.30` for id-ID). Shared by TimeField and
 * TimeRangeField.
 *
 * Render it inside a reka-ui TimeFieldRoot, or inside a TimeRangeFieldRoot with `type="start"` or `type="end"`.
 *
 * Usage:
 * <TimeFieldRoot v-slot="{ segments }" v-model="time">
 *     <TimeSegments :segments="segments" />
 * </TimeFieldRoot>
 */
import { TimeFieldInput, TimeRangeFieldInput } from 'reka-ui';

defineProps({
    /** `segments` slot prop of the field root (for a range: `segments.start` or `segments.end`). */
    segments: { type: Array, required: true },
    /** Range fields only: which time these segments edit, `start` or `end`. */
    type: { type: String, default: null },
});

const segmentClass =
    'rounded-md px-1 py-0.5 tabular-nums outline-none focus:bg-brand-soft focus:text-brand-ink data-placeholder:text-muted/80';
</script>

<template>
    <template v-for="(segment, index) in segments" :key="`${segment.part}-${index}`">
        <span v-if="segment.part === 'literal'" class="whitespace-pre text-muted" aria-hidden="true">{{ segment.value }}</span>

        <TimeRangeFieldInput v-else-if="type" :part="segment.part" :type="type" :class="segmentClass">
            {{ segment.value }}
        </TimeRangeFieldInput>

        <TimeFieldInput v-else :part="segment.part" :class="segmentClass">
            {{ segment.value }}
        </TimeFieldInput>
    </template>
</template>
