<script setup>
/**
 * One resizable panel inside a Splitter. Sizes are percentages of the group by default.
 * Default slot props: { isCollapsed, isExpanded, collapse, expand, resize }.
 * Events: @resize(size, previousSize), @collapse, @expand.
 * A template ref exposes collapse(), expand(), resize(size) and isCollapsed().
 *
 * Usage: <SplitterPanel :default-size="30" :min-size="20" collapsible :collapsed-size="0">...</SplitterPanel>
 */
import { SplitterPanel } from 'reka-ui';
import { useTemplateRef } from 'vue';

defineProps({
    defaultSize: { type: Number, default: undefined },
    minSize: { type: Number, default: undefined },
    maxSize: { type: Number, default: undefined },
    /** Allow collapsing to `collapsedSize` when dragged below `minSize`. */
    collapsible: { type: Boolean, default: false },
    collapsedSize: { type: Number, default: undefined },
    /** Unit for every size prop: '%' (default) or 'px'. */
    sizeUnit: { type: String, default: undefined },
    /** Needed when panels are added or removed conditionally. */
    id: { type: String, default: undefined },
    order: { type: Number, default: undefined },
});

const panel = useTemplateRef('panel');

defineExpose({
    collapse: () => panel.value?.collapse(),
    expand: () => panel.value?.expand(),
    resize: (size) => panel.value?.resize(size),
    isCollapsed: () => panel.value?.isCollapsed ?? false,
});
</script>

<template>
    <SplitterPanel
        :id="id"
        ref="panel"
        v-slot="slotProps"
        :default-size="defaultSize"
        :min-size="minSize"
        :max-size="maxSize"
        :collapsible="collapsible"
        :collapsed-size="collapsedSize"
        :size-unit="sizeUnit"
        :order="order"
    >
        <slot v-bind="slotProps" />
    </SplitterPanel>
</template>
