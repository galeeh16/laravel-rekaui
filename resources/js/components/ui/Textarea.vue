<script setup>
/**
 * Usage: <Textarea v-model="form.body" label="Isi surat" rows="6" :error="form.errors.body" />
 *
 * The `class` attribute is applied to the wrapper; every other attribute goes to the <textarea>.
 */
import { useAttrs, useId } from 'vue';
import Label from './Label.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    rows: { type: [String, Number], default: 4 },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const attrs = useAttrs();
const generatedId = useId();

const textareaId = () => props.id ?? generatedId;

function textareaAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <div :class="['flex flex-col gap-1.5', attrs.class]">
        <Label v-if="label" :for="textareaId()">{{ label }}</Label>

        <textarea
            :id="textareaId()"
            v-model="model"
            v-bind="textareaAttrs()"
            :rows="rows"
            :aria-invalid="error ? 'true' : undefined"
            :class="[
                'w-full rounded-xl border bg-surface px-3.5 py-2.5 text-sm text-ink shadow-xs outline-none transition',
                'placeholder:text-muted/80 focus:ring-3 disabled:cursor-not-allowed disabled:opacity-60',
                error
                    ? 'border-danger focus:border-danger focus:ring-danger/15'
                    : 'border-line focus:border-brand focus:ring-brand/15',
            ]"
        />

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
