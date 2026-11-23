<script setup>
/**
 * Internal building block: the editable day / month / year segments of a date field, laid out by a
 * display `format`. Shared by DateField, DatePicker, DateRangeField and DateRangePicker.
 *
 * Format tokens:
 *   DD = day, 2 digits (01)     D = day (1)
 *   MM = month, 2 digits (01)   M = month (1)
 *   YYYY = year
 * Anything else is shown as a separator, e.g. 'DD/MM/YYYY', 'DD-MM-YYYY', 'YYYY-MM-DD', 'MM/DD/YYYY'.
 *
 * Render it inside a reka-ui DateFieldRoot / DatePickerField, or inside a DateRangeFieldRoot /
 * DateRangePickerField with `type="start"` or `type="end"`.
 *
 * Usage:
 * <DateFieldRoot v-slot="{ segments }" v-model="date">
 *     <DateSegments :segments="segments" format="DD/MM/YYYY" />
 * </DateFieldRoot>
 */
import { DateFieldInput, DateRangeFieldInput } from 'reka-ui';
import { computed } from 'vue';

const props = defineProps({
    /** `segments` slot prop of the field root (for a range: `segments.start` or `segments.end`). */
    segments: { type: Array, required: true },
    /** Display format, see the tokens above. */
    format: { type: String, default: 'DD/MM/YYYY' },
    /** Range fields only: which date these segments edit, `start` or `end`. */
    type: { type: String, default: null },
});

const formatTokenParts = {
    YYYY: { part: 'year', pad: false },
    DD: { part: 'day', pad: true },
    D: { part: 'day', pad: false },
    MM: { part: 'month', pad: true },
    M: { part: 'month', pad: false },
};

/**
 * Split the format into segments and separators, e.g. 'DD/MM/YYYY' →
 * [day, '/', month, '/', year]. reka-ui moves between segments in DOM order,
 * so rendering them in this order also drives keyboard navigation.
 */
const formatTokens = computed(() =>
    props.format
        .split(/(YYYY|DD|D|MM|M)/)
        .filter((token) => token !== '')
        .map((token) => (formatTokenParts[token] ? { type: 'segment', ...formatTokenParts[token] } : { type: 'literal', value: token })),
);

/** Text for a segment; single digits 1–9 get a leading zero when the token asks for it (DD, MM). */
function segmentText(token) {
    const value = props.segments.find((segment) => segment.part === token.part)?.value ?? '';

    return token.pad && /^[1-9]$/.test(value) ? `0${value}` : value;
}

const segmentClass =
    'rounded-md px-1 py-0.5 tabular-nums outline-none focus:bg-brand-soft focus:text-brand-ink data-placeholder:text-muted/80';
</script>

<template>
    <template v-for="(token, index) in formatTokens" :key="index">
        <span v-if="token.type === 'literal'" class="px-0.5 whitespace-pre text-muted" aria-hidden="true">{{ token.value }}</span>

        <DateRangeFieldInput v-else-if="type" :part="token.part" :type="type" :class="segmentClass">
            {{ segmentText(token) }}
        </DateRangeFieldInput>

        <DateFieldInput v-else :part="token.part" :class="segmentClass">
            {{ segmentText(token) }}
        </DateFieldInput>
    </template>
</template>
