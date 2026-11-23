<script setup>
/**
 * Built on reka-ui Tree (arrow-key navigation, typeahead, single or multiple selection).
 * Items are data-driven: `{ id, label, icon?, children? }`; an item with `children` is a folder.
 * Folders show folder / folder-open icons, files show `icon` (default: file).
 *
 * Usage:
 * <Tree v-model="selected" :items="folders" :default-expanded="['surat-masuk']" />
 * <Tree v-model="checked" v-model:expanded="openFolders" :items="folders" multiple propagate-select bubble-select />
 *
 * Keys come from `get-key` (default: item => item.id). Single selection stores a copy of the item,
 * multiple selection an array of items. The #item slot ({ item, isExpanded, isSelected }) replaces the label.
 */
import { TreeItem, TreeRoot } from 'reka-ui';
import Icon from './Icon.vue';

const model = defineModel({ type: [Object, Array], default: undefined });
const expanded = defineModel('expanded', { type: Array, default: undefined });

defineProps({
    /** @type {Array<{ id: string|number, label: string, icon?: string, children?: Array }>} */
    items: { type: Array, required: true },
    getKey: { type: Function, default: (item) => item.id },
    /** Keys of the folders that are open initially (without v-model:expanded). */
    defaultExpanded: { type: Array, default: undefined },
    multiple: { type: Boolean, default: false },
    /** Multiple: selecting a folder selects (or clears) everything inside it. */
    propagateSelect: { type: Boolean, default: false },
    /** Multiple: a folder becomes selected when all of its children are. */
    bubbleSelect: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

function iconFor(item, isFolder, isExpanded) {
    if (isFolder) {
        return isExpanded ? 'folder-open' : 'folder';
    }

    return item.icon ?? 'file';
}
</script>

<template>
    <TreeRoot
        v-slot="{ flattenItems }"
        v-model="model"
        v-model:expanded="expanded"
        :items="items"
        :get-key="getKey"
        :default-expanded="defaultExpanded"
        :multiple="multiple"
        :propagate-select="propagateSelect"
        :bubble-select="bubbleSelect"
        :disabled="disabled"
        class="flex flex-col gap-0.5 text-sm select-none"
    >
        <TreeItem
            v-for="item in flattenItems"
            :key="item._id"
            v-slot="{ isExpanded, isSelected, isIndeterminate }"
            v-bind="item.bind"
            :style="{ paddingLeft: `${(item.level - 1) * 1.25 + 0.5}rem` }"
            class="flex h-9 cursor-pointer items-center gap-2 rounded-lg pr-2 text-ink transition-colors hover:bg-ink/5 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand data-disabled:cursor-not-allowed data-disabled:opacity-50 data-selected:bg-brand-soft data-selected:text-brand-ink"
        >
            <Icon
                v-if="item.hasChildren"
                name="chevron-right"
                :class="['size-4 text-muted transition-transform duration-200', { 'rotate-90': isExpanded }]"
            />
            <span v-else class="size-4 shrink-0" aria-hidden="true" />

            <span
                v-if="multiple"
                :class="[
                    'flex size-4 shrink-0 items-center justify-center rounded border transition-colors',
                    isSelected || isIndeterminate ? 'border-brand bg-brand text-white' : 'border-line bg-surface',
                ]"
                aria-hidden="true"
            >
                <Icon v-if="isIndeterminate" name="minus" class="size-3" />
                <Icon v-else-if="isSelected" name="check" class="size-3" />
            </span>

            <Icon
                :name="iconFor(item.value, item.hasChildren, isExpanded)"
                :class="['size-4.5', item.hasChildren ? 'text-warning' : 'text-muted']"
            />

            <span class="min-w-0 flex-1 truncate">
                <slot name="item" :item="item.value" :is-expanded="isExpanded" :is-selected="isSelected">
                    {{ item.value.label }}
                </slot>
            </span>
        </TreeItem>
    </TreeRoot>
</template>
