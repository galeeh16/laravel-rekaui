<script setup>
/**
 * Built on reka-ui CheckboxGroup: several checkboxes sharing one array v-model, with arrow-key navigation.
 *
 * Usage: <CheckboxGroup
 *            v-model="form.channels"
 *            label="Kirim notifikasi lewat"
 *            name="channels"
 *            :options="[{ value: 'email', label: 'Email' }, { value: 'wa', label: 'WhatsApp', hint: 'Nomor terdaftar' }]"
 *        />
 *
 * Options are `{ value, label, hint?, disabled? }` or plain values such as `['A', 'B']`.
 * Use `max` to limit how many can be ticked; the rest are disabled once the limit is reached.
 */
import { CheckboxGroupRoot } from 'reka-ui';
import { computed, useId } from 'vue';
import Checkbox from './Checkbox.vue';

const model = defineModel({ type: Array, default: () => [] });

const props = defineProps({
    options: { type: Array, required: true },
    label: { type: String, default: null },
    name: { type: String, default: undefined },
    /** `vertical` stacks the options; `horizontal` puts them in a row from the sm breakpoint. */
    orientation: { type: String, default: 'vertical' },
    max: { type: Number, default: undefined },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const labelId = useId();

const normalizedOptions = computed(() =>
    props.options.map((option) => (typeof option === 'object' ? option : { value: option, label: String(option) })),
);
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <span v-if="label" :id="labelId" class="text-sm font-medium text-ink">{{ label }}</span>

        <CheckboxGroupRoot
            v-model="model"
            :name="name"
            :max="max"
            :disabled="disabled"
            :orientation="orientation"
            role="group"
            :aria-labelledby="label ? labelId : undefined"
            :class="['flex gap-3', orientation === 'horizontal' ? 'flex-col sm:flex-row sm:flex-wrap sm:gap-x-6' : 'flex-col']"
        >
            <Checkbox
                v-for="option in normalizedOptions"
                :key="option.value"
                :value="option.value"
                :disabled="option.disabled"
                :hint="option.hint"
                :aria-invalid="error ? 'true' : undefined"
            >
                {{ option.label }}
            </Checkbox>
        </CheckboxGroupRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
