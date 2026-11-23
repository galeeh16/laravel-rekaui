<script setup>
/**
 * Persistent dashboard layout (set as the default in `app.js`).
 * Pages pass layout props with `defineOptions({ layout: { breadcrumbs: [...] } })`.
 */
import { Link, router, usePage } from '@inertiajs/vue3';
import { DialogContent, DialogOverlay, DialogPortal, DialogRoot, DialogTitle, VisuallyHidden } from 'reka-ui';
import { computed, onBeforeUnmount, onMounted, ref, useTemplateRef } from 'vue';
import { route } from 'ziggy-js';
import AppSidebar from '@/components/AppSidebar.vue';
import NewLetterModal from '@/components/NewLetterModal.vue';
import Alert from '@/components/ui/Alert.vue';
import Button from '@/components/ui/Button.vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuLabel from '@/components/ui/DropdownMenuLabel.vue';
import Icon from '@/components/ui/Icon.vue';
import Input from '@/components/ui/Input.vue';
import Toaster from '@/components/ui/Toaster.vue';
import { useSidebar } from '@/composables/useSidebar';

defineProps({
    /** @type {Array<{ label: string, href?: string }>} */
    breadcrumbs: { type: Array, default: () => [] },
});

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

const globalSearch = ref('');
const searchInput = useTemplateRef('searchInput');

function submitSearch() {
    router.get(route('dashboard', globalSearch.value ? { q: globalSearch.value } : {}));
}

function handleShortcut(event) {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        searchInput.value?.focus();
        searchInput.value?.select();
    }
}

const desktopQuery = window.matchMedia('(min-width: 64rem)');
const closeMobileSidebar = () => (isMobileOpen.value = false);
let removeNavigateListener = null;

/** True once the page is scrolled, so the sticky header shows a border and shadow. */
const isScrolled = ref(false);
const updateScrolled = () => (isScrolled.value = window.scrollY > 0);

onMounted(() => {
    window.addEventListener('keydown', handleShortcut);
    window.addEventListener('scroll', updateScrolled, { passive: true });
    desktopQuery.addEventListener('change', closeMobileSidebar);
    removeNavigateListener = router.on('navigate', closeMobileSidebar);
    updateScrolled();
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleShortcut);
    window.removeEventListener('scroll', updateScrolled);
    desktopQuery.removeEventListener('change', closeMobileSidebar);
    removeNavigateListener?.();
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

                        <span class="truncate">
                            <span class="hidden sm:inline">{{ today }}</span>

                            <template v-for="(crumb, index) in breadcrumbs" :key="crumb.label">
                                <span :class="['mx-1 text-muted/70', { 'hidden sm:inline': index === 0 }]" aria-hidden="true">
                                    {{ index === 0 ? '·' : '›' }}
                                </span>

                                <Link v-if="crumb.href" :href="crumb.href" class="hover:text-ink">{{ crumb.label }}</Link>
                                <span v-else class="text-ink/80">{{ crumb.label }}</span>
                            </template>
                        </span>
                    </div>
                </div>

                <form role="search" class="hidden w-full max-w-xs md:block xl:max-w-md" @submit.prevent="submitSearch">
                    <Input
                        ref="searchInput"
                        v-model="globalSearch"
                        type="search"
                        icon="search"
                        kbd="⌘K"
                        placeholder="Cari surat, penerima, perihal…"
                        aria-label="Cari"
                    />
                </form>

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
