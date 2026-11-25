<script setup>
/**
 * Persistent dashboard layout (set as the default in `app.js`).
 */
import { router, usePage } from '@inertiajs/vue3';
import { DialogContent, DialogOverlay, DialogPortal, DialogRoot, DialogTitle, VisuallyHidden } from 'reka-ui';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import AppSidebar from '@/components/AppSidebar.vue';
import NewLetterModal from '@/components/NewLetterModal.vue';
import Alert from '@/components/ui/Alert.vue';
import Button from '@/components/ui/Button.vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuLabel from '@/components/ui/DropdownMenuLabel.vue';
import Icon from '@/components/ui/Icon.vue';
import Toaster from '@/components/ui/Toaster.vue';
import { useSidebar } from '@/composables/useSidebar';
import { toast } from '@/composables/useToast';

const page = usePage();
const user = computed(() => page.props.auth.user);
const firstName = computed(() => user.value?.name.split(' ')[0] ?? '');

const now = new Date();

const greeting = (() => {
    const hour = now.getHours();

    if (hour < 11) {
        return 'Selamat pagi';
    }

    if (hour < 15) {
        return 'Selamat siang';
    }

    return hour < 18 ? 'Selamat sore' : 'Selamat malam';
})();

const today = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
}).format(now);

const { isMobileOpen } = useSidebar();

const desktopQuery = window.matchMedia('(min-width: 64rem)');
const closeMobileSidebar = () => (isMobileOpen.value = false);
let removeNavigateListener = null;

/**
 * Server-side toasts: `Inertia::flash('toast', ['title' => ..., 'description' => ..., 'variant' => 'success'])`.
 */
let removeFlashListener = null;
const showFlashToast = (event) => {
    const flashToast = event.detail.flash.toast;

    if (flashToast?.title) {
        toast(flashToast);
    }
};

/** True once the page is scrolled, so the sticky header shows a border and shadow. */
const isScrolled = ref(false);
const updateScrolled = () => (isScrolled.value = window.scrollY > 0);

onMounted(() => {
    window.addEventListener('scroll', updateScrolled, { passive: true });
    desktopQuery.addEventListener('change', closeMobileSidebar);
    removeNavigateListener = router.on('navigate', closeMobileSidebar);
    removeFlashListener = router.on('flash', showFlashToast);
    updateScrolled();
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateScrolled);
    desktopQuery.removeEventListener('change', closeMobileSidebar);
    removeNavigateListener?.();
    removeFlashListener?.();
});
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Desktop sidebar -->
        <aside
            class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-line bg-sidebar p-4 transition-[width] duration-200 lg:flex lg:sidebar-collapsed:w-20 lg:sidebar-collapsed:px-3"
        >
            <AppSidebar />
        </aside>

        <!-- Mobile drawer -->
        <DialogRoot v-model:open="isMobileOpen">
            <DialogPortal>
                <DialogOverlay
                    class="fixed inset-0 z-40 bg-black/40 data-[state=closed]:animate-fade-out data-[state=open]:animate-fade-in"
                />
                <DialogContent
                    :aria-describedby="undefined"
                    class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] flex-col border-r border-line bg-sidebar p-4 text-ink outline-none data-[state=closed]:animate-slide-out-left data-[state=open]:animate-slide-in-left"
                >
                    <VisuallyHidden>
                        <DialogTitle>Menu navigasi</DialogTitle>
                    </VisuallyHidden>

                    <AppSidebar mobile @close="closeMobileSidebar" />
                </DialogContent>
            </DialogPortal>
        </DialogRoot>

        <!-- Main -->
        <div class="flex min-w-0 flex-1 flex-col">
            <header
                :class="[
                    'sticky top-0 z-30 flex items-center gap-3 border-b bg-canvas/85 px-4 py-4 backdrop-blur-md transition-[border-color,box-shadow] duration-200 sm:px-6 lg:px-10',
                    isScrolled ? 'border-line shadow-sm shadow-black/5' : 'border-transparent',
                ]"
            >
                <Button
                    variant="secondary"
                    size="icon"
                    icon="menu"
                    class="lg:hidden"
                    aria-label="Buka menu"
                    @click="isMobileOpen = true"
                />

                <div class="min-w-0 flex-1">
                    <p class="truncate text-lg font-medium sm:text-xl">{{ greeting }}, {{ firstName }}</p>

                    <div class="mt-0.5 flex items-center gap-1.5 text-sm text-muted">
                        <span class="size-2 shrink-0 rounded-full bg-brand ring-3 ring-brand/20" aria-hidden="true" />

                        <span class="truncate">{{ today }}</span>
                    </div>
                </div>

                <DropdownMenu width="w-80">
                    <template #trigger>
                        <Button variant="secondary" size="icon-lg" icon="bell" aria-label="Notifikasi" />
                    </template>

                    <DropdownMenuLabel>Notifikasi</DropdownMenuLabel>

                    <div class="flex flex-col items-center gap-2 px-4 py-8 text-center text-sm text-muted">
                        <Icon name="bell" class="size-6" />
                        Belum ada notifikasi.
                    </div>
                </DropdownMenu>
            </header>

            <main class="flex-1 px-4 pb-12 sm:px-6 lg:px-10">
                <Alert v-if="page.flash.success" variant="success" class="mt-2">{{ page.flash.success }}</Alert>

                <slot />
            </main>
        </div>

        <NewLetterModal />

        <!-- Global toasts: call `toast({ title, variant })` from useToast() anywhere. -->
        <Toaster />
    </div>
</template>
