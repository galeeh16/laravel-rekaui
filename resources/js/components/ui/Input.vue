<script setup>
/**
 * Usage: <Input v-model="form.email" label="Email" type="email" :error="form.errors.email" />
 *        <Input v-model="search" type="search" icon="search" kbd="⌘K" placeholder="Cari..." />
 *
 * The `class` attribute is applied to the wrapper; every other attribute goes to the <input>.
 */
import { useAttrs, useId, useTemplateRef } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: [String, Number], default: '' });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    icon: { type: String, default: null },
    kbd: { type: String, default: null },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const attrs = useAttrs();
const generatedId = useId();
const input = useTemplateRef('input');

const inputId = () => props.id ?? generatedId;

function inputAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}

defineExpose({
    focus: () => input.value?.focus(),
    select: () => input.value?.select(),
});
</script>

<template>
    <div :class="['flex flex-col gap-1.5', attrs.class]">
        <Label v-if="label" :for="inputId()">{{ label }}</Label>

        <div class="relative">
            <Icon
                v-if="icon"
                :name="icon"
                class="pointer-events-none absolute top-1/2 left-3.5 size-4.5 -translate-y-1/2 text-muted"
            />

            <input
                :id="inputId()"
                ref="input"
                v-model="model"
                v-bind="inputAttrs()"
                :aria-invalid="error ? 'true' : undefined"
                :class="[
                    'h-11 w-full rounded-xl border bg-surface text-sm text-ink shadow-xs outline-none transition',
                    'placeholder:text-muted/80 focus:ring-3 disabled:cursor-not-allowed disabled:opacity-60',
                    '[&::-webkit-search-cancel-button]:hidden',
                    icon ? 'pl-10' : 'pl-3.5',
                    kbd ? 'pr-14' : 'pr-3.5',
                    error
                        ? 'border-danger focus:border-danger focus:ring-danger/15'
                        : 'border-line focus:border-brand focus:ring-brand/15',
                ]"
            />

            <kbd
                v-if="kbd"
                class="pointer-events-none absolute top-1/2 right-2.5 hidden -translate-y-1/2 rounded-md border border-line bg-canvas px-1.5 py-0.5 font-sans text-xs font-medium text-muted sm:block"
            >{{ kbd }}</kbd>
        </div>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
