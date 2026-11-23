<script setup>
/**
 * Renders an `items` array inside the content of a DropdownMenu, ContextMenu or Menubar, using the
 * reka-ui primitives of that menu `kind`. Used by those wrappers; you normally don't render it yourself.
 * Sub-menus (an item with `items`) render recursively.
 *
 * Item shapes:
 * - { label, icon?, shortcut?, variant?: 'danger', disabled?, onSelect?(event) }     action
 * - { label, icon?, href, method? }                                                  Inertia link
 * - { type: 'checkbox', label, checked, onCheckedChange(checked), disabled? }
 * - { type: 'radio', value, options: [{ value, label, disabled? }], onValueChange(value) }
 * - { label, icon?, items: [...] }                                                   sub-menu
 * - { type: 'label', label } / { type: 'separator' }
 *
 * Call `event.preventDefault()` in `onSelect` to keep the menu open after selecting.
 */
import { Link } from '@inertiajs/vue3';
import {
    ContextMenuCheckboxItem,
    ContextMenuItem,
    ContextMenuItemIndicator,
    ContextMenuLabel,
    ContextMenuPortal,
    ContextMenuRadioGroup,
    ContextMenuRadioItem,
    ContextMenuSeparator,
    ContextMenuSub,
    ContextMenuSubContent,
    ContextMenuSubTrigger,
    DropdownMenuCheckboxItem,
    DropdownMenuItem,
    DropdownMenuItemIndicator,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    MenubarCheckboxItem,
    MenubarItem,
    MenubarItemIndicator,
    MenubarLabel,
    MenubarPortal,
    MenubarRadioGroup,
    MenubarRadioItem,
    MenubarSeparator,
    MenubarSub,
    MenubarSubContent,
    MenubarSubTrigger,
} from 'reka-ui';
import { computed } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    /** Which reka-ui menu this list lives in: dropdown, context or menubar. */
    kind: { type: String, default: 'dropdown' },
    /** @type {Array<Record<string, any>>} */
    items: { type: Array, required: true },
});

/** The reka-ui building blocks of each menu kind, plus the CSS variable it sets for the transform origin. */
const partsByKind = {
    dropdown: {
        Item: DropdownMenuItem,
        CheckboxItem: DropdownMenuCheckboxItem,
        ItemIndicator: DropdownMenuItemIndicator,
        Label: DropdownMenuLabel,
        Portal: DropdownMenuPortal,
        RadioGroup: DropdownMenuRadioGroup,
        RadioItem: DropdownMenuRadioItem,
        Separator: DropdownMenuSeparator,
        Sub: DropdownMenuSub,
        SubContent: DropdownMenuSubContent,
        SubTrigger: DropdownMenuSubTrigger,
        originClass: 'origin-(--reka-dropdown-menu-content-transform-origin)',
    },
    context: {
        Item: ContextMenuItem,
        CheckboxItem: ContextMenuCheckboxItem,
        ItemIndicator: ContextMenuItemIndicator,
        Label: ContextMenuLabel,
        Portal: ContextMenuPortal,
        RadioGroup: ContextMenuRadioGroup,
        RadioItem: ContextMenuRadioItem,
        Separator: ContextMenuSeparator,
        Sub: ContextMenuSub,
        SubContent: ContextMenuSubContent,
        SubTrigger: ContextMenuSubTrigger,
        originClass: 'origin-(--reka-context-menu-content-transform-origin)',
    },
    menubar: {
        Item: MenubarItem,
        CheckboxItem: MenubarCheckboxItem,
        ItemIndicator: MenubarItemIndicator,
        Label: MenubarLabel,
        Portal: MenubarPortal,
        RadioGroup: MenubarRadioGroup,
        RadioItem: MenubarRadioItem,
        Separator: MenubarSeparator,
        Sub: MenubarSub,
        SubContent: MenubarSubContent,
        SubTrigger: MenubarSubTrigger,
        originClass: 'origin-(--reka-menubar-content-transform-origin)',
    },
};

const parts = computed(() => partsByKind[props.kind] ?? partsByKind.dropdown);

const baseItemClass =
    'flex w-full cursor-pointer items-center gap-2.5 rounded-lg py-2 pr-2.5 text-left text-sm outline-none select-none transition-colors data-disabled:pointer-events-none data-disabled:opacity-50';

/** Checkbox and radio items keep room on the left for their indicator, aligned with item icons. */
const checkableItemClass = [baseItemClass, 'relative pl-9 text-ink data-highlighted:bg-ink/5'];

const isDanger = (item) => item.variant === 'danger';

function itemClass(item) {
    return [
        baseItemClass,
        'pl-2.5',
        isDanger(item) ? 'text-danger data-highlighted:bg-danger-soft' : 'text-ink data-highlighted:bg-ink/5',
    ];
}

const iconClass = (item) => ['size-4', isDanger(item) ? '' : 'text-muted'];

const linkMethod = (item) => item.method ?? 'get';
</script>

<template>
    <template v-for="(item, index) in items" :key="`${index}-${item.type ?? 'item'}-${item.label ?? ''}`">
        <component :is="parts.Separator" v-if="item.type === 'separator'" class="-mx-1.5 my-1.5 h-px bg-line" />

        <component :is="parts.Label" v-else-if="item.type === 'label'" class="px-2.5 pt-1.5 pb-1 text-xs font-medium text-muted">
            {{ item.label }}
        </component>

        <component
            :is="parts.CheckboxItem"
            v-else-if="item.type === 'checkbox'"
            :model-value="item.checked"
            :disabled="item.disabled"
            :class="checkableItemClass"
            @update:model-value="item.onCheckedChange?.($event)"
            @select="item.onSelect?.($event)"
        >
            <component :is="parts.ItemIndicator" class="absolute left-2.5 inline-flex">
                <Icon name="check" class="size-4 text-brand" />
            </component>
            <span class="flex-1 truncate">{{ item.label }}</span>
            <span v-if="item.shortcut" class="pl-4 text-xs tracking-wide text-muted">{{ item.shortcut }}</span>
        </component>

        <component
            :is="parts.RadioGroup"
            v-else-if="item.type === 'radio'"
            :model-value="item.value"
            @update:model-value="item.onValueChange?.($event)"
        >
            <component
                :is="parts.RadioItem"
                v-for="option in item.options"
                :key="option.value"
                :value="option.value"
                :disabled="option.disabled"
                :class="checkableItemClass"
            >
                <component :is="parts.ItemIndicator" class="absolute left-2.5 inline-flex size-4 items-center justify-center">
                    <span class="size-2 rounded-full bg-brand" />
                </component>
                <span class="flex-1 truncate">{{ option.label }}</span>
            </component>
        </component>

        <component :is="parts.Sub" v-else-if="item.items">
            <component :is="parts.SubTrigger" :disabled="item.disabled" :class="[itemClass(item), 'data-[state=open]:bg-ink/5']">
                <Icon v-if="item.icon" :name="item.icon" :class="iconClass(item)" />
                <span class="flex-1 truncate">{{ item.label }}</span>
                <Icon name="chevron-right" class="size-4 text-muted" />
            </component>

            <component :is="parts.Portal">
                <component
                    :is="parts.SubContent"
                    :side-offset="4"
                    :align-offset="-7"
                    :collision-padding="8"
                    :class="[
                        'z-50 w-52 rounded-xl border border-line bg-surface p-1.5 text-ink shadow-lg shadow-black/8 outline-none',
                        'data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in',
                        parts.originClass,
                    ]"
                >
                    <MenuItems :kind="kind" :items="item.items" />
                </component>
            </component>
        </component>

        <component :is="parts.Item" v-else-if="item.href" as-child :disabled="item.disabled" @select="item.onSelect?.($event)">
            <Link :href="item.href" :method="linkMethod(item)" :as="linkMethod(item) === 'get' ? 'a' : 'button'" :class="itemClass(item)">
                <Icon v-if="item.icon" :name="item.icon" :class="iconClass(item)" />
                <span class="flex-1 truncate">{{ item.label }}</span>
                <span v-if="item.shortcut" class="pl-4 text-xs tracking-wide text-muted">{{ item.shortcut }}</span>
            </Link>
        </component>

        <component :is="parts.Item" v-else :disabled="item.disabled" :class="itemClass(item)" @select="item.onSelect?.($event)">
            <Icon v-if="item.icon" :name="item.icon" :class="iconClass(item)" />
            <span class="flex-1 truncate">{{ item.label }}</span>
            <span v-if="item.shortcut" class="pl-4 text-xs tracking-wide text-muted">{{ item.shortcut }}</span>
        </component>
    </template>
</template>
