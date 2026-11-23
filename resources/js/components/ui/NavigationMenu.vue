<script setup>
/**
 * Built on reka-ui NavigationMenu (site navigation: hovering or clicking a trigger opens a panel of
 * links, arrow keys move between items, the panel resizes smoothly when switching items).
 * Links render as Inertia <Link>s.
 *
 * Usage:
 * <NavigationMenu
 *     :items="[
 *         { label: 'Dashboard', href: route('dashboard'), active: true },
 *         {
 *             label: 'Surat',
 *             value: 'letters',
 *             children: [
 *                 { label: 'Draf', href: route('dashboard', { status: 'draft' }), icon: 'pen-line', description: 'Surat yang belum dikirim.' },
 *             ],
 *         },
 *     ]"
 * />
 *
 * `v-model` holds the value of the open item ('' when closed). Panels with more than three links
 * use two columns unless the item sets `columns`.
 */
import { Link } from '@inertiajs/vue3';
import {
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuRoot,
    NavigationMenuTrigger,
    NavigationMenuViewport,
} from 'reka-ui';
import Icon from './Icon.vue';

const model = defineModel({ type: String, default: undefined });

defineProps({
    /**
     * @type {Array<{
     *     label: string, value?: string, icon?: string, href?: string, active?: boolean, columns?: 1 | 2,
     *     children?: Array<{ label: string, href: string, icon?: string, description?: string, active?: boolean }>
     * }>}
     */
    items: { type: Array, required: true },
    /** Where the panel lines up with its trigger. */
    align: { type: String, default: 'start' },
});

/** Item values end up in element ids, so fall back to the index instead of the (possibly spaced) label. */
const itemValue = (item, index) => item.value ?? `item-${index}`;

const columnCount = (item) => item.columns ?? (item.children.length > 3 ? 2 : 1);

const topLevelClass =
    'group inline-flex h-9 cursor-pointer items-center gap-1.5 rounded-lg px-3 text-sm font-medium whitespace-nowrap text-ink outline-none transition-colors hover:bg-ink/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand';
</script>

<template>
    <NavigationMenuRoot v-model="model" class="relative z-10 flex max-w-max">
        <NavigationMenuList class="flex items-center gap-1 rounded-xl border border-line bg-surface p-1 shadow-xs">
            <NavigationMenuItem v-for="(item, index) in items" :key="itemValue(item, index)" :value="itemValue(item, index)">
                <template v-if="item.children">
                    <NavigationMenuTrigger :class="[topLevelClass, 'data-[state=open]:bg-ink/5']">
                        <Icon v-if="item.icon" :name="item.icon" class="size-4 text-muted" />
                        {{ item.label }}
                        <Icon
                            name="chevron-down"
                            class="size-4 text-muted transition-transform duration-200 group-data-[state=open]:rotate-180"
                        />
                    </NavigationMenuTrigger>

                    <NavigationMenuContent
                        class="absolute top-0 left-0 data-[motion^=from-]:animate-fade-in data-[motion^=to-]:animate-fade-out"
                    >
                        <ul :class="['grid gap-1 p-1.5', columnCount(item) === 2 ? 'w-[34rem] grid-cols-2' : 'w-80']">
                            <li v-for="child in item.children" :key="child.href">
                                <NavigationMenuLink as-child :active="child.active">
                                    <Link
                                        :href="child.href"
                                        class="flex gap-3 rounded-lg p-2.5 outline-none transition-colors hover:bg-ink/5 focus-visible:bg-ink/5 data-active:bg-brand-soft"
                                    >
                                        <span
                                            v-if="child.icon"
                                            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-brand-soft text-brand-ink"
                                        >
                                            <Icon :name="child.icon" class="size-4.5" />
                                        </span>

                                        <span class="min-w-0">
                                            <span class="block text-sm font-medium text-ink">{{ child.label }}</span>
                                            <span v-if="child.description" class="mt-0.5 line-clamp-2 block text-xs text-muted">
                                                {{ child.description }}
                                            </span>
                                        </span>
                                    </Link>
                                </NavigationMenuLink>
                            </li>
                        </ul>
                    </NavigationMenuContent>
                </template>

                <NavigationMenuLink v-else as-child :active="item.active">
                    <Link :href="item.href" :class="[topLevelClass, 'data-active:bg-brand-soft data-active:text-brand-ink']">
                        <Icon v-if="item.icon" :name="item.icon" class="size-4 text-muted group-data-active:text-brand-ink" />
                        {{ item.label }}
                    </Link>
                </NavigationMenuLink>
            </NavigationMenuItem>
        </NavigationMenuList>

        <!-- content-box: the border sits outside the size reka measures, so the panel isn't clipped. -->
        <NavigationMenuViewport
            :align="align"
            class="absolute top-full left-(--reka-navigation-menu-viewport-left) mt-2 box-content h-(--reka-navigation-menu-viewport-height) w-(--reka-navigation-menu-viewport-width) origin-top overflow-hidden rounded-xl border border-line bg-surface text-ink shadow-lg shadow-black/8 transition-[width,height,left] duration-200 ease-out data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in"
        />
    </NavigationMenuRoot>
</template>
