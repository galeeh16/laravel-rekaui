<script setup>
/**
 * A button inside a Toolbar (reka-ui ToolbarButton rendering our Button). Without slot text it becomes
 * an icon-only square button, so give it an aria-label.
 *
 * Usage: <ToolbarButton icon="image" aria-label="Sisipkan gambar" />
 *        <ToolbarButton variant="primary" icon="send">Kirim</ToolbarButton>
 */
import { ToolbarButton } from 'reka-ui';
import { useSlots } from 'vue';
import Button from './Button.vue';

defineProps({
    icon: { type: String, default: null },
    /** Any Button variant; `ghost` blends into the toolbar. */
    variant: { type: String, default: 'ghost' },
    disabled: { type: Boolean, default: false },
});

const slots = useSlots();
</script>

<template>
    <ToolbarButton as-child :disabled="disabled">
        <Button :variant="variant" :size="slots.default ? 'sm' : 'icon-sm'" :icon="icon">
            <slot />
        </Button>
    </ToolbarButton>
</template>
