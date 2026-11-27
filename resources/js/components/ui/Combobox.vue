<script setup>
/**
 * Built on reka-ui Combobox: a select whose list can be searched by typing
 * (case- and accent-insensitive, rendered in a portal).
 *
 * Usage: <Combobox v-model="form.unit" label="Unit tujuan" :options="units" placeholder="Cari unit…" />
 *        <Combobox v-model="form.cc" label="Tembusan" :options="employees" multiple />   (array v-model, shown as chips)
 *        <Combobox v-model="form.unit" :options="units" clearable />   (× button empties it: null, or [] in multiple mode)
 *
 * Options are `{ value, label, hint?, disabled? }` or plain values; group them under a heading with
 * `{ label: 'Sekretariat', options: [...] }`. Typing also matches the `hint` (e.g. a position or NIP).
 * In multiple mode, Backspace in the empty search field removes the last chip.
 *
 * The `class` attribute is applied to the wrapper; every other attribute goes to the <input>.
 */
import {
    ComboboxAnchor,
    ComboboxContent,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxLabel,
    ComboboxPortal,
    ComboboxRoot,
    ComboboxTrigger,
    ComboboxViewport,
} from 'reka-ui';
import { computed, ref, useAttrs, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: [String, Number, Array], default: undefined });

const props = defineProps({
    options: { type: Array, default: () => [] },
    label: { type: String, default: null },
    id: { type: String, default: null },
    name: { type: String, default: undefined },
    placeholder: { type: String, default: 'Cari…' },
    /** Shown in the list when nothing matches the search. */
    emptyText: { type: String, default: 'Tidak ada hasil.' },
    multiple: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
    /** Show a × button that empties the selection (null, or [] in multiple mode). */
    clearable: { type: Boolean, default: false },
});

const attrs = useAttrs();
const generatedId = useId();
const searchTerm = ref('');

const inputId = computed(() => props.id ?? generatedId);

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

const optionsByValue = computed(() => new Map(groups.value.flatMap((group) => group.options).map((option) => [option.value, option])));

const selectedValues = computed(() => (props.multiple && Array.isArray(model.value) ? model.value : []));

function labelOf(value) {
    return optionsByValue.value.get(value)?.label ?? String(value ?? '');
}

/** Text shown in the input once an option is picked. In multiple mode the input is only for searching. */
function displayValue(value) {
    return props.multiple || value === null || value === undefined ? '' : labelOf(value);
}

function removeValue(value) {
    model.value = selectedValues.value.filter((selected) => selected !== value);
}

function removeLastValue() {
    if (props.multiple && searchTerm.value === '' && selectedValues.value.length > 0) {
        model.value = selectedValues.value.slice(0, -1);
    }
}

const hasSelection = computed(() =>
    props.multiple ? selectedValues.value.length > 0 : model.value !== null && model.value !== undefined && model.value !== '',
);

const showClearButton = computed(() => props.clearable && !props.disabled && hasSelection.value);

/** Empty the selection and the search text, then focus the input since the × button disappears. */
function clearSelection() {
    model.value = props.multiple ? [] : null;
    searchTerm.value = '';
    document.getElementById(inputId.value)?.focus();
}

function inputAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <div :class="['flex flex-col gap-1.5', attrs.class]">
        <Label v-if="label" :for="inputId" :required="required">{{ label }}</Label>

        <ComboboxRoot v-model="model" :multiple="multiple" :disabled="disabled" :name="name" :required="required" open-on-click>
            <ComboboxAnchor
                :class="[
                    'flex min-h-11 w-full flex-wrap items-center gap-1.5 rounded-xl border bg-surface py-1.5 pr-1.5 text-sm text-ink shadow-xs transition',
                    'focus-within:ring-3 has-disabled:cursor-not-allowed has-disabled:opacity-60',
                    selectedValues.length > 0 ? 'pl-1.5' : 'pl-3.5',
                    error
                        ? 'border-danger focus-within:border-danger focus-within:ring-danger/15'
                        : 'border-line focus-within:border-brand focus-within:ring-brand/15',
                ]"
            >
                <span
                    v-for="value in selectedValues"
                    :key="value"
                    class="inline-flex h-7 max-w-full items-center gap-1 rounded-lg bg-brand-soft pr-1 pl-2.5 text-brand-ink"
                >
                    <span class="truncate">{{ labelOf(value) }}</span>
                    <button
                        type="button"
                        class="inline-flex size-5 shrink-0 cursor-pointer items-center justify-center rounded-md transition-colors hover:bg-brand/10 focus-visible:outline-2 focus-visible:outline-brand disabled:cursor-not-allowed"
                        :aria-label="`Hapus ${labelOf(value)}`"
                        :disabled="disabled"
                        @click="removeValue(value)"
                    >
                        <Icon name="x" class="size-3.5" />
                    </button>
                </span>

                <ComboboxInput
                    :id="inputId"
                    v-model="searchTerm"
                    v-bind="inputAttrs()"
                    :display-value="displayValue"
                    :placeholder="selectedValues.length > 0 ? undefined : placeholder"
                    :aria-invalid="error ? 'true' : undefined"
                    :class="[
                        'h-8 min-w-24 flex-1 bg-transparent outline-none placeholder:text-muted/80 disabled:cursor-not-allowed',
                        selectedValues.length > 0 && 'pl-1',
                    ]"
                    @keydown.backspace="removeLastValue"
                />

                <button
                    v-if="showClearButton"
                    type="button"
                    :aria-label="label ? `Kosongkan ${label.toLowerCase()}` : 'Kosongkan pilihan'"
                    class="-mr-1 inline-flex size-6 shrink-0 cursor-pointer items-center justify-center rounded-md text-muted transition-colors hover:bg-ink/6 hover:text-ink focus-visible:outline-2 focus-visible:outline-brand"
                    @click="clearSelection"
                >
                    <Icon name="x" class="size-3.5" />
                </button>

                <ComboboxTrigger
                    class="inline-flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-lg text-muted transition-colors hover:bg-ink/5 hover:text-ink disabled:cursor-not-allowed"
                    aria-label="Tampilkan pilihan"
                >
                    <Icon name="chevrons-up-down" class="size-4" />
                </ComboboxTrigger>
            </ComboboxAnchor>

            <ComboboxPortal>
                <ComboboxContent
                    position="popper"
                    :side-offset="6"
                    class="z-50 max-h-[min(20rem,var(--reka-combobox-content-available-height))] w-(--reka-combobox-trigger-width) origin-(--reka-combobox-content-transform-origin) overflow-hidden rounded-xl border border-line bg-surface p-1.5 text-ink shadow-lg shadow-black/8 data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
                >
                    <ComboboxViewport>
                        <ComboboxEmpty class="px-2.5 py-6 text-center text-sm text-muted">{{ emptyText }}</ComboboxEmpty>

                        <component :is="group.label ? ComboboxGroup : 'div'" v-for="(group, groupIndex) in groups" :key="groupIndex">
                            <ComboboxLabel v-if="group.label" class="px-2.5 pt-2 pb-1 text-xs font-medium text-muted">
                                {{ group.label }}
                            </ComboboxLabel>

                            <ComboboxItem
                                v-for="option in group.options"
                                :key="option.value"
                                :value="option.value"
                                :disabled="option.disabled"
                                :text-value="option.hint ? `${option.label} ${option.hint}` : option.label"
                                class="relative flex cursor-pointer flex-col rounded-lg py-2 pr-8 pl-2.5 text-sm outline-none select-none data-disabled:pointer-events-none data-disabled:opacity-50 data-highlighted:bg-ink/5"
                            >
                                <span>{{ option.label }}</span>
                                <span v-if="option.hint" class="text-xs text-muted">{{ option.hint }}</span>

                                <ComboboxItemIndicator class="absolute top-1/2 right-2.5 inline-flex -translate-y-1/2">
                                    <Icon name="check" class="size-4 text-brand" />
                                </ComboboxItemIndicator>
                            </ComboboxItem>
                        </component>
                    </ComboboxViewport>
                </ComboboxContent>
            </ComboboxPortal>
        </ComboboxRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
