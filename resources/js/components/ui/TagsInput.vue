<script setup>
/**
 * Built on reka-ui TagsInput: type and press Enter (or the delimiter, a comma by default) to add a tag;
 * Backspace / arrow keys select tags in the field and Backspace again removes the selected one.
 * The v-model is an array of strings.
 *
 * Usage: <TagsInput v-model="form.cc" label="Tembusan" placeholder="Tambah penerima…" />
 *        <TagsInput v-model="form.keywords" :max="5" clearable />
 */
import { TagsInputClear, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText, TagsInputRoot } from 'reka-ui';
import { computed, useId } from 'vue';
import Icon from './Icon.vue';
import Label from './Label.vue';

const model = defineModel({ type: Array, default: () => [] });

const props = defineProps({
    label: { type: String, default: null },
    id: { type: String, default: null },
    name: { type: String, default: undefined },
    placeholder: { type: String, default: 'Ketik lalu tekan Enter…' },
    /** Maximum number of tags; 0 means unlimited. */
    max: { type: Number, default: 0 },
    /** Character (or RegExp) that also adds the typed text as a tag. */
    delimiter: { type: [String, RegExp], default: ',' },
    /** Allow the same tag more than once. */
    duplicate: { type: Boolean, default: false },
    /** Split pasted text on the delimiter and add every part. */
    addOnPaste: { type: Boolean, default: true },
    /** Add the typed text when the field loses focus. */
    addOnBlur: { type: Boolean, default: true },
    /** Show a button that removes every tag. */
    clearable: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const generatedId = useId();
const inputId = computed(() => props.id ?? generatedId);

const isFull = computed(() => props.max > 0 && model.value.length >= props.max);
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="inputId">{{ label }}</Label>

        <TagsInputRoot
            :id="inputId"
            v-model="model"
            :name="name"
            :max="max"
            :delimiter="delimiter"
            :duplicate="duplicate"
            :add-on-paste="addOnPaste"
            :add-on-blur="addOnBlur"
            :disabled="disabled"
            :class="[
                'flex min-h-11 w-full flex-wrap items-center gap-1.5 rounded-xl border bg-surface px-2 py-1.5 text-sm text-ink shadow-xs transition',
                'focus-within:ring-3 data-disabled:cursor-not-allowed data-disabled:opacity-60',
                error
                    ? 'border-danger focus-within:border-danger focus-within:ring-danger/15'
                    : 'border-line focus-within:border-brand focus-within:ring-brand/15',
            ]"
        >
            <TagsInputItem
                v-for="(tag, index) in model"
                :key="`${index}-${tag}`"
                :value="tag"
                class="inline-flex h-7 max-w-full items-center gap-1 rounded-lg bg-ink/6 pr-1 pl-2.5 text-ink transition-colors data-[state=active]:bg-brand-soft data-[state=active]:text-brand-ink"
            >
                <TagsInputItemText class="truncate" />

                <TagsInputItemDelete
                    :aria-label="`Hapus ${tag}`"
                    class="inline-flex size-5 shrink-0 cursor-pointer items-center justify-center rounded-md text-muted transition-colors hover:bg-ink/10 hover:text-ink"
                >
                    <Icon name="x" class="size-3.5" />
                </TagsInputItemDelete>
            </TagsInputItem>

            <TagsInputInput
                :placeholder="isFull ? '' : placeholder"
                :aria-invalid="error ? 'true' : undefined"
                class="h-7 min-w-28 flex-1 bg-transparent px-1.5 outline-none placeholder:text-muted/80 disabled:cursor-not-allowed data-invalid:text-danger"
            />

            <TagsInputClear
                v-if="clearable && model.length > 0"
                aria-label="Hapus semua"
                class="ml-auto inline-flex size-7 shrink-0 cursor-pointer items-center justify-center rounded-lg text-muted transition-colors hover:bg-ink/5 hover:text-ink focus-visible:outline-2 focus-visible:outline-brand"
            >
                <Icon name="x" class="size-4" />
            </TagsInputClear>
        </TagsInputRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
