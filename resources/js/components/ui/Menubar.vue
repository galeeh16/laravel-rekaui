<script setup>
/**
 * Built on reka-ui Menubar (desktop-style menu bar: arrow keys move between menus, and once a menu
 * is open, hovering another trigger switches to it). Each menu's items are data; their shapes are
 * listed in `MenuItems.vue`.
 *
 * Usage:
 * <Menubar
 *     :menus="[
 *         { label: 'Berkas', items: [{ label: 'Surat baru', icon: 'plus', shortcut: '⌘N', onSelect: create }] },
 *         { label: 'Tampilan', items: [{ type: 'checkbox', label: 'Penggaris', checked: showRuler, onCheckedChange: (value) => (showRuler = value) }] },
 *     ]"
 * />
 *
 * `v-model` holds the value of the open menu ('' when all are closed).
 */
import { MenubarContent, MenubarMenu, MenubarPortal, MenubarRoot, MenubarTrigger } from 'reka-ui';
import MenuItems from './MenuItems.vue';

const model = defineModel({ type: String, default: undefined });

defineProps({
    /** @type {Array<{ label: string, value?: string, disabled?: boolean, items: Array<Record<string, any>> }>} */
    menus: { type: Array, required: true },
    width: { type: String, default: 'w-56' },
    /** Arrow keys wrap from the last menu to the first. */
    loop: { type: Boolean, default: true },
});

/** Menu values end up in element ids, so fall back to the index instead of the (possibly spaced) label. */
const menuValue = (menu, index) => menu.value ?? `menu-${index}`;
</script>

<template>
    <MenubarRoot
        v-model="model"
        :loop="loop"
        class="flex w-fit items-center gap-0.5 rounded-xl border border-line bg-surface p-1 shadow-xs"
    >
        <MenubarMenu v-for="(menu, index) in menus" :key="menuValue(menu, index)" :value="menuValue(menu, index)">
            <MenubarTrigger
                :disabled="menu.disabled"
                class="flex h-8 cursor-pointer items-center rounded-lg px-3 text-sm font-medium text-ink outline-none select-none transition-colors hover:bg-ink/5 data-disabled:pointer-events-none data-disabled:opacity-50 data-highlighted:bg-ink/5 data-[state=open]:bg-ink/5"
            >
                {{ menu.label }}
            </MenubarTrigger>

            <MenubarPortal>
                <MenubarContent
                    align="start"
                    :side-offset="8"
                    :align-offset="-4"
                    :collision-padding="8"
                    :class="[
                        'z-50 origin-(--reka-menubar-content-transform-origin) rounded-xl border border-line bg-surface p-1.5 text-ink shadow-lg shadow-black/8 outline-none',
                        'data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in',
                        width,
                    ]"
                >
                    <MenuItems kind="menubar" :items="menu.items" />
                </MenubarContent>
            </MenubarPortal>
        </MenubarMenu>
    </MenubarRoot>
</template>
