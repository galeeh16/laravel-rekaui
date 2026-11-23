<script setup>
/**
 * Built on reka-ui Accordion (keyboard navigation, animated height).
 *
 * Usage:
 * <Accordion default-value="item-1">
 *     <AccordionItem value="item-1" title="Pertanyaan pertama">Jawaban...</AccordionItem>
 *     <AccordionItem value="item-2" title="Pertanyaan kedua">Jawaban...</AccordionItem>
 * </Accordion>
 *
 * Use type="multiple" (with an array v-model / default-value) to allow several open panels.
 */
import { AccordionRoot } from 'reka-ui';

const model = defineModel({ type: [String, Array], default: undefined });

defineProps({
    type: { type: String, default: 'single' },
    /** Single mode only: allow closing the open panel. */
    collapsible: { type: Boolean, default: true },
    defaultValue: { type: [String, Array], default: undefined },
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <AccordionRoot
        v-model="model"
        :type="type"
        :collapsible="collapsible"
        :default-value="defaultValue"
        :disabled="disabled"
        class="w-full divide-y divide-line overflow-hidden rounded-2xl border border-line bg-surface shadow-xs"
    >
        <slot />
    </AccordionRoot>
</template>
