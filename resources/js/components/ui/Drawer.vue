<script setup>
/**
 * Built on reka-ui Drawer (swipe or drag to dismiss, focus trap, scroll lock, rendered in a portal).
 * Sides: bottom (sheet with a grab handle, swipe down to close), right (side panel, swipe right to close)
 *
 * Usage:
 * <Drawer v-model:open="isOpen" title="Filter surat" description="Saring daftar surat keluar.">
 *     <template #trigger>                       (optional — or control `open` yourself)
 *         <Button variant="secondary" icon="list">Filter</Button>
 *     </template>
 *
 *     ...body...
 *
 *     <template #footer="{ close }">
 *         <Button variant="secondary" @click="close">Batal</Button>
 *         <Button @click="apply">Terapkan</Button>
 *     </template>
 * </Drawer>
 */
import {
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerHandle,
    DrawerOverlay,
    DrawerPortal,
    DrawerRoot,
    DrawerTitle,
    DrawerTrigger,
} from 'reka-ui';
import { computed } from 'vue';
import Button from './Button.vue';

const open = defineModel('open', { type: Boolean, default: false });

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: null },
    side: { type: String, default: 'bottom' },
});

/**
 * reka-ui only writes the drag distance to `--drawer-swipe-movement-x/y`; the panel follows the
 * pointer by translating with it, and eases back when a swipe is cancelled (no transition mid-drag).
 */
const sides = {
    bottom: {
        swipeDirection: 'down',
        classes: [
            'inset-x-0 bottom-0 mx-auto max-h-[85dvh] w-full max-w-2xl rounded-t-3xl border-x border-t',
            'translate-y-(--drawer-swipe-movement-y) data-[state=closed]:animate-slide-out-bottom data-[state=open]:animate-slide-in-bottom',
        ],
    },
    right: {
        swipeDirection: 'right',
        classes: [
            'inset-y-0 right-0 w-[calc(100vw-2rem)] max-w-md rounded-l-2xl border-y border-l',
            'translate-x-(--drawer-swipe-movement-x) data-[state=closed]:animate-slide-out-right data-[state=open]:animate-slide-in-right',
        ],
    },
};

const current = computed(() => sides[props.side] ?? sides.bottom);
</script>

<template>
    <DrawerRoot v-slot="{ close }" v-model:open="open" :swipe-direction="current.swipeDirection">
        <DrawerTrigger v-if="$slots.trigger" as-child>
            <slot name="trigger" />
        </DrawerTrigger>

        <DrawerPortal>
            <DrawerOverlay
                class="fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] data-[state=closed]:animate-fade-out data-[state=open]:animate-fade-in"
            />

            <!-- Without a description, drop reka's aria-describedby so it does not point to a missing element. -->
            <DrawerContent
                v-bind="description ? {} : { 'aria-describedby': undefined }"
                :class="[
                    'fixed z-50 flex flex-col border-line bg-surface text-ink shadow-xl shadow-black/10 outline-none',
                    'transition-[translate] duration-200 ease-out data-swiping:transition-none',
                    current.classes,
                ]"
            >
                <DrawerHandle v-if="side === 'bottom'" class="mx-auto mt-3 h-1.5 w-12 shrink-0 rounded-full bg-ink/15" />

                <header class="flex items-start gap-4 px-6 pt-5 pb-4">
                    <div class="min-w-0 flex-1">
                        <DrawerTitle class="text-lg font-semibold tracking-tight">{{ title }}</DrawerTitle>
                        <DrawerDescription v-if="description" class="mt-1 text-sm text-muted">
                            {{ description }}
                        </DrawerDescription>
                    </div>

                    <DrawerClose as-child>
                        <Button variant="ghost" size="icon-sm" icon="x" aria-label="Tutup" class="-mt-1 -mr-2" />
                    </DrawerClose>
                </header>

                <div class="min-h-0 flex-1 overflow-y-auto px-6 pb-6">
                    <slot :close="close" />
                </div>

                <footer
                    v-if="$slots.footer"
                    class="flex flex-col-reverse gap-2 border-t border-line px-6 py-4 sm:flex-row sm:justify-end"
                >
                    <slot name="footer" :close="close" />
                </footer>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>
