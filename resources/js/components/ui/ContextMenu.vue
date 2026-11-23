<script setup>
/**
 * Built on reka-ui ContextMenu (opens at the pointer on right click or long press, keyboard
 * navigation, sub-menus, rendered in a portal). Items are data; their shapes are listed in `MenuItems.vue`.
 *
 * Usage:
 * <ContextMenu
 *     :items="[
 *         { label: 'Buka surat', icon: 'eye', onSelect: openLetter },
 *         { label: 'Pindahkan ke', icon: 'folder', items: [{ label: 'Arsip', onSelect: archive }] },
 *         { type: 'separator' },
 *         { label: 'Hapus', icon: 'trash', variant: 'danger', onSelect: destroy },
 *     ]"
 * >
 *     <div class="data-[state=open]:bg-ink/5">...one element that reacts to right click...</div>
 * </ContextMenu>
 */
import { ContextMenuContent, ContextMenuPortal, ContextMenuRoot, ContextMenuTrigger } from 'reka-ui';
import MenuItems from './MenuItems.vue';

const open = defineModel('open', { type: Boolean, default: undefined });

defineProps({
    /** @type {Array<Record<string, any>>} */
    items: { type: Array, required: true },
    width: { type: String, default: 'w-56' },
    /** Keep the browser's own context menu instead. */
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <ContextMenuRoot v-model:open="open">
        <ContextMenuTrigger as-child :disabled="disabled">
            <slot />
        </ContextMenuTrigger>

        <ContextMenuPortal>
            <ContextMenuContent
                :collision-padding="8"
                :class="[
                    'z-50 origin-(--reka-context-menu-content-transform-origin) rounded-xl border border-line bg-surface p-1.5 text-ink shadow-lg shadow-black/8 outline-none',
                    'data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in',
                    width,
                ]"
            >
                <MenuItems kind="context" :items="items" />
            </ContextMenuContent>
        </ContextMenuPortal>
    </ContextMenuRoot>
</template>
