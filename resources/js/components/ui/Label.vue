<script setup>
/**
 * Built on reka-ui Label (a native <label> that doesn't select text on double click).
 *
 * Usage: <Label for="subject">Perihal</Label>
 *        <Label for="number" required>Nomor surat</Label>   (adds a red asterisk)
 *
 * Form wrappers such as Input, Select and Textarea already render a Label through their `label` prop.
 */
import { Label } from 'reka-ui';

defineProps({
    /** Show a red asterisk after the text. Visual only: set `required` on the control too. */
    required: { type: Boolean, default: false },
});
</script>

<template>
    <Label class="text-sm font-medium text-ink">
        <slot />
        <span v-if="required" class="ml-0.5 text-danger" aria-hidden="true">*</span>
    </Label>
</template>
