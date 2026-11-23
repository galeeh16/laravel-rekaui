<script setup>
/**
 * Built on reka-ui ScrollArea: native scrolling with a slim, theme-coloured scrollbar.
 * Give it a height or max-height through `class` (e.g. h-72 or max-h-72).
 * Orientation: vertical (default), horizontal, both
 * Type: hover (default), auto, always, scroll, glimpse
 *
 * Usage:
 * <ScrollArea class="h-72 rounded-2xl border border-line">
 *     ...long content...
 * </ScrollArea>
 */
import { ScrollAreaCorner, ScrollAreaRoot, ScrollAreaScrollbar, ScrollAreaThumb, ScrollAreaViewport } from 'reka-ui';

defineProps({
    orientation: { type: String, default: 'vertical' },
    /** When the scrollbar is visible: hover, auto, always, scroll, glimpse. */
    type: { type: String, default: 'hover' },
    /** Milliseconds before the scrollbar hides (type hover / scroll). */
    scrollHideDelay: { type: Number, default: 600 },
});
</script>

<template>
    <ScrollAreaRoot :type="type" :scroll-hide-delay="scrollHideDelay" class="relative overflow-hidden">
        <!-- max-h-[inherit] lets a max-height on the root limit the viewport too. -->
        <ScrollAreaViewport class="size-full max-h-[inherit] rounded-[inherit]">
            <slot />
        </ScrollAreaViewport>

        <ScrollAreaScrollbar
            v-if="orientation !== 'horizontal'"
            orientation="vertical"
            class="flex w-2.5 touch-none p-0.5 select-none data-[state=hidden]:animate-fade-out data-[state=visible]:animate-fade-in"
        >
            <ScrollAreaThumb class="relative flex-1 rounded-full bg-ink/20 transition-colors hover:bg-ink/35" />
        </ScrollAreaScrollbar>

        <ScrollAreaScrollbar
            v-if="orientation !== 'vertical'"
            orientation="horizontal"
            class="flex h-2.5 touch-none flex-col p-0.5 select-none data-[state=hidden]:animate-fade-out data-[state=visible]:animate-fade-in"
        >
            <ScrollAreaThumb class="relative flex-1 rounded-full bg-ink/20 transition-colors hover:bg-ink/35" />
        </ScrollAreaScrollbar>

        <ScrollAreaCorner v-if="orientation === 'both'" />
    </ScrollAreaRoot>
</template>
