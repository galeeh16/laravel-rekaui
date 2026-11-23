<script setup>
/**
 * Built on reka-ui ColorField (type a hex value, or a single channel as a number).
 * The value is committed on Enter or blur; invalid input reverts to the last valid colour.
 * Arrow Up/Down step the value (Page Up/Down by 10, Home/End to min/max); the mouse wheel does too while focused.
 *
 * Usage: <ColorField v-model="form.brand_color" label="Warna brand" :error="form.errors.brand_color" />
 *        <ColorField v-model="color" channel="hue" label="Hue" />
 *
 * v-model is a hex string such as '#17795a' (reka-ui emits lowercase hex).
 * The `class` attribute is applied to the wrapper; every other attribute goes to the <input>.
 */
import { ColorFieldInput, ColorFieldRoot, isValidColor } from 'reka-ui';
import { computed, useAttrs, useId } from 'vue';
import ColorSwatch from './ColorSwatch.vue';
import Label from './Label.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: String, default: undefined });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    /** Edit one channel (hue, saturation, lightness, brightness, red, green, blue, alpha) instead of the hex value. */
    channel: { type: String, default: null },
    /** Only matters for `channel="saturation"`: HSL (default) or HSB saturation. */
    colorSpace: { type: String, default: 'hsl' },
    defaultValue: { type: String, default: '#17795a' },
    name: { type: String, default: undefined },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const attrs = useAttrs();
const generatedId = useId();

const inputId = computed(() => props.id ?? generatedId);

/** reka-ui throws on strings it can't parse, so fall back to the default colour. */
const currentColor = computed(() => (model.value && isValidColor(model.value) ? model.value : props.defaultValue));

function inputAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <div :class="['flex flex-col gap-1.5', attrs.class]">
        <Label v-if="label" :for="inputId">{{ label }}</Label>

        <ColorFieldRoot
            :model-value="currentColor"
            :channel="channel ?? undefined"
            :color-space="colorSpace"
            :name="name"
            :disabled="disabled"
            :readonly="readonly"
            class="relative"
            @update:model-value="model = $event"
        >
            <ColorSwatch
                :color="currentColor"
                size="sm"
                aria-hidden="true"
                class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2"
            />

            <ColorFieldInput
                :id="inputId"
                v-bind="inputAttrs()"
                :aria-invalid="error ? 'true' : undefined"
                :class="[
                    'h-11 w-full rounded-xl border bg-surface pr-3.5 pl-10 text-sm text-ink tabular-nums shadow-xs outline-none transition',
                    'placeholder:text-muted/80 focus:ring-3 disabled:cursor-not-allowed disabled:opacity-60',
                    error
                        ? 'border-danger focus:border-danger focus:ring-danger/15'
                        : 'border-line focus:border-brand focus:ring-brand/15',
                ]"
            />
        </ColorFieldRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
