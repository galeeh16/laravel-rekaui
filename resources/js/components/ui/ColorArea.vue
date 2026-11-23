<script setup>
/**
 * Built on reka-ui ColorArea (2D picker: drag or click, arrow keys move the thumb, Shift steps by 10).
 * By default the x-axis is saturation and the y-axis brightness (HSB), so pair it with a hue ColorSlider.
 *
 * Usage:
 * <ColorArea v-model="color" class="max-w-xs" />
 * <ColorSlider v-model="color" channel="hue" />
 *
 * v-model is a hex string such as '#17795a' (reka-ui emits lowercase hex).
 * The `class` attribute sizes the component; the area keeps a 4:3 ratio.
 */
import { ColorAreaArea, ColorAreaRoot, ColorAreaThumb, isValidColor } from 'reka-ui';
import { computed } from 'vue';

const model = defineModel({ type: String, default: undefined });

const props = defineProps({
    defaultValue: { type: String, default: '#17795a' },
    colorSpace: { type: String, default: 'hsb' },
    xChannel: { type: String, default: 'saturation' },
    yChannel: { type: String, default: 'brightness' },
    disabled: { type: Boolean, default: false },
});

/** reka-ui throws on strings it can't parse, so fall back to the default colour. */
const currentColor = computed(() => (model.value && isValidColor(model.value) ? model.value : props.defaultValue));
</script>

<template>
    <ColorAreaRoot
        v-slot="{ style }"
        :model-value="currentColor"
        :color-space="colorSpace"
        :x-channel="xChannel"
        :y-channel="yChannel"
        :disabled="disabled"
        class="w-full data-disabled:opacity-50"
        @update:model-value="model = $event"
    >
        <ColorAreaArea
            :style="style"
            class="relative aspect-4/3 w-full cursor-crosshair rounded-xl shadow-xs inset-ring inset-ring-ink/10 data-disabled:cursor-not-allowed"
        >
            <ColorAreaThumb
                :style="{ backgroundColor: currentColor }"
                class="block size-5 rounded-full border-2 border-white shadow-md ring-1 ring-black/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
            />
        </ColorAreaArea>
    </ColorAreaRoot>
</template>
