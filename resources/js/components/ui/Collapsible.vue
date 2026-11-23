<script setup>
/**
 * Built on reka-ui Collapsible (animated height, aria-expanded / aria-controls wiring).
 * Variants: card (bordered panel with a header button, default), plain (no frame, compact trigger)
 *
 * Usage:
 * <Collapsible title="Riwayat disposisi" description="3 catatan">
 *     ...content...
 * </Collapsible>
 *
 * Custom trigger: the #trigger slot is rendered as-child, so pass one focusable element (e.g. a Button).
 * <Collapsible v-model:open="showAll" variant="plain">
 *     <template #trigger="{ open }">
 *         <Button variant="ghost" size="sm">{{ open ? 'Sembunyikan' : 'Tampilkan semua' }}</Button>
 *     </template>
 *     ...content...
 * </Collapsible>
 */
import { CollapsibleContent, CollapsibleRoot, CollapsibleTrigger } from 'reka-ui';
import Icon from './Icon.vue';

const open = defineModel('open', { type: Boolean, default: undefined });

defineProps({
    title: { type: String, default: null },
    description: { type: String, default: null },
    icon: { type: String, default: null },
    variant: { type: String, default: 'card' },
    defaultOpen: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    /** Remove the content from the DOM while closed (set false to keep it mounted, e.g. for forms). */
    unmountOnHide: { type: Boolean, default: true },
});
</script>

<template>
    <CollapsibleRoot
        v-slot="{ open: isOpen }"
        v-model:open="open"
        :default-open="defaultOpen"
        :disabled="disabled"
        :unmount-on-hide="unmountOnHide"
        :class="variant === 'card' ? 'rounded-2xl border border-line bg-surface shadow-xs' : 'flex flex-col'"
    >
        <CollapsibleTrigger v-if="$slots.trigger" as-child>
            <slot name="trigger" :open="isOpen" />
        </CollapsibleTrigger>

        <CollapsibleTrigger
            v-else
            :class="[
                'group flex w-full cursor-pointer items-center text-left text-sm font-medium text-ink transition-colors',
                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand data-disabled:cursor-not-allowed data-disabled:opacity-50',
                variant === 'card' ? 'gap-3 rounded-2xl px-5 py-4 hover:bg-ink/2' : 'gap-2 rounded-lg py-1.5 hover:text-brand',
            ]"
        >
            <Icon v-if="icon" :name="icon" class="size-4.5 text-muted" />
            <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                <span class="truncate"><slot name="title">{{ title }}</slot></span>
                <span v-if="description" class="font-normal text-muted">{{ description }}</span>
            </span>
            <Icon name="chevron-down" class="size-4 text-muted transition-transform duration-200 group-data-[state=open]:rotate-180" />
        </CollapsibleTrigger>

        <CollapsibleContent
            class="overflow-hidden data-[state=closed]:animate-collapsible-up data-[state=open]:animate-collapsible-down"
        >
            <div :class="variant === 'card' ? 'px-5 pb-5' : 'pt-2'">
                <slot />
            </div>
        </CollapsibleContent>
    </CollapsibleRoot>
</template>
