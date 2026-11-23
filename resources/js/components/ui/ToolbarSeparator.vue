<script setup>
/**
 * Divider between groups of controls in a Toolbar (reka-ui ToolbarSeparator).
 * Drawn as a vertical line in a horizontal toolbar and a horizontal line in a vertical one.
 *
 * Usage: <ToolbarSeparator />
 */
import { ToolbarSeparator } from 'reka-ui';
</script>

<template>
    <ToolbarSeparator
        class="shrink-0 bg-line data-[orientation=horizontal]:mx-1 data-[orientation=horizontal]:h-5 data-[orientation=horizontal]:w-px data-[orientation=vertical]:my-1 data-[orientation=vertical]:h-px data-[orientation=vertical]:w-full"
    />
</template>
