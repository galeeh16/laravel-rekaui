<script setup>
/**
 * Built on reka-ui ColorSwatchPicker (a listbox of swatches: arrow keys move, Enter or click selects).
 * Sizes and shapes are the same as ColorSwatch. The selected swatch gets a brand ring and a check mark.
 *
 * Usage: <ColorSwatchPicker v-model="color" :colors="['#17795a', '#2d5bb0', '#c03a26']" />
 *        <ColorSwatchPicker v-model="colors" multiple label="Warna kategori" :colors="[{ value: '#17795a', label: 'Hijau brand' }]" />
 *
 * v-model is a lowercase hex string ('#17795a'), or an array of them with `multiple`.
 * Values are normalised, so '#17795A' or 'rgb(23, 121, 90)' still select the '#17795a' swatch.
 * The `class` attribute is applied to the wrapper; every other attribute goes to the listbox.
 */
import { ColorSwatchPickerItem, ColorSwatchPickerItemIndicator, ColorSwatchPickerRoot, colorToHex, isValidColor, parseColor } from 'reka-ui';
import { computed, useAttrs, useId } from 'vue';
import ColorSwatch from './ColorSwatch.vue';
import Icon from './Icon.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: [String, Array], default: undefined });

const props = defineProps({
    /** `['#17795a', ...]` or `[{ value: '#17795a', label: 'Hijau brand' }, ...]`. */
    colors: { type: Array, required: true },
    multiple: { type: Boolean, default: false },
    label: { type: String, default: null },
    size: { type: String, default: 'md' },
    shape: { type: String, default: 'circle' },
    disabled: { type: Boolean, default: false },
});

const attrs = useAttrs();
const labelId = useId();

/** Lowercase hex, so differently written colours compare equal; unparsable values pass through. */
function toHex(value) {
    return typeof value === 'string' && isValidColor(value) ? colorToHex(parseColor(value)) : value;
}

const options = computed(() =>
    props.colors.map((color) => {
        const option = typeof color === 'object' ? color : { value: color, label: null };

        return { value: toHex(option.value), label: option.label ?? null };
    }),
);

const selected = computed({
    get: () => (props.multiple ? (Array.isArray(model.value) ? model.value : []).map(toHex) : toHex(model.value)),
    set: (value) => (model.value = value),
});

function listboxAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <div :class="['flex flex-col gap-2', attrs.class]">
        <span v-if="label" :id="labelId" class="text-sm font-medium text-ink">{{ label }}</span>

        <ColorSwatchPickerRoot
            v-bind="listboxAttrs()"
            v-model="selected"
            :multiple="multiple"
            :disabled="disabled"
            :selection-behavior="multiple ? 'toggle' : 'replace'"
            :aria-labelledby="label ? labelId : undefined"
            class="flex flex-wrap gap-3 outline-none data-disabled:opacity-50"
        >
            <ColorSwatchPickerItem
                v-for="option in options"
                :key="option.value"
                :value="option.value"
                v-bind="option.label ? { 'aria-label': option.label } : {}"
                :title="option.label ?? option.value"
                class="group/swatch flex cursor-pointer outline-none data-disabled:cursor-not-allowed"
            >
                <ColorSwatch
                    :color="option.value"
                    :size="size"
                    :shape="shape"
                    aria-hidden="true"
                    class="transition-shadow group-focus-visible/swatch:outline-2 group-focus-visible/swatch:outline-offset-4 group-focus-visible/swatch:outline-brand group-data-[state=checked]/swatch:ring-2 group-data-[state=checked]/swatch:ring-brand group-data-[state=checked]/swatch:ring-offset-2 group-data-[state=checked]/swatch:ring-offset-surface"
                >
                    <ColorSwatchPickerItemIndicator class="inline-flex text-white in-data-[color-contrast=dark]:text-black/75">
                        <Icon name="check" :class="size === 'sm' ? 'size-3' : 'size-4'" stroke-width="3" />
                    </ColorSwatchPickerItemIndicator>
                </ColorSwatch>
            </ColorSwatchPickerItem>
        </ColorSwatchPickerRoot>
    </div>
</template>
