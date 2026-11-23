<script setup>
/**
 * Built on reka-ui Checkbox.
 *
 * Usage: <Checkbox v-model="form.remember" name="remember">Ingat saya</Checkbox>
 *        <Checkbox v-model="selectAll" hint="Termasuk surat yang diarsipkan">Pilih semua</Checkbox>
 *
 * The v-model is `true`, `false` or `'indeterminate'` (a dash, for "some selected").
 * Inside a CheckboxGroup, pass `value` instead of v-model. Inside a Field, the box turns red when the field is invalid.
 * The `class` attribute is applied to the wrapper <label>; every other attribute (name, value,
 * disabled, required, aria-*) goes to the checkbox itself.
 */
import { CheckboxIndicator, CheckboxRoot } from 'reka-ui';
import { useAttrs } from 'vue';
import Icon from './Icon.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: [Boolean, String], default: false });

defineProps({
    /** Muted text under the label. */
    hint: { type: String, default: null },
    /** Error message under the label; also turns the box red. */
    error: { type: String, default: null },
});

const attrs = useAttrs();

function checkboxAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <label
        :class="[
            'inline-flex cursor-pointer items-start gap-2.5 text-sm text-ink has-aria-disabled:cursor-not-allowed has-aria-disabled:opacity-60',
            attrs.class,
        ]"
    >
        <CheckboxRoot
            v-slot="{ state }"
            v-model="model"
            v-bind="checkboxAttrs()"
            :aria-invalid="error ? 'true' : attrs['aria-invalid']"
            class="mt-px inline-flex size-4.5 shrink-0 cursor-pointer items-center justify-center rounded-[5px] border border-line bg-surface text-white shadow-xs transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:cursor-not-allowed aria-invalid:border-danger data-[state=checked]:border-brand data-[state=checked]:bg-brand data-[state=indeterminate]:border-brand data-[state=indeterminate]:bg-brand"
        >
            <CheckboxIndicator class="inline-flex">
                <Icon :name="state === 'indeterminate' ? 'minus' : 'check'" class="size-3.5" stroke-width="3" />
            </CheckboxIndicator>
        </CheckboxRoot>

        <span v-if="$slots.default || hint || error" class="flex flex-col gap-0.5">
            <span v-if="$slots.default"><slot /></span>
            <span v-if="error" class="text-danger">{{ error }}</span>
            <span v-else-if="hint" class="text-muted">{{ hint }}</span>
        </span>
    </label>
</template>
