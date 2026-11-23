<script setup>
/**
 * Built on reka-ui Field: groups a label, one control, a description and the validation message, and wires up
 * ids, aria-describedby / aria-invalid and native constraint validation (required, type, minlength, pattern…).
 *
 * Usage:
 * <Field name="email" label="Email" description="Untuk notifikasi disposisi." validation-mode="onBlur" required>
 *     <FieldControl v-model="email" type="email" />
 * </Field>
 *
 * The control can be a FieldControl, Select or Checkbox. On its own a Field needs
 * `validation-mode="onBlur"` or `"onChange"`; inside a Form it validates on submit by default
 * and also shows the server error for its `name`.
 *
 * Failed native constraints get Indonesian messages (see `defaultMessages`); override them per constraint with
 * `:messages="{ valueMissing: 'Email wajib diisi.' }"`. Messages from `validate` and from the server are shown as they are.
 *
 * The default slot receives `{ invalid, errors, validity }`.
 */
import { FieldDescription, FieldError, FieldLabel, FieldRoot, FieldValidity } from 'reka-ui';

const props = defineProps({
    name: { type: String, default: undefined },
    label: { type: String, default: null },
    description: { type: String, default: null },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    /** Force the invalid state from your own logic. */
    invalid: { type: Boolean, default: undefined },
    /** Custom check `(value, formValues) => string | string[] | null`, may return a Promise. */
    validate: { type: Function, default: undefined },
    /** `onSubmit` (default, inside a Form), `onBlur` or `onChange`. */
    validationMode: { type: String, default: undefined },
    /** Debounce for `onChange` validation in ms, handy for async `validate`. */
    validationDebounceTime: { type: Number, default: undefined },
    /** Messages per failed constraint, e.g. `{ typeMismatch: 'Format email tidak valid.' }`. */
    messages: { type: Object, default: () => ({}) },
});

const defaultMessages = {
    valueMissing: 'Wajib diisi.',
    typeMismatch: 'Format tidak valid.',
    patternMismatch: 'Format tidak sesuai.',
    tooShort: 'Terlalu pendek.',
    tooLong: 'Terlalu panjang.',
    rangeUnderflow: 'Nilai terlalu kecil.',
    rangeOverflow: 'Nilai terlalu besar.',
    stepMismatch: 'Nilai tidak sesuai kelipatan.',
    badInput: 'Nilai tidak valid.',
};

/**
 * Native constraint failures get our (Indonesian) message instead of the browser's;
 * custom `validate` and server messages are already written by us, so they pass through.
 */
function errorMessages(validity, errors) {
    const failedConstraint = validity.customError ? null : Object.keys(defaultMessages).find((key) => validity[key]);

    return failedConstraint ? [props.messages[failedConstraint] ?? defaultMessages[failedConstraint]] : errors;
}
</script>

<template>
    <FieldRoot
        v-slot="{ invalid: isInvalid, errors }"
        :name="name"
        :required="required"
        :disabled="disabled"
        :invalid="invalid"
        :validate="validate"
        :validation-mode="validationMode"
        :validation-debounce-time="validationDebounceTime"
        class="flex flex-col gap-1.5"
    >
        <FieldValidity v-slot="{ validity }">
            <FieldLabel v-if="label" class="text-sm font-medium text-ink data-disabled:opacity-60">
                {{ label }}
                <span v-if="required" class="ml-0.5 text-danger" aria-hidden="true">*</span>
            </FieldLabel>

            <slot :invalid="isInvalid" :errors="errors" :validity="validity" />

            <FieldError v-slot="{ errors: shownErrors }" class="flex flex-col text-sm text-danger">
                <span v-for="message in errorMessages(validity, shownErrors)" :key="message">{{ message }}</span>
            </FieldError>

            <FieldDescription v-if="description && !isInvalid" class="text-sm text-muted">{{ description }}</FieldDescription>
        </FieldValidity>
    </FieldRoot>
</template>
