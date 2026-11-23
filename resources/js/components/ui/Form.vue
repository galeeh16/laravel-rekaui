<script setup>
/**
 * Built on reka-ui Form: a <form> that validates every Field on submit, focuses the first invalid one and
 * only emits `submit` when everything is valid. The browser's own submit is always prevented, so send the
 * data yourself (e.g. with Inertia's useForm).
 *
 * Usage:
 * <Form :errors="form.errors" @submit="form.post(route('letters.store'))">
 *     <Field name="subject" label="Perihal" required>
 *         <FieldControl v-model="form.subject" />
 *     </Field>
 *     <Button type="submit" :disabled="form.processing">Simpan</Button>
 * </Form>
 *
 * `errors` takes server-side errors keyed by field name (Inertia's `form.errors`): each Field shows the message
 * for its `name` until the user edits it, and the first one gets focus.
 * Validation modes: onSubmit (default; after the first submit fields re-validate while typing), onBlur, onChange.
 */
import { FormRoot } from 'reka-ui';
import { useTemplateRef } from 'vue';

defineProps({
    /** Server errors, `{ name: 'Pesan' }` or `{ name: ['Pesan'] }`. */
    errors: { type: Object, default: undefined },
    validationMode: { type: String, default: 'onSubmit' },
});

const emit = defineEmits({
    /** All fields are valid: `(values, event)`, values of every named Field. */
    submit: null,
});

const formRoot = useTemplateRef('formRoot');

defineExpose({
    /** Validate every field, or only the one with this name; returns true when valid. */
    validate: (name) => formRoot.value?.validate(name) ?? false,
});
</script>

<template>
    <FormRoot
        ref="formRoot"
        :errors="errors"
        :validation-mode="validationMode"
        class="flex flex-col gap-5"
        @form-submit="(values, event) => emit('submit', values, event)"
    >
        <slot />
    </FormRoot>
</template>
