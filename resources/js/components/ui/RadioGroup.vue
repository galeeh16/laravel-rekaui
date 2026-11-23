<script setup>
/**
 * Built on reka-ui RadioGroup (arrow-key navigation between options).
 * Variants: default (plain radios), card (bordered tiles)
 *
 * Usage: <RadioGroup v-model="form.gender" label="Jenis kelamin" :options="[{ value: 'male', label: 'Laki-laki' }]" variant="card" />
 *
 * Options are `{ value, label, hint?, disabled? }` or plain values such as `['Biasa', 'Segera']`.
 */
import { RadioGroupIndicator, RadioGroupItem, RadioGroupRoot } from 'reka-ui';
import { computed, useId } from 'vue';

const model = defineModel({ type: [String, Number], default: null });

const props = defineProps({
    options: { type: Array, required: true },
    label: { type: String, default: null },
    name: { type: String, default: undefined },
    orientation: { type: String, default: 'horizontal' },
    variant: { type: String, default: 'default' },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
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

        <RadioGroupRoot
            v-model="model"
            :name="name"
            :orientation="orientation"
            :disabled="disabled"
            :required="required"
            :aria-labelledby="label ? labelId : undefined"
            :aria-invalid="error ? 'true' : undefined"
            :class="[
                'flex',
                orientation === 'horizontal' ? 'flex-col sm:flex-row' : 'flex-col',
                variant === 'default' && orientation === 'vertical' ? 'gap-3' : 'gap-2',
                variant === 'default' && orientation === 'horizontal' ? 'sm:gap-6' : '',
            ]"
        >
            <label
                v-for="option in normalizedOptions"
                :key="option.value"
                :class="[
                    'inline-flex cursor-pointer gap-2.5 text-sm text-ink has-data-disabled:cursor-not-allowed has-data-disabled:opacity-60',
                    option.hint ? 'items-start' : 'items-center',
                    variant === 'card' &&
                        'min-h-11 rounded-xl border bg-surface px-3.5 py-2.5 shadow-xs transition-colors hover:bg-canvas has-data-[state=checked]:border-brand has-data-[state=checked]:bg-brand-soft has-data-[state=checked]:text-brand-ink',
                    variant === 'card' && orientation === 'horizontal' && 'sm:flex-1',
                    variant === 'card' && (error ? 'border-danger' : 'border-line'),
                ]"
            >
                <RadioGroupItem
                    :value="option.value"
                    :disabled="option.disabled"
                    :class="[
                        'inline-flex size-4.5 shrink-0 cursor-pointer items-center justify-center rounded-full border bg-surface shadow-xs transition-colors',
                        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand data-[state=checked]:border-brand',
                        option.hint && 'mt-px',
                        error ? 'border-danger' : 'border-line',
                    ]"
                >
                    <RadioGroupIndicator class="size-2 rounded-full bg-brand" />
                </RadioGroupItem>

                <span v-if="option.hint" class="flex flex-col gap-0.5">
                    <span>{{ option.label }}</span>
                    <span class="text-muted">{{ option.hint }}</span>
                </span>
                <template v-else>{{ option.label }}</template>
            </label>
        </RadioGroupRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
