<script setup>
/**
 * Built on reka-ui Splitter: resizable panels (drag a handle, or focus it and use the arrow keys).
 * The `class` attribute goes to an outer box that the panels fill, so give it a height there.
 * Nested splitters: put another <Splitter class="h-full"> inside a SplitterPanel.
 *
 * Usage:
 * <Splitter class="h-80 rounded-2xl border border-line">
 *     <SplitterPanel :default-size="30" :min-size="20">...</SplitterPanel>
 *     <SplitterHandle />
 *     <SplitterPanel :default-size="70">...</SplitterPanel>
 * </Splitter>
 */
import { SplitterGroup } from 'reka-ui';

defineProps({
    direction: { type: String, default: 'horizontal' },
    /** Remember the layout in localStorage under this key. */
    autoSaveId: { type: String, default: null },
    /** Percentage moved per arrow-key press on a handle. */
    keyboardResizeBy: { type: Number, default: 10 },
});

/** `layout`: panel sizes (percent) after every resize. */
const emit = defineEmits(['layout']);
</script>

<template>
    <div class="overflow-hidden">
        <SplitterGroup
            :direction="direction"
            :auto-save-id="autoSaveId"
            :keyboard-resize-by="keyboardResizeBy"
            @layout="(sizes) => emit('layout', sizes)"
        >
            <slot />
        </SplitterGroup>
    </div>
</template>
