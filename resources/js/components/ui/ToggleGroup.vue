<script setup>
/**
 * Built on reka-ui ToggleGroup (arrow-key navigation between items).
 * type="single": v-model is one value; type="multiple": v-model is an array.
 * Variants: outline (items inside one bordered box, default), ghost (loose buttons)
 * Sizes: sm, md
 *
 * Usage: <ToggleGroup v-model="alignment" :options="[{ value: 'left', label: 'Rata kiri', icon: 'align-left' }]" icon-only disallow-empty />
 *        <ToggleGroup v-model="channels" type="multiple" :options="[{ value: 'email', label: 'Email', icon: 'mail' }]" />
 *
 * Options are `{ value, label, icon?, disabled? }` or plain values. With `icon-only` the label becomes the aria-label and tooltip.
 */
import { ToggleGroupItem, ToggleGroupRoot } from 'reka-ui';
import { computed, useId } from 'vue';
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
    variant: { type: String, default: 'outline' },
    size: { type: String, default: 'md' },
    orientation: { type: String, default: 'horizontal' },
    label: { type: String, default: null },
    name: { type: String, default: undefined },
    disabled: { type: Boolean, default: false },
    hint: { type: String, default: null },
});

const labelId = useId();

const normalizedOptions = computed(() =>
    props.options.map((option) => (typeof option === 'object' ? option : { value: option, label: String(option) })),
);

/**
 * Always hand reka-ui a defined value (null / []) so it stays controlled by this v-model;
 * that is what lets `disallow-empty` simply ignore an empty update.
 */
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

const itemSizeClasses = {
    sm: { text: 'h-7 gap-1.5 rounded-md px-2.5 text-sm', icon: 'size-7 rounded-md' },
    md: { text: 'h-8 gap-2 rounded-lg px-3 text-sm', icon: 'size-8 rounded-lg' },
};

const rootClasses = computed(() => {
    if (props.variant === 'ghost') {
        return 'gap-1';
    }

    return props.size === 'sm'
        ? 'gap-0.5 rounded-lg border border-line bg-surface p-0.5 shadow-xs'
        : 'gap-1 rounded-xl border border-line bg-surface p-1 shadow-xs';
});
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <span v-if="label" :id="labelId" class="text-sm font-medium text-ink">{{ label }}</span>

        <ToggleGroupRoot
            v-model="groupValue"
            :type="type"
            :orientation="orientation"
            :name="name"
            :disabled="disabled"
            :aria-labelledby="label ? labelId : undefined"
            :class="['flex w-fit items-center data-[orientation=vertical]:flex-col data-[orientation=vertical]:items-stretch', rootClasses]"
        >
            <ToggleGroupItem
                v-for="option in normalizedOptions"
                :key="option.value"
                :value="option.value"
                :disabled="option.disabled"
                :aria-label="iconOnly ? option.label : undefined"
                :title="iconOnly ? option.label : undefined"
                :class="[
                    'inline-flex shrink-0 cursor-pointer items-center justify-center font-medium whitespace-nowrap text-muted transition-colors hover:bg-ink/5 hover:text-ink',
                    'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:pointer-events-none disabled:opacity-50',
                    'data-[state=on]:bg-brand-soft data-[state=on]:text-brand-ink',
                    iconOnly ? itemSizeClasses[size].icon : itemSizeClasses[size].text,
                ]"
            >
                <Icon v-if="option.icon" :name="option.icon" class="size-4" />
                <span v-if="!iconOnly">{{ option.label }}</span>
            </ToggleGroupItem>
        </ToggleGroupRoot>

        <p v-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
