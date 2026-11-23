<script setup>
/**
 * A group of toggle buttons inside a Toolbar (reka-ui ToolbarToggleGroup); arrow-key focus is handled
 * by the Toolbar. type="single": v-model is one value; type="multiple": v-model is an array.
 *
 * Usage: <ToolbarToggleGroup v-model="alignment" icon-only disallow-empty :options="[{ value: 'left', label: 'Rata kiri', icon: 'align-left' }]" />
 *
 * Options are `{ value, label, icon?, disabled? }`. With `icon-only` the label becomes the aria-label and tooltip.
 */
import { ToolbarToggleGroup, ToolbarToggleItem } from 'reka-ui';
import { computed } from 'vue';
import Icon from './Icon.vue';

const model = defineModel({ type: [String, Number, Array], default: undefined });

const props = defineProps({
    options: { type: Array, required: true },
    /** `single` or `multiple`. */
    type: { type: String, default: 'single' },
    /** Hide the labels and show only the icons. */
    iconOnly: { type: Boolean, default: false },
    /** Ignore clicks that would leave nothing pressed. */
    disallowEmpty: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

/** Keep reka-ui controlled (null / [] instead of undefined) so `disallow-empty` can ignore an empty update. */
const groupValue = computed({
    get: () => model.value ?? (props.type === 'multiple' ? [] : null),
    set: (value) => {
        const isEmpty = value === undefined || value === null || (Array.isArray(value) && value.length === 0);

        if (props.disallowEmpty && isEmpty) {
            return;
        }

        model.value = value;
    },
});
</script>

<template>
    <ToolbarToggleGroup
        v-model="groupValue"
        :type="type"
        :disabled="disabled"
        class="flex items-center gap-1 data-[orientation=vertical]:flex-col data-[orientation=vertical]:items-stretch"
    >
        <ToolbarToggleItem
            v-for="option in options"
            :key="option.value"
            :value="option.value"
            :disabled="option.disabled"
            :aria-label="iconOnly ? option.label : undefined"
            :title="iconOnly ? option.label : undefined"
            :class="[
                'inline-flex shrink-0 cursor-pointer items-center justify-center text-sm font-medium whitespace-nowrap text-ink transition-colors hover:bg-ink/5',
                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-50',
                'data-[state=on]:bg-brand-soft data-[state=on]:text-brand-ink',
                iconOnly ? 'size-8 rounded-lg' : 'h-8 gap-1.5 rounded-lg px-3',
            ]"
        >
            <Icon v-if="option.icon" :name="option.icon" class="size-4" />
            <span v-if="!iconOnly">{{ option.label }}</span>
        </ToolbarToggleItem>
    </ToolbarToggleGroup>
</template>
