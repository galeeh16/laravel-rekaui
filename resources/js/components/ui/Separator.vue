<script setup>
/**
 * Built on reka-ui Separator (role="separator" with aria-orientation, or purely decorative).
 * A vertical separator stretches to the height of its flex parent.
 *
 * Usage: <Separator />
 *        <Separator orientation="vertical" decorative />
 *        <Separator label="atau" />   (text in the middle; always decorative)
 */
import { Separator } from 'reka-ui';

defineProps({
    orientation: { type: String, default: 'horizontal' },
    /** Hide it from assistive technology when it is only visual. */
    decorative: { type: Boolean, default: false },
    label: { type: String, default: null },
});
</script>

<template>
    <div v-if="label || $slots.default" class="flex w-full items-center gap-3 text-xs font-medium text-muted">
        <Separator decorative class="h-px flex-1 bg-line" />
        <span class="shrink-0"><slot>{{ label }}</slot></span>
        <Separator decorative class="h-px flex-1 bg-line" />
    </div>

    <Separator
        v-else
        :orientation="orientation"
        :decorative="decorative"
        :class="['shrink-0 bg-line', orientation === 'vertical' ? 'w-px self-stretch' : 'h-px w-full']"
    />
</template>
