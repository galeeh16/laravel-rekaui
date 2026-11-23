<script setup>
/**
 * Built on reka-ui Autocomplete: a free-text input with a list of suggestions.
 * Unlike Combobox, the v-model is whatever was typed (a string); picking a suggestion only fills it in.
 *
 * Usage: <Autocomplete v-model="form.city" label="Kota tujuan" :suggestions="['Bandung', 'Bekasi', 'Bogor']" />
 *        <Autocomplete v-model="query" :suggestions="results" :loading="isSearching" ignore-filter />   (server-side search)
 *
 * Suggestions are strings or `{ value, hint? }`. By default they are filtered by what's typed;
 * with `ignore-filter` they are shown as given (filter them yourself, e.g. on the server).
 * Without `empty-text` the list simply hides when nothing matches.
 *
 * The `class` attribute is applied to the wrapper; every other attribute goes to the <input>.
 */
import {
    AutocompleteAnchor,
    AutocompleteCancel,
    AutocompleteContent,
    AutocompleteEmpty,
    AutocompleteInput,
    AutocompleteItem,
    AutocompletePortal,
    AutocompleteRoot,
    AutocompleteViewport,
} from 'reka-ui';
import { computed, useAttrs, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    suggestions: { type: Array, default: () => [] },
    label: { type: String, default: null },
    id: { type: String, default: null },
    name: { type: String, default: undefined },
    placeholder: { type: String, default: null },
    /** Icon at the start of the input, e.g. `search`. */
    icon: { type: String, default: null },
    /** Shown in the list when nothing matches; leave empty to hide the list instead. */
    emptyText: { type: String, default: null },
    /** Show a spinner (and "Mencari…" while there are no suggestions yet). */
    loading: { type: Boolean, default: false },
    /** Don't filter the suggestions client-side. */
    ignoreFilter: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const attrs = useAttrs();
const generatedId = useId();

const inputId = computed(() => props.id ?? generatedId);

const normalizedSuggestions = computed(() =>
    props.suggestions.map((suggestion) => (typeof suggestion === 'object' ? suggestion : { value: String(suggestion) })),
);

function inputAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <div :class="['flex flex-col gap-1.5', attrs.class]">
        <Label v-if="label" :for="inputId" :required="required">{{ label }}</Label>

        <AutocompleteRoot v-model="model" :name="name" :disabled="disabled" :required="required" :ignore-filter="ignoreFilter" open-on-click>
            <AutocompleteAnchor
                :class="[
                    'flex h-11 w-full items-center rounded-xl border bg-surface text-sm text-ink shadow-xs transition',
                    'focus-within:ring-3 has-disabled:cursor-not-allowed has-disabled:opacity-60',
                    error
                        ? 'border-danger focus-within:border-danger focus-within:ring-danger/15'
                        : 'border-line focus-within:border-brand focus-within:ring-brand/15',
                ]"
            >
                <Icon v-if="icon" :name="icon" class="pointer-events-none ml-3.5 size-4.5 text-muted" />

                <AutocompleteInput
                    :id="inputId"
                    v-bind="inputAttrs()"
                    :placeholder="placeholder"
                    :aria-invalid="error ? 'true' : undefined"
                    :class="[
                        'h-full min-w-0 flex-1 bg-transparent pr-2 outline-none placeholder:text-muted/80 disabled:cursor-not-allowed',
                        icon ? 'pl-2.5' : 'pl-3.5',
                    ]"
                />

                <Icon v-if="loading" name="loader" class="mr-3.5 size-4 animate-spin text-muted" />

                <AutocompleteCancel
                    v-else-if="model && !disabled"
                    class="mr-1.5 inline-flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-lg text-muted transition-colors hover:bg-ink/5 hover:text-ink"
                    aria-label="Hapus teks"
                >
                    <Icon name="x" class="size-4" />
                </AutocompleteCancel>
            </AutocompleteAnchor>

            <AutocompletePortal>
                <AutocompleteContent
                    position="popper"
                    :side-offset="6"
                    :hide-when-empty="!loading && !emptyText"
                    class="z-50 max-h-[min(20rem,var(--reka-combobox-content-available-height))] w-(--reka-combobox-trigger-width) origin-(--reka-combobox-content-transform-origin) overflow-hidden rounded-xl border border-line bg-surface p-1.5 text-ink shadow-lg shadow-black/8 data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
                >
                    <AutocompleteViewport>
                        <p v-if="loading && normalizedSuggestions.length === 0" class="px-2.5 py-6 text-center text-sm text-muted">Mencari…</p>
                        <AutocompleteEmpty v-else-if="emptyText" class="px-2.5 py-6 text-center text-sm text-muted">{{ emptyText }}</AutocompleteEmpty>

                        <AutocompleteItem
                            v-for="suggestion in normalizedSuggestions"
                            :key="suggestion.value"
                            :value="suggestion.value"
                            :text-value="suggestion.value"
                            class="flex cursor-pointer items-center justify-between gap-3 rounded-lg px-2.5 py-2 text-sm outline-none select-none data-disabled:pointer-events-none data-disabled:opacity-50 data-highlighted:bg-ink/5"
                        >
                            <span class="truncate">{{ suggestion.value }}</span>
                            <span v-if="suggestion.hint" class="shrink-0 text-xs text-muted">{{ suggestion.hint }}</span>
                        </AutocompleteItem>
                    </AutocompleteViewport>
                </AutocompleteContent>
            </AutocompletePortal>
        </AutocompleteRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
