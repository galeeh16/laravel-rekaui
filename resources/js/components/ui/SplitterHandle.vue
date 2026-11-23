<script setup>
/**
 * Drag handle between two SplitterPanels (built on reka-ui SplitterResizeHandle).
 * Focus it and use the arrow keys to resize from the keyboard.
 *
 * Usage: <SplitterHandle />   ·   <SplitterHandle :with-grip="false" />
 */
import { SplitterResizeHandle } from 'reka-ui';
import Icon from './Icon.vue';

defineProps({
    /** Show the small grip in the middle of the line. */
    withGrip: { type: Boolean, default: true },
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <SplitterResizeHandle
        :disabled="disabled"
        class="group relative flex shrink-0 items-center justify-center bg-line transition-colors outline-none data-[orientation=horizontal]:w-px data-[orientation=vertical]:h-px data-[state=drag]:bg-brand data-[state=hover]:bg-brand/50 focus-visible:bg-brand data-disabled:pointer-events-none"
    >
        <span
            v-if="withGrip"
            class="z-10 flex h-6 w-3.5 items-center justify-center rounded-md border border-line bg-surface text-muted shadow-xs transition-colors group-focus-visible:outline-2 group-focus-visible:outline-offset-2 group-focus-visible:outline-brand group-data-[orientation=vertical]:rotate-90 group-data-[state=drag]:border-brand group-data-[state=drag]:text-brand"
        >
            <Icon name="grip-vertical" class="size-3" />
        </span>
    </SplitterResizeHandle>
</template>
