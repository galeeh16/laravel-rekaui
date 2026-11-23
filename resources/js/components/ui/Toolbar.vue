<script setup>
/**
 * Built on reka-ui Toolbar: one Tab stop for the whole bar, arrow keys move between its controls.
 * Fill it with ToolbarButton, ToolbarToggleGroup and ToolbarSeparator.
 *
 * Usage:
 * <Toolbar aria-label="Format surat">
 *     <ToolbarToggleGroup v-model="formats" type="multiple" icon-only :options="[{ value: 'bold', label: 'Tebal', icon: 'bold' }]" />
 *     <ToolbarSeparator />
 *     <ToolbarButton icon="link" aria-label="Sisipkan tautan" />
 *     <ToolbarButton variant="primary" icon="send" class="ml-auto">Kirim</ToolbarButton>
 * </Toolbar>
 */
import { ToolbarRoot } from 'reka-ui';

defineProps({
    orientation: { type: String, default: 'horizontal' },
    /** Arrow keys wrap from the last control back to the first. */
    loop: { type: Boolean, default: true },
});
</script>

<template>
    <ToolbarRoot
        :orientation="orientation"
        :loop="loop"
        class="flex flex-wrap items-center gap-1 rounded-xl border border-line bg-surface p-1 shadow-xs data-[orientation=vertical]:w-fit data-[orientation=vertical]:flex-col data-[orientation=vertical]:items-stretch"
    >
        <slot />
    </ToolbarRoot>
</template>
