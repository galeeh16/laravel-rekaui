<script setup>
/**
 * Sidebar content, shared by the desktop <aside> and the mobile drawer in `layouts/AppLayout.vue`.
 */
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import AppLogo from '@/components/AppLogo.vue';
import NavCollapsible from '@/components/NavCollapsible.vue';
import NavGroup from '@/components/NavGroup.vue';
import NavItem from '@/components/NavItem.vue';
import Avatar from '@/components/ui/Avatar.vue';
import Button from '@/components/ui/Button.vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Icon from '@/components/ui/Icon.vue';
import Switch from '@/components/ui/Switch.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useSidebar } from '@/composables/useSidebar';

defineProps({
    mobile: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const page = usePage();
const user = computed(() => page.props.auth.user);
const appName = computed(() => page.props.name);

const { isDark, setDark } = useAppearance();
const { toggleCollapsed } = useSidebar();

const darkMode = computed({
    get: () => isDark.value,
    set: (value) => setDark(value),
});

/**
 * Whether the browser is on the given named route. Reading `page.url` makes computed
 * values that call this re-evaluate after every Inertia visit.
 */
const isCurrentRoute = (name) => Boolean(page.url) && route().current(name);

const mainNavigation = computed(() => [
    { label: 'Dashboard', href: route('dashboard'), icon: 'layout-grid', active: isCurrentRoute('dashboard') },
]);

/** Children of "Components": the overview plus one link per reka-ui component (shared prop). */
const componentLinks = computed(() => [
    { label: 'Semua komponen', href: route('components.index'), active: isCurrentRoute('components.index') },
    ...(page.props.componentNavigation ?? []).map((component) => ({
        label: component.label,
        href: route(component.route),
        active: isCurrentRoute(component.route),
    })),
]);
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex items-center gap-3 lg:sidebar-collapsed:flex-col">
            <Link :href="route('dashboard')" class="flex min-w-0 flex-1 items-center gap-3">
                <AppLogo />

                <span class="min-w-0 lg:sidebar-collapsed:hidden">
                    <span class="block truncate text-lg leading-tight font-semibold">{{ appName }}</span>
                    <span class="block truncate text-xs text-muted">Manajemen surat</span>
                </span>
            </Link>

            <Button
                v-if="mobile"
                variant="ghost"
                size="icon-sm"
                icon="x"
                aria-label="Tutup menu"
                @click="emit('close')"
            />
            <Button
                v-else
                variant="secondary"
                size="icon-sm"
                icon="panel-left"
                aria-label="Ciutkan atau lebarkan sidebar"
                @click="toggleCollapsed"
            />
        </div>

        <nav class="-mx-1 mt-6 flex flex-1 flex-col gap-6 overflow-y-auto px-1" aria-label="Menu utama">
            <NavGroup heading="Utama">
                <NavItem v-for="item in mainNavigation" :key="item.label" v-bind="item" />
            </NavGroup>

            <NavGroup heading="Dokumentasi">
                <NavCollapsible label="Components" icon="component" :children="componentLinks" />
            </NavGroup>
        </nav>

        <div class="mt-4 flex flex-col gap-0.5">
            <div
                class="flex h-11 items-center gap-3 border-t border-line px-3 pt-2 lg:sidebar-collapsed:justify-center lg:sidebar-collapsed:px-0"
            >
                <Icon name="sun" class="size-5 text-muted lg:sidebar-collapsed:hidden" />
                <span class="flex-1 text-[0.875rem] text-ink/80 lg:sidebar-collapsed:hidden">Mode gelap</span>
                <Switch v-model="darkMode" aria-label="Mode gelap" />
            </div>

            <DropdownMenu side="top" align="start" width="w-60">
                <template #trigger>
                    <button
                        type="button"
                        class="mt-3 flex w-full cursor-pointer items-center gap-3 rounded-xl border border-line bg-surface p-2 text-left shadow-xs transition-colors hover:bg-canvas lg:sidebar-collapsed:justify-center lg:sidebar-collapsed:border-transparent lg:sidebar-collapsed:bg-transparent lg:sidebar-collapsed:shadow-none"
                        aria-label="Menu akun"
                    >
                        <Avatar :name="user.name" />

                        <span class="min-w-0 flex-1 lg:sidebar-collapsed:hidden">
                            <span class="block truncate text-sm font-medium">{{ user.name }}</span>
                            <span class="block truncate text-xs text-muted">{{ user.email }}</span>
                        </span>

                        <Icon name="chevrons-up-down" class="size-4 text-muted lg:sidebar-collapsed:hidden" />
                    </button>
                </template>

                <DropdownMenuLabel>Akun saya</DropdownMenuLabel>
                <DropdownMenuItem icon="user">Profil</DropdownMenuItem>
                <DropdownMenuItem icon="settings">Pengaturan</DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem :href="route('logout')" method="post" icon="log-out" variant="danger">Logout</DropdownMenuItem>
            </DropdownMenu>
        </div>
    </div>
</template>
