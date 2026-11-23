<script setup>
/**
 * Built on reka-ui TagGroup: a set of tags with arrow-key navigation, optional selection
 * (single / multiple; click, Enter or Space toggles) and optional removal (× button, Delete or Backspace).
 *
 * Usage: <TagGroup v-model="selectedLabels" :items="['Undangan', 'Keuangan']" selection-mode="multiple" />
 *        <TagGroup v-model:items="recipients" removable />
 *
 * Items are plain strings or `{ value, label, icon?, disabled? }`. With `removable`, removed tags are
 * dropped from `v-model:items` and a `remove` event is emitted with the removed values.
 */
import { TagGroupItem, TagGroupItemDelete, TagGroupItemText, TagGroupRoot } from 'reka-ui';
import { computed, useId } from 'vue';
import Icon from './Icon.vue';

/** Selected value (single) or values (multiple). */
const model = defineModel({ type: [String, Number, Array], default: undefined });
const items = defineModel('items', { type: Array, default: () => [] });

const props = defineProps({
    /** `none` (display only), `single` or `multiple`. */
    selectionMode: { type: String, default: 'none' },
    /** Keep at least one tag selected. */
    disallowEmptySelection: { type: Boolean, default: false },
    removable: { type: Boolean, default: false },
    label: { type: String, default: null },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['remove']);

const labelId = useId();

const normalizedItems = computed(() =>
    items.value.map((item) => (typeof item === 'object' ? item : { value: item, label: String(item) })),
);

function itemValue(item) {
    return typeof item === 'object' ? item.value : item;
}

function removeItems(values) {
    items.value = items.value.filter((item) => !values.includes(itemValue(item)));
    emit('remove', values);
}

/** reka-ui only enables removing when the root has a `remove` listener, so bind it conditionally. */
const removeListener = computed(() => (props.removable ? { remove: removeItems } : {}));
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <span v-if="label" :id="labelId" class="text-sm font-medium text-ink">{{ label }}</span>

        <TagGroupRoot
            v-model="model"
            :selection-mode="selectionMode"
            :disallow-empty-selection="disallowEmptySelection"
            :disabled="disabled"
            :aria-labelledby="label ? labelId : undefined"
            class="flex flex-wrap gap-2 rounded-lg outline-none data-disabled:opacity-60"
            v-on="removeListener"
        >
            <TagGroupItem
                v-for="item in normalizedItems"
                :key="item.value"
                v-slot="{ selected }"
                :value="item.value"
                :text-value="item.label"
                :disabled="item.disabled"
                :class="[
                    'inline-flex h-8 items-center gap-1.5 rounded-full border border-line bg-surface text-sm font-medium text-ink shadow-xs transition-colors',
                    'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand data-disabled:cursor-not-allowed data-disabled:opacity-50',
                    'data-[state=checked]:border-brand/30 data-[state=checked]:bg-brand-soft data-[state=checked]:text-brand-ink',
                    selectionMode !== 'none' && 'cursor-pointer hover:bg-canvas',
                    removable ? 'pr-1 pl-3' : 'px-3',
                ]"
            >
                <Icon v-if="selected" name="check" class="size-3.5" />
                <Icon v-else-if="item.icon" :name="item.icon" class="size-3.5 text-muted" />

                <TagGroupItemText>{{ item.label }}</TagGroupItemText>

                <TagGroupItemDelete
                    v-if="removable"
                    aria-label="Hapus"
                    class="inline-flex size-6 shrink-0 cursor-pointer items-center justify-center rounded-full text-muted transition-colors hover:bg-ink/10 hover:text-ink disabled:pointer-events-none"
                >
                    <Icon name="x" class="size-3.5" />
                </TagGroupItemDelete>
            </TagGroupItem>
        </TagGroupRoot>
    </div>
</template>
