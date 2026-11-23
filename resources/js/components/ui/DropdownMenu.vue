<script setup>
/**
 * Built on reka-ui DropdownMenu (keyboard navigation, focus management, rendered in a portal
 * so it is never clipped by tables or scroll containers).
 *
 * Usage:
 * <DropdownMenu>
 *     <template #trigger>
 *         <Button variant="ghost" size="icon-sm" icon="ellipsis" aria-label="Aksi" />
 *     </template>
 *
 *     <DropdownMenuItem icon="pencil" href="/letters/1/edit">Edit</DropdownMenuItem>
 *     <DropdownMenuSeparator />
 *     <DropdownMenuItem icon="trash" variant="danger" @select="destroy">Hapus</DropdownMenuItem>
 * </DropdownMenu>
 *
 * Or pass the items as data, which also supports checkbox / radio items and sub-menus
 * (item shapes are listed in `MenuItems.vue`):
 * <DropdownMenu :items="[{ label: 'Edit', icon: 'pencil', onSelect: edit }, { type: 'separator' }]">
 *     <template #trigger>...</template>
 * </DropdownMenu>
 */
import { DropdownMenuContent, DropdownMenuPortal, DropdownMenuRoot, DropdownMenuTrigger } from 'reka-ui';
import MenuItems from './MenuItems.vue';

defineProps({
    align: { type: String, default: 'end' },
    side: { type: String, default: 'bottom' },
    width: { type: String, default: 'w-56' },
    /** Optional data-driven items, rendered before the default slot. */
    items: { type: Array, default: null },
});
</script>

<template>
    <DropdownMenuRoot>
        <DropdownMenuTrigger as-child>
            <slot name="trigger" />
        </DropdownMenuTrigger>

        <DropdownMenuPortal>
            <DropdownMenuContent
                :align="align"
                :side="side"
                :side-offset="6"
                :collision-padding="8"
                :class="[
                    'z-50 origin-(--reka-dropdown-menu-content-transform-origin) rounded-xl border border-line bg-surface p-1.5 text-ink shadow-lg shadow-black/8 outline-none',
                    'data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in',
                    width,
                ]"
            >
                <MenuItems v-if="items" kind="dropdown" :items="items" />
                <slot />
            </DropdownMenuContent>
        </DropdownMenuPortal>
    </DropdownMenuRoot>
</template>
