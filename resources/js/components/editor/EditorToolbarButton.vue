<script setup>
/**
 * Square button inside the document editor toolbar (reka-ui ToolbarButton, so arrow keys move between
 * buttons). `label` is used as the accessible name and native tooltip. Pass `toggle` for buttons that
 * switch a format on and off, so `active` is announced as pressed.
 *
 * Usage: <EditorToolbarButton icon="bold" label="Tebal (⌘B)" toggle :active="editor.isActive('bold')" @click="..." />
 *        <EditorToolbarButton label="Warna teks"><Icon name="baseline" class="size-4" /></EditorToolbarButton>
 */
import { ToolbarButton } from 'reka-ui';
import Icon from '@/components/ui/Icon.vue';

defineProps({
    icon: { type: String, default: null },
    label: { type: String, required: true },
    active: { type: Boolean, default: false },
    toggle: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <ToolbarButton
        :disabled="disabled"
        :aria-label="label"
        :aria-pressed="toggle ? active : undefined"
        :title="label"
        :data-active="active || undefined"
        class="relative inline-flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-lg text-ink transition-colors hover:bg-ink/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-40 data-active:bg-brand-soft data-active:text-brand-ink data-[state=open]:bg-ink/5"
    >
        <slot>
            <Icon :name="icon" class="size-4" />
        </slot>
    </ToolbarButton>
</template>
