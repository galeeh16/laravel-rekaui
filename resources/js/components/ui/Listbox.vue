<script setup>
/**
 * Built on reka-ui Listbox: an always-visible list of options, single or multiple choice,
 * with arrow-key navigation and typeahead.
 *
 * Usage: <Listbox v-model="form.template" label="Template surat" :options="templates" />
 *        <Listbox v-model="form.recipients" label="Penerima" :options="employees" multiple searchable />
 *
 * Options are `{ value, label, hint?, icon?, disabled? }` or plain values; group them under a heading
 * with `{ label: 'Direksi', options: [...] }`. `searchable` adds a filter field above the list
 * (case- and accent-insensitive, also matches the hint).
 *
 * The `class` attribute is applied to the wrapper.
 */
import { ListboxContent, ListboxFilter, ListboxGroup, ListboxGroupLabel, ListboxItem, ListboxItemIndicator, ListboxRoot, useFilter } from 'reka-ui';
import { computed, ref, useId } from 'vue';
import Icon from './Icon.vue';

const model = defineModel({ type: [String, Number, Array], default: undefined });

const props = defineProps({
    options: { type: Array, required: true },
    label: { type: String, default: null },
    name: { type: String, default: undefined },
    multiple: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    searchable: { type: Boolean, default: false },
    searchPlaceholder: { type: String, default: 'Cari…' },
    /** Shown when the search matches nothing. */
    emptyText: { type: String, default: 'Tidak ada hasil.' },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const labelId = useId();
const searchTerm = ref('');
const { contains } = useFilter({ sensitivity: 'base' });

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

const visibleGroups = computed(() => {
    if (!searchTerm.value) {
        return groups.value;
    }

    return groups.value
        .map((group) => ({
            ...group,
            options: group.options.filter((option) => contains(`${option.label} ${option.hint ?? ''}`, searchTerm.value)),
        }))
        .filter((group) => group.options.length > 0);
});
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <span v-if="label" :id="labelId" class="text-sm font-medium text-ink">
            {{ label }}
            <span v-if="required" class="ml-0.5 text-danger" aria-hidden="true">*</span>
        </span>

        <ListboxRoot
            v-model="model"
            :multiple="multiple"
            :disabled="disabled"
            :name="name"
            :required="required"
            :class="[
                'overflow-hidden rounded-xl border bg-surface text-sm text-ink shadow-xs transition',
                'focus-within:ring-3 data-disabled:cursor-not-allowed data-disabled:opacity-60',
                error
                    ? 'border-danger focus-within:border-danger focus-within:ring-danger/15'
                    : 'border-line focus-within:border-brand focus-within:ring-brand/15',
            ]"
        >
            <div v-if="searchable" class="flex items-center gap-2 border-b border-line px-3.5">
                <Icon name="search" class="size-4 text-muted" />
                <ListboxFilter
                    v-model="searchTerm"
                    :placeholder="searchPlaceholder"
                    :aria-label="searchPlaceholder"
                    class="h-10 w-full bg-transparent outline-none placeholder:text-muted/80 disabled:cursor-not-allowed"
                />
            </div>

            <ListboxContent
                :aria-labelledby="label ? labelId : undefined"
                :aria-invalid="error ? 'true' : undefined"
                class="max-h-64 overflow-y-auto p-1.5 outline-none"
            >
                <component :is="group.label ? ListboxGroup : 'div'" v-for="(group, groupIndex) in visibleGroups" :key="group.label ?? groupIndex">
                    <ListboxGroupLabel v-if="group.label" class="px-2.5 pt-2 pb-1 text-xs font-medium text-muted">
                        {{ group.label }}
                    </ListboxGroupLabel>

                    <ListboxItem
                        v-for="option in group.options"
                        :key="option.value"
                        :value="option.value"
                        :disabled="option.disabled"
                        class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 outline-none select-none hover:bg-ink/5 data-disabled:pointer-events-none data-disabled:opacity-50 data-highlighted:bg-ink/5 data-[state=checked]:font-medium"
                    >
                        <Icon v-if="option.icon" :name="option.icon" class="size-4 text-muted" />

                        <span class="flex min-w-0 flex-1 flex-col">
                            <span class="truncate">{{ option.label }}</span>
                            <span v-if="option.hint" class="truncate text-xs font-normal text-muted">{{ option.hint }}</span>
                        </span>

                        <ListboxItemIndicator class="inline-flex">
                            <Icon name="check" class="size-4 text-brand" />
                        </ListboxItemIndicator>
                    </ListboxItem>
                </component>

                <p v-if="visibleGroups.length === 0" class="px-2.5 py-6 text-center text-muted">{{ emptyText }}</p>
            </ListboxContent>
        </ListboxRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
