<script setup>
/**
 * Built on reka-ui FieldControl: the text control of a Field, styled like Input. Renders an <input>,
 * or a <textarea> with as="textarea". Its id, name, required/disabled state and aria attributes come
 * from the surrounding Field; it turns red while the field is invalid.
 *
 * Usage: <Field name="subject" label="Perihal" required>
 *            <FieldControl v-model="form.subject" placeholder="Undangan rapat koordinasi" />
 *        </Field>
 *
 * Every other attribute (type, placeholder, minlength, maxlength, pattern, autocomplete…) goes to the element.
 */
import { FieldControl } from 'reka-ui';

const model = defineModel({ type: [String, Number], default: '' });

defineProps({
    /** `input` or `textarea`. */
    as: { type: String, default: 'input' },
    /** Textarea only. */
    rows: { type: [String, Number], default: 4 },
});
</script>

<template>
    <FieldControl
        v-model="model"
        :as="as"
        :rows="as === 'textarea' ? rows : undefined"
        :class="[
            'w-full rounded-xl border border-line bg-surface px-3.5 text-sm text-ink shadow-xs outline-none transition',
            'placeholder:text-muted/80 focus:border-brand focus:ring-3 focus:ring-brand/15 disabled:cursor-not-allowed disabled:opacity-60',
            'data-invalid:border-danger data-invalid:focus:border-danger data-invalid:focus:ring-danger/15',
            as === 'textarea' ? 'py-2.5' : 'h-11',
        ]"
    />
</template>
