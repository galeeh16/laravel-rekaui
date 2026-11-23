<script setup>
/**
 * Built on reka-ui NumberField: − / + buttons (hold to repeat), arrow keys, Page Up/Down (×10),
 * Home/End (min/max) and the mouse wheel while focused. Values are formatted with Intl.NumberFormat
 * for `locale` (default id-ID, e.g. 1.250,5), and `format-options` can show currency, percent or units.
 *
 * Usage: <NumberField v-model="form.copies" label="Jumlah rangkap" :min="1" :max="10" />
 *        <NumberField v-model="form.fee" label="Biaya" :step="1000" :format-options="{ style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }" />
 *
 * The `class` attribute is applied to the wrapper; every other attribute goes to the <input>.
 */
import { NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput, NumberFieldRoot } from 'reka-ui';
import { computed, useAttrs, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

defineOptions({ inheritAttrs: false });

/** `undefined` when the field is empty. */
const model = defineModel({ type: Number, default: undefined });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    name: { type: String, default: undefined },
    min: { type: Number, default: undefined },
    max: { type: Number, default: undefined },
    step: { type: Number, default: 1 },
    /** Intl.NumberFormat options, e.g. `{ style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }`. */
    formatOptions: { type: Object, default: undefined },
    locale: { type: String, default: 'id-ID' },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const attrs = useAttrs();
const generatedId = useId();

const fieldId = computed(() => props.id ?? generatedId);

function inputAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}

const stepButtonClass =
    'inline-flex size-9 shrink-0 cursor-pointer items-center justify-center rounded-lg text-muted transition-colors hover:bg-ink/5 hover:text-ink disabled:pointer-events-none disabled:opacity-40';
</script>

<template>
    <div :class="['flex flex-col gap-1.5', attrs.class]">
        <Label v-if="label" :for="fieldId">{{ label }}</Label>

        <NumberFieldRoot
            :id="fieldId"
            v-model="model"
            :name="name"
            :min="min"
            :max="max"
            :step="step"
            :format-options="formatOptions"
            :locale="locale"
            :disabled="disabled"
            :readonly="readonly"
            :class="[
                'flex h-11 w-full items-center gap-1 rounded-xl border bg-surface px-1 text-sm text-ink shadow-xs transition',
                'focus-within:ring-3 data-disabled:cursor-not-allowed data-disabled:opacity-60',
                error
                    ? 'border-danger focus-within:border-danger focus-within:ring-danger/15'
                    : 'border-line focus-within:border-brand focus-within:ring-brand/15',
            ]"
        >
            <NumberFieldDecrement :class="stepButtonClass" aria-label="Kurangi">
                <Icon name="minus" class="size-4" />
            </NumberFieldDecrement>

            <NumberFieldInput
                v-bind="inputAttrs()"
                :aria-invalid="error ? 'true' : undefined"
                class="h-full min-w-0 flex-1 bg-transparent text-center tabular-nums outline-none placeholder:text-muted/80 disabled:cursor-not-allowed"
            />

            <NumberFieldIncrement :class="stepButtonClass" aria-label="Tambah">
                <Icon name="plus" class="size-4" />
            </NumberFieldIncrement>
        </NumberFieldRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
