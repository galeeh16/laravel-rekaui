<script setup>
/**
 * Sidebar navigation link. Items without `href` render as a placeholder until the page exists.
 * The link to the page that is already open renders as plain text, so it cannot be clicked again.
 * In the collapsed desktop sidebar only the icon shows, so the label appears in a tooltip on hover or focus.
 * Usage: <NavItem label="Dashboard" :href="route('dashboard')" icon="layout-grid" :active="true" :count="2" />
 */
import { Link } from '@inertiajs/vue3';
import { computed, inject } from 'vue';
import Count from '@/components/ui/Count.vue';
import Icon from '@/components/ui/Icon.vue';
import Tooltip from '@/components/ui/Tooltip.vue';
import { useCurrentPage } from '@/composables/useCurrentPage';
import { mobileSidebarKey, useSidebar } from '@/composables/useSidebar';

const props = defineProps({
    label: { type: String, required: true },
    href: { type: String, default: null },
    icon: { type: String, default: null },
    active: { type: Boolean, default: false },
    count: { type: Number, default: null },
});

const { isCurrentPage } = useCurrentPage();
const { isCollapsed } = useSidebar();
const isMobileSidebar = inject(mobileSidebarKey, false);

const showsTooltip = computed(() => isCollapsed.value && !isMobileSidebar);
const isOnThisPage = computed(() => isCurrentPage(props.href));

/**
 * The layout persists across visits, so arriving on or leaving this page swaps the tooltip trigger between
 * `<span>` and `<a>`. reka-ui keeps anchoring to the removed element (measured at 0,0), which puts the tooltip
 * in the top-left corner instead of beside the icon. Changing the key remounts the tooltip on the new element.
 */
const tooltipKey = computed(() => (isOnThisPage.value ? 'current-page' : 'link'));

/** Plain text on its own page, a placeholder `<a>` without `href`, otherwise an Inertia link. */
const element = computed(() => {
    if (isOnThisPage.value) {
        return 'span';
    }

    return props.href ? Link : 'a';
});
</script>

<template>
    <Tooltip :key="tooltipKey" :content="label" side="right" :delay-duration="100" :disabled="!showsTooltip">
        <component
            :is="element"
            :href="isOnThisPage ? undefined : (href ?? '#')"
            :aria-label="label"
            :aria-current="active ? 'page' : undefined"
            :class="[
                'flex h-11 items-center overflow-hidden rounded-xl pl-3.5 text-[0.875rem] transition-colors',
                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                active ? 'bg-brand-soft font-medium text-brand-ink' : 'text-ink/80 hover:bg-ink/5 hover:text-ink',
                { 'cursor-default': isOnThisPage },
            ]"
            @click="href ? undefined : $event.preventDefault()"
        >
            <Icon v-if="icon" :name="icon" :class="['size-5', active ? 'text-brand' : 'text-muted']" />

            <span
                :class="[
                    'sidebar-label flex min-w-0 flex-1 items-center gap-2 pr-3 lg:sidebar-collapsed:w-[178px] lg:sidebar-collapsed:flex-none',
                    { 'ml-3': icon },
                ]"
            >
                <span class="min-w-0 flex-1 truncate">{{ label }}</span>
                <Count v-if="count !== null" variant="dark" class="shrink-0">{{ count }}</Count>
            </span>
        </component>
    </Tooltip>
</template>
