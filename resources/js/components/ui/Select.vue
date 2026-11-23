<script setup>
/**
 * Built on reka-ui Select (keyboard navigation, typeahead, rendered in a portal).
 * Sizes: sm, md
 *
 * Usage: <Select v-model="status" label="Status" :options="[{ value: 'draft', label: 'Draf' }]" placeholder="Pilih status" />
 *        <Select v-model="perPage" size="sm" :options="[5, 10, 25]" />
 *
 * Options are `{ value, label, disabled? }` or plain values. Group them under a heading with
 * `{ label: 'Internal', options: [...] }`; groups and plain options can be mixed.
 *
 * The `class` attribute is applied to the wrapper; every other attribute goes to the trigger button.
 * Inside a Field, the trigger is linked to the field's label and turns red when the field is invalid.
 */
import {
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    SelectLabel,
    SelectPortal,
    SelectRoot,
    SelectSeparator,
    SelectTrigger,
    SelectValue,
    SelectViewport,
} from 'reka-ui';
import { computed, useAttrs, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: [String, Number], default: undefined });

const props = defineProps({
    options: { type: Array, default: () => [] },
    label: { type: String, default: null },
    id: { type: String, default: null },
    name: { type: String, default: undefined },
    placeholder: { type: String, default: 'Pilih…' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const attrs = useAttrs();
const generatedId = useId();

const selectId = computed(() => props.id ?? generatedId);

function normalizeOption(option) {
    return typeof option === 'object' ? option : { value: option, label: String(option) };
}

/**
 * Options as a list of groups: `{ label, options }` entries stay as they are, consecutive
 * plain options are collected into one group without a heading.
 */
const groups = computed(() =>
    props.options.reduce((result, option) => {
        if (option !== null && typeof option === 'object' && Array.isArray(option.options)) {
            result.push({ label: option.label, options: option.options.map(normalizeOption) });
        } else if (result.length > 0 && !result.at(-1).label) {
            result.at(-1).options.push(normalizeOption(option));
        } else {
            result.push({ label: null, options: [normalizeOption(option)] });
        }

        return result;
    }, []),
);

function triggerAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <div :class="['flex flex-col gap-1.5', attrs.class]">
        <Label v-if="label" :for="selectId" :required="required">{{ label }}</Label>

        <SelectRoot v-model="model" :name="name" :disabled="disabled" :required="required">
            <SelectTrigger
                :id="selectId"
                v-bind="triggerAttrs()"
                :aria-invalid="error ? 'true' : undefined"
                :class="[
                    'flex w-full cursor-pointer items-center justify-between gap-2 border bg-surface text-left text-sm text-ink shadow-xs outline-none transition',
                    'focus-visible:ring-3 disabled:cursor-not-allowed disabled:opacity-60 data-placeholder:text-muted/80',
                    size === 'sm' ? 'h-8 rounded-lg pr-2 pl-2.5' : 'h-11 rounded-xl pr-3 pl-3.5',
                    error
                        ? 'border-danger focus-visible:border-danger focus-visible:ring-danger/15'
                        : 'border-line focus-visible:border-brand focus-visible:ring-brand/15 data-invalid:border-danger data-invalid:focus-visible:border-danger data-invalid:focus-visible:ring-danger/15',
                ]"
            >
                <SelectValue :placeholder="placeholder" class="truncate" />
                <Icon name="chevron-down" class="size-4 text-muted" />
            </SelectTrigger>

            <SelectPortal>
                <SelectContent
                    position="popper"
                    :side-offset="6"
                    class="z-50 max-h-(--reka-select-content-available-height) min-w-(--reka-select-trigger-width) origin-(--reka-select-content-transform-origin) overflow-hidden rounded-xl border border-line bg-surface p-1.5 text-ink shadow-lg shadow-black/8 data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
                >
                    <SelectViewport>
                        <template v-for="(group, groupIndex) in groups" :key="groupIndex">
                            <SelectSeparator v-if="groupIndex > 0" class="-mx-1.5 my-1.5 h-px bg-line" />

                            <component :is="group.label ? SelectGroup : 'div'">
                                <SelectLabel v-if="group.label" class="px-2.5 pt-1.5 pb-1 text-xs font-medium text-muted">
                                    {{ group.label }}
                                </SelectLabel>

                                <SelectItem
                                    v-for="option in group.options"
                                    :key="option.value"
                                    :value="option.value"
                                    :disabled="option.disabled"
                                    class="relative flex cursor-pointer items-center rounded-lg py-2 pr-8 pl-2.5 text-sm outline-none select-none data-disabled:pointer-events-none data-disabled:opacity-50 data-highlighted:bg-ink/5"
                                >
                                    <SelectItemText>{{ option.label }}</SelectItemText>

                                    <SelectItemIndicator class="absolute right-2.5 inline-flex">
                                        <Icon name="check" class="size-4 text-brand" />
                                    </SelectItemIndicator>
                                </SelectItem>
                            </component>
                        </template>
                    </SelectViewport>
                </SelectContent>
            </SelectPortal>
        </SelectRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
