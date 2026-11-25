<script setup>
/**
 * Toolbar button that opens a swatch palette, used for text color and highlight color.
 * Emits `select` with a hex color, or `null` when the reset option is chosen.
 *
 * Usage: <EditorColorPicker icon="baseline" label="Warna teks" reset-label="Otomatis" :colors="colors" :color="current" @select="apply" />
 */
import { computed, ref } from 'vue';
import Icon from '@/components/ui/Icon.vue';
import Popover from '@/components/ui/Popover.vue';
import EditorToolbarButton from './EditorToolbarButton.vue';

const props = defineProps({
    icon: { type: String, required: true },
    label: { type: String, required: true },
    resetLabel: { type: String, required: true },
    colors: { type: Array, required: true },
    /** The color at the cursor, or null when none is set. */
    color: { type: String, default: null },
    /** Color of the bar under the icon when no color is set. */
    fallbackColor: { type: String, default: 'transparent' },
});

const emit = defineEmits(['select']);

const open = ref(false);

/** The native color input only accepts `#rrggbb`. */
const colorInputValue = computed(() => (/^#[0-9a-f]{6}$/i.test(props.color ?? '') ? props.color : '#000000'));

const isSelected = (swatch) => props.color?.toLowerCase() === swatch.toLowerCase();

function choose(color) {
    emit('select', color);
    open.value = false;
}
</script>

<template>
    <Popover v-model:open="open" align="start" width="w-auto">
        <template #trigger>
            <EditorToolbarButton :label="label">
                <Icon :name="icon" class="size-4" />
                <span
                    class="absolute inset-x-2 bottom-1 h-1 rounded-full border border-black/10"
                    :style="{ backgroundColor: color ?? fallbackColor }"
                    aria-hidden="true"
                />
            </EditorToolbarButton>
        </template>

        <div class="flex flex-col gap-3">
            <button
                type="button"
                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-left text-sm transition-colors hover:bg-ink/5"
                @click="choose(null)"
            >
                <Icon name="x" class="size-4 text-muted" />
                {{ resetLabel }}
            </button>

            <div class="grid grid-cols-8 gap-1.5">
                <button
                    v-for="swatch in colors"
                    :key="swatch"
                    type="button"
                    :aria-label="swatch"
                    :title="swatch"
                    :class="[
                        'size-6 cursor-pointer rounded-md border border-black/10 ring-offset-2 ring-offset-surface transition-transform hover:scale-110',
                        { 'ring-2 ring-brand': isSelected(swatch) },
                    ]"
                    :style="{ backgroundColor: swatch }"
                    @click="choose(swatch)"
                />
            </div>

            <label class="flex cursor-pointer items-center justify-between gap-3 border-t border-line pt-3 text-sm text-muted">
                Warna lain
                <input
                    type="color"
                    :value="colorInputValue"
                    class="size-7 cursor-pointer rounded-md border border-line bg-transparent p-0.5"
                    @change="choose($event.target.value)"
                />
            </label>
        </div>
    </Popover>
</template>
