<script setup>
/**
 * Built on reka-ui AlertDialog (focus trap with the cancel button focused first, not dismissable by
 * clicking outside, Escape or "Batal" cancels, rendered in a portal).
 * Variants: default, danger
 *
 * Usage:
 * <AlertDialog
 *     variant="danger"
 *     title="Hapus surat ini?"
 *     description="Surat 015/SK/XI/2026 akan dihapus permanen."
 *     confirm-label="Hapus"
 *     @confirm="destroy"
 * >
 *     <template #trigger>                       (optional — or control it with v-model:open)
 *         <Button variant="danger" icon="trash">Hapus</Button>
 *     </template>
 * </AlertDialog>
 *
 * The dialog closes right after `confirm`. For a request, pass `:close-on-confirm="false"` and
 * `:processing="form.processing"`, then set `open` to false yourself when it succeeds.
 */
import {
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogOverlay,
    AlertDialogPortal,
    AlertDialogRoot,
    AlertDialogTitle,
    AlertDialogTrigger,
} from 'reka-ui';
import { computed } from 'vue';
import Button from './Button.vue';
import Icon from './Icon.vue';

const open = defineModel('open', { type: Boolean, default: false });

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: null },
    variant: { type: String, default: 'default' },
    /** Icon in the tinted circle. Defaults to `triangle-alert` (danger) or `circle-help`. */
    icon: { type: String, default: null },
    confirmLabel: { type: String, default: 'Lanjutkan' },
    cancelLabel: { type: String, default: 'Batal' },
    /** Shows a spinner on the confirm button and blocks closing while true. */
    processing: { type: Boolean, default: false },
    closeOnConfirm: { type: Boolean, default: true },
});

const emit = defineEmits(['confirm']);

const isDanger = computed(() => props.variant === 'danger');

const iconName = computed(() => props.icon ?? (isDanger.value ? 'triangle-alert' : 'circle-help'));

function confirm() {
    emit('confirm');

    if (props.closeOnConfirm) {
        open.value = false;
    }
}

/** Escape would otherwise close the dialog in the middle of a request. */
function preventCloseWhileProcessing(event) {
    if (props.processing) {
        event.preventDefault();
    }
}
</script>

<template>
    <AlertDialogRoot v-model:open="open">
        <AlertDialogTrigger v-if="$slots.trigger" as-child>
            <slot name="trigger" />
        </AlertDialogTrigger>

        <AlertDialogPortal>
            <AlertDialogOverlay
                class="fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] data-[state=closed]:animate-fade-out data-[state=open]:animate-fade-in"
            />

            <!-- Without a description, drop reka's aria-describedby so it does not point to a missing element. -->
            <AlertDialogContent
                v-bind="description ? {} : { 'aria-describedby': undefined }"
                class="fixed top-1/2 left-1/2 z-50 w-[calc(100vw-2rem)] max-w-md -translate-x-1/2 -translate-y-1/2 rounded-2xl border border-line bg-surface p-6 text-ink shadow-xl shadow-black/10 outline-none data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
                @escape-key-down="preventCloseWhileProcessing"
            >
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                    <span
                        :class="[
                            'flex size-11 shrink-0 items-center justify-center rounded-full',
                            isDanger ? 'bg-danger-soft text-danger' : 'bg-brand-soft text-brand-ink',
                        ]"
                    >
                        <Icon :name="iconName" class="size-5" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <AlertDialogTitle class="text-lg font-semibold tracking-tight">{{ title }}</AlertDialogTitle>
                        <AlertDialogDescription v-if="description" class="mt-1.5 text-sm text-muted">
                            {{ description }}
                        </AlertDialogDescription>

                        <div v-if="$slots.default" class="mt-4 text-sm">
                            <slot />
                        </div>
                    </div>
                </div>

                <footer class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <AlertDialogCancel as-child>
                        <Button variant="secondary" :disabled="processing">{{ cancelLabel }}</Button>
                    </AlertDialogCancel>

                    <Button :variant="isDanger ? 'danger' : 'primary'" :disabled="processing" @click="confirm">
                        <Icon v-if="processing" name="loader" class="size-4.5 animate-spin" />
                        {{ confirmLabel }}
                    </Button>
                </footer>
            </AlertDialogContent>
        </AlertDialogPortal>
    </AlertDialogRoot>
</template>
