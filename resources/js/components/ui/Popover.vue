<script setup>
/**
 * Built on reka-ui Popover (opens on click, focus moves inside, Escape / outside click to close,
 * rendered in a portal and flips to stay on screen).
 *
 * Usage:
 * <Popover width="w-80">
 *     <template #trigger>
 *         <Button variant="secondary" icon="users">Bagikan</Button>
 *     </template>
 *
 *     ...content...   (slot props: { close })
 * </Popover>
 */
import { PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger } from 'reka-ui';

const open = defineModel('open', { type: Boolean, default: false });

defineProps({
    side: { type: String, default: 'bottom' },
    align: { type: String, default: 'center' },
    width: { type: String, default: 'w-72' },
    /** Trap focus inside and make the rest of the page inert while open. */
    modal: { type: Boolean, default: false },
});
</script>

<template>
    <PopoverRoot v-slot="{ close }" v-model:open="open" :modal="modal">
        <PopoverTrigger as-child>
            <slot name="trigger" />
        </PopoverTrigger>

        <PopoverPortal>
            <PopoverContent
                :side="side"
                :align="align"
                :side-offset="8"
                :collision-padding="8"
                :class="[
                    'z-50 origin-(--reka-popover-content-transform-origin) rounded-xl border border-line bg-surface p-4 text-ink shadow-lg shadow-black/8 outline-none',
                    'data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in',
                    width,
                ]"
            >
                <slot :close="close" />
            </PopoverContent>
        </PopoverPortal>
    </PopoverRoot>
</template>
