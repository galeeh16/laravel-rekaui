<script setup>
/**
 * One panel of an Accordion: header button (`title` prop or #title slot) and content (default slot).
 */
import { AccordionContent, AccordionHeader, AccordionItem, AccordionTrigger } from 'reka-ui';
import Icon from './Icon.vue';

defineProps({
    value: { type: String, required: true },
    title: { type: String, default: null },
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <AccordionItem :value="value" :disabled="disabled">
        <AccordionHeader class="flex">
            <AccordionTrigger
                class="group flex flex-1 cursor-pointer items-center justify-between gap-4 px-5 py-4 text-left text-sm font-medium text-ink transition-colors hover:bg-ink/2 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand data-disabled:cursor-not-allowed data-disabled:opacity-50"
            >
                <slot name="title">{{ title }}</slot>
                <Icon name="chevron-down" class="size-4 text-muted transition-transform duration-200 group-data-[state=open]:rotate-180" />
            </AccordionTrigger>
        </AccordionHeader>

        <AccordionContent
            class="overflow-hidden text-sm text-muted data-[state=closed]:animate-accordion-up data-[state=open]:animate-accordion-down"
        >
            <div class="px-5 pb-4">
                <slot />
            </div>
        </AccordionContent>
    </AccordionItem>
</template>
