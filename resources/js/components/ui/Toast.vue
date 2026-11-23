<script setup>
/**
 * One toast, built on reka-ui ToastRoot. Rendered by <Toaster /> for every entry created with
 * `toast()` / `promise()` from `@/composables/useToast`; you normally don't use it directly.
 * Variants: success, danger, info, warning (promise toasts map loading/success/error onto info/success/danger).
 * Swipe right or press the close button to dismiss; hovering or focusing the viewport pauses the timer.
 */
import { ToastAction, ToastClose, ToastDescription, ToastRoot, ToastTitle } from 'reka-ui';
import { computed } from 'vue';
import Button from './Button.vue';
import Icon from './Icon.vue';

const props = defineProps({
    /** Entry from reka's toast manager: { id, title, description, variant, status, actionProps, open, ... } */
    toast: { type: Object, required: true },
});

const variants = {
    success: { classes: 'bg-brand-soft text-brand-ink', icon: 'circle-check' },
    danger: { classes: 'bg-danger-soft text-danger', icon: 'triangle-alert' },
    info: { classes: 'bg-info-soft text-info', icon: 'info' },
    warning: { classes: 'bg-warning-soft text-warning', icon: 'triangle-alert' },
};

/** Toasts made with promise() carry a status instead of a variant. */
const statusVariants = { loading: 'info', success: 'success', error: 'danger' };

const current = computed(() => variants[statusVariants[props.toast.status] ?? props.toast.variant] ?? variants.info);
const isLoading = computed(() => props.toast.status === 'loading');
</script>

<template>
    <ToastRoot
        :toast="toast"
        :class="[
            'flex w-full items-start gap-3 rounded-2xl p-4 pr-3 shadow-lg shadow-black/8 ring-1 ring-ink/5 data-limited:hidden',
            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
            'data-[state=closed]:animate-slide-out-right data-[state=open]:animate-slide-in-right',
            'data-[swipe=cancel]:translate-x-0 data-[swipe=cancel]:transition-[translate] data-[swipe=end]:translate-x-(--reka-toast-swipe-end-x) data-[swipe=move]:translate-x-(--reka-toast-swipe-move-x)',
            current.classes,
        ]"
    >
        <Icon :name="isLoading ? 'loader' : current.icon" :class="['mt-0.5 size-4.5', { 'animate-spin': isLoading }]" />

        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
            <ToastTitle class="text-sm font-semibold" />
            <ToastDescription class="text-sm text-ink/75" />

            <ToastAction v-if="toast.actionProps" as-child>
                <Button variant="secondary" size="sm" class="mt-2 self-start">{{ toast.actionProps.label }}</Button>
            </ToastAction>
        </div>

        <ToastClose as-child>
            <Button variant="ghost" size="icon-sm" icon="x" aria-label="Tutup notifikasi" class="-my-1.5" />
        </ToastClose>
    </ToastRoot>
</template>
