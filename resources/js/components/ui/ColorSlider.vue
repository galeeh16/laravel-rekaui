<script setup>
/**
 * Built on reka-ui ColorSlider (one colour channel on a gradient track; arrow keys step, Shift/Page keys step by 10).
 * Channels: hue, saturation, lightness, brightness, red, green, blue, alpha.
 *
 * Usage: <ColorSlider v-model="color" channel="hue" label="Hue" />
 *        <ColorSlider v-model="color" channel="alpha" label="Opasitas" />
 *
 * v-model is a hex string such as '#17795a'; the alpha channel produces 8-digit hex ('#17795a80').
 * reka-ui draws the track gradient left-to-right only, so this wrapper is horizontal.
 * The `class` attribute is applied to the wrapper; every other attribute goes to the slider root.
 */
import { ColorSliderRoot, ColorSliderThumb, ColorSliderTrack, colorToHex, convertToHsb, getChannelValue, isValidColor, normalizeColor } from 'reka-ui';
import { computed, ref, useAttrs, useId } from 'vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: String, default: undefined });

const props = defineProps({
    channel: { type: String, default: 'hue' },
    /** Only matters for `saturation`: HSL (default) or HSB saturation. */
    colorSpace: { type: String, default: 'hsl' },
    defaultValue: { type: String, default: '#17795a' },
    /** Shown above the track together with the channel value. */
    label: { type: String, default: null },
    disabled: { type: Boolean, default: false },
});

const attrs = useAttrs();
const labelId = useId();

const channelUnits = { hue: '°', saturation: '%', lightness: '%', brightness: '%', alpha: '%' };

/** reka-ui throws on strings it can't parse, so fall back to the default colour. */
const currentColor = computed(() => (model.value && isValidColor(model.value) ? model.value : props.defaultValue));

/** Last colour object emitted by the slider. Unlike a hex string it keeps the hue of greys. */
const lastSliderColor = ref(null);

const channelValueText = computed(() => {
    const emitted = lastSliderColor.value;
    let color = emitted && colorToHex(emitted) === currentColor.value.toLowerCase() ? emitted : normalizeColor(currentColor.value);

    if (props.channel === 'brightness' || (props.channel === 'saturation' && props.colorSpace === 'hsb')) {
        color = convertToHsb(color);
    }

    return `${Math.round(getChannelValue(color, props.channel))}${channelUnits[props.channel] ?? ''}`;
});

function sliderAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <div :class="['flex flex-col gap-2', attrs.class]">
        <div v-if="label" class="flex items-center justify-between gap-3 text-sm">
            <span :id="labelId" class="font-medium text-ink">{{ label }}</span>
            <span class="text-muted tabular-nums">{{ channelValueText }}</span>
        </div>

        <ColorSliderRoot
            v-bind="sliderAttrs()"
            :model-value="currentColor"
            :channel="channel"
            :color-space="colorSpace"
            :disabled="disabled"
            class="relative flex h-5 w-full touch-none items-center select-none data-disabled:opacity-50"
            @update:model-value="model = $event"
            @update:color="lastSliderColor = $event"
        >
            <ColorSliderTrack class="relative h-3 grow rounded-full inset-ring inset-ring-ink/10" />

            <ColorSliderThumb
                :aria-labelledby="label ? labelId : undefined"
                class="block size-5 cursor-grab rounded-full border border-line bg-white shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand active:cursor-grabbing data-disabled:cursor-not-allowed"
            />
        </ColorSliderRoot>
    </div>
</template>
