<script setup>
/**
 * Renders the toasts created with `toast()` from `@/composables/useToast`
 * (reka-ui ToastProvider + ToastViewport in a portal, bottom-right, swipe right to dismiss, F8 focuses it).
 * Mount it once, e.g. in the app layout. If several are mounted, only the first one renders toasts.
 *
 * Usage: <Toaster />   ·   <Toaster :duration="8000" :limit="5" />
 */
import { ToastPortal, ToastProvider, ToastViewport, useToastManager } from 'reka-ui';
import { defineComponent } from 'vue';
import { toastManager, useToasterRegistration } from '@/composables/useToast';
import Toast from './Toast.vue';

defineProps({
    /** Milliseconds before a toast closes itself (per-toast `duration` wins). */
    duration: { type: Number, default: 5000 },
    /** Maximum number of visible toasts; older ones are hidden until newer ones close. */
    limit: { type: Number, default: 3 },
});

const { isActive } = useToasterRegistration();

/** Renderless helper: useToastManager() only works below ToastProvider, so the queue is read one level down. */
const ToastQueue = defineComponent({
    setup(_, { slots }) {
        const { toasts } = useToastManager();

        return () => slots.default?.({ toasts: toasts.value });
    },
});
</script>

<template>
    <ToastProvider
        v-if="isActive"
        :toast-manager="toastManager"
        :duration="duration"
        :limit="limit"
        label="Notifikasi"
        swipe-direction="right"
    >
        <ToastQueue v-slot="{ toasts }">
            <Toast v-for="item in toasts" :key="item.id" :toast="item" />
        </ToastQueue>

        <ToastPortal>
            <ToastViewport
                label="Notifikasi ({hotkey})"
                class="fixed right-0 bottom-0 z-100 flex w-full flex-col gap-3 p-4 outline-none sm:max-w-sm"
            />
        </ToastPortal>
    </ToastProvider>
</template>
