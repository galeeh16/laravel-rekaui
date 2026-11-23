<script setup>
/**
 * Reusable modal built on reka-ui Dialog (focus trap, Escape / outside click to close,
 * scroll lock, rendered in a portal). The first form field in the body gets focus on open.
 * Sizes: sm, md, lg, xl
 *
 * Usage:
 * <Modal v-model:open="isOpen" title="Surat baru" description="Lengkapi data berikut.">
 *     <template #trigger>                       (optional — or control `open` yourself)
 *         <Button>Buka</Button>
 *     </template>
 *
 *     ...body...
 *
 *     <template #footer>
 *         <Button variant="secondary" @click="isOpen = false">Batal</Button>
 *         <Button type="submit" form="my-form">Simpan</Button>
 *     </template>
 * </Modal>
 *
 * Without v-model, the default and #footer slots receive `close`: <template #footer="{ close }">.
 * Pass `:dismissible="false"` so only buttons inside the modal close it (no Escape / outside click).
 */
import {
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
    DialogTrigger,
} from 'reka-ui';
import { useTemplateRef } from 'vue';
import Button from './Button.vue';

const open = defineModel('open', { type: Boolean, default: false });

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: null },
    size: { type: String, default: 'md' },
    /** Close on Escape and on a click outside the modal. */
    dismissible: { type: Boolean, default: true },
});

const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl',
};

const body = useTemplateRef('body');

/** Focus the first form field in the body instead of the close button, when there is one. */
function focusFirstField(event) {
    const field = body.value?.querySelector(
        'input:not([type=hidden]):not([aria-hidden=true]):not(:disabled), textarea:not(:disabled), [role=spinbutton], [role=combobox], [role=radio]',
    );

    if (field) {
        event.preventDefault();
        field.focus();
    }
}

/** Keeps the modal open on Escape / outside click when it is not dismissible. */
function preventDismiss(event) {
    if (!props.dismissible) {
        event.preventDefault();
    }
}
</script>

<template>
    <DialogRoot v-slot="{ close }" v-model:open="open">
        <DialogTrigger v-if="$slots.trigger" as-child>
            <slot name="trigger" />
        </DialogTrigger>

        <DialogPortal>
            <DialogOverlay
                class="fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] data-[state=closed]:animate-fade-out data-[state=open]:animate-fade-in"
            />

            <!-- Without a description, drop reka's aria-describedby so it does not point to a missing element. -->
            <DialogContent
                v-bind="description ? {} : { 'aria-describedby': undefined }"
                :class="[
                    'fixed top-1/2 left-1/2 z-50 flex max-h-[calc(100dvh-2rem)] w-[calc(100vw-2rem)] -translate-x-1/2 -translate-y-1/2 flex-col',
                    'rounded-2xl border border-line bg-surface text-ink shadow-xl shadow-black/10 outline-none',
                    'data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in',
                    sizeClasses[size],
                ]"
                @open-auto-focus="focusFirstField"
                @escape-key-down="preventDismiss"
                @interact-outside="preventDismiss"
            >
                <header class="flex items-start gap-4 px-6 pt-6 pb-4">
                    <div class="min-w-0 flex-1">
                        <DialogTitle class="text-lg font-semibold tracking-tight">{{ title }}</DialogTitle>
                        <DialogDescription v-if="description" class="mt-1 text-sm text-muted">
                            {{ description }}
                        </DialogDescription>
                    </div>

                    <DialogClose as-child>
                        <Button variant="ghost" size="icon-sm" icon="x" aria-label="Tutup" class="-mt-1 -mr-2" />
                    </DialogClose>
                </header>

                <div ref="body" class="min-h-0 flex-1 overflow-y-auto px-6 pb-6">
                    <slot :close="close" />
                </div>

                <footer
                    v-if="$slots.footer"
                    class="flex flex-col-reverse gap-2 border-t border-line px-6 py-4 sm:flex-row sm:justify-end"
                >
                    <slot name="footer" :close="close" />
                </footer>
            </DialogContent>
        </DialogPortal>
    </DialogRoot>
</template>
