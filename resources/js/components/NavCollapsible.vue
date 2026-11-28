<script setup>
/**
 * Sidebar item with nested links (multi-level navigation).
 * Expanded sidebar: a reka-ui Collapsible that opens automatically when a child is active.
 * Collapsed desktop sidebar: the same trigger opens the children as a flyout menu and shows the label in a tooltip.
 *
 * Usage: <NavCollapsible label="Components" icon="component" :children="[{ label, href, active }]" />
 */
import { Link } from '@inertiajs/vue3';
import { CollapsibleContent, CollapsibleRoot } from 'reka-ui';
import { computed, inject, ref, watch } from 'vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/DropdownMenuLabel.vue';
import Icon from '@/components/ui/Icon.vue';
import Tooltip from '@/components/ui/Tooltip.vue';
import { useCurrentPage } from '@/composables/useCurrentPage';
import { mobileSidebarKey, useSidebar } from '@/composables/useSidebar';

const props = defineProps({
    label: { type: String, required: true },
    icon: { type: String, default: null },
    /** @type {Array<{ label: string, href: string, active?: boolean }>} */
    children: { type: Array, required: true },
});

/** The child link to the page that is already open renders as plain text, so it cannot be clicked again. */
const { isCurrentPage } = useCurrentPage();

const hasActiveChild = computed(() => props.children.some((child) => child.active));
const isOpen = ref(hasActiveChild.value);

watch(hasActiveChild, (active) => {
    if (active) {
        isOpen.value = true;
    }
});

const { isCollapsed } = useSidebar();
const isMobileSidebar = inject(mobileSidebarKey, false);

const showsFlyout = computed(() => isCollapsed.value && !isMobileSidebar);

/**
 * Remember the user's open preference while smoothly closing inline children when the desktop
 * sidebar collapses (and re-opening them when it expands again).
 */
const isExpandedOpen = computed({
    get: () => isOpen.value && !showsFlyout.value,
    set: (value) => {
        isOpen.value = value;
    },
});

const isFlyoutOpen = ref(false);
const isTooltipOpen = ref(false);

watch(showsFlyout, (collapsed) => {
    if (!collapsed) {
        isFlyoutOpen.value = false;
        isTooltipOpen.value = false;
    }
});

function handleFlyoutOpenChange(nextOpen) {
    if (!showsFlyout.value) {
        isFlyoutOpen.value = false;

        return;
    }

    isFlyoutOpen.value = nextOpen;

    if (nextOpen) {
        isTooltipOpen.value = false;
    }
}

function handleTriggerClick() {
    if (!showsFlyout.value) {
        isOpen.value = !isOpen.value;
    }
}

function handleTriggerKeydown(event) {
    if (!showsFlyout.value && !event.repeat && (event.key === 'Enter' || event.key === ' ')) {
        isOpen.value = !isOpen.value;
    }
}

/**
 * The tooltip is attached to the wrapper around the flyout menu, because its trigger cannot also be the
 * menu's trigger button. Hover works through the wrapper; keyboard focus does not bubble as `focus`,
 * so the tooltip is opened from `focusin` here (keyboard focus only, not a mouse click).
 */
function showTooltipOnKeyboardFocus(event) {
    if (showsFlyout.value && event.target.matches(':focus-visible')) {
        isTooltipOpen.value = true;
    }
}
</script>

<template>
    <CollapsibleRoot v-model:open="isExpandedOpen">
        <Tooltip v-model:open="isTooltipOpen" :content="label" side="right" :delay-duration="100" :disabled="!showsFlyout">
            <div @focusin="showTooltipOnKeyboardFocus" @focusout="isTooltipOpen = false">
                <DropdownMenu :open="isFlyoutOpen" side="right" align="start" width="w-60" @update:open="handleFlyoutOpenChange">
                    <template #trigger>
                        <button
                            type="button"
                            :aria-label="label"
                            :aria-haspopup="showsFlyout ? 'menu' : undefined"
                            :aria-expanded="showsFlyout ? isFlyoutOpen : isExpandedOpen"
                            :class="[
                                'group flex h-11 w-full cursor-pointer items-center overflow-hidden rounded-xl pl-3.5 text-left text-[0.875rem] transition-colors',
                                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                                hasActiveChild
                                    ? 'font-medium text-ink lg:sidebar-collapsed:bg-brand-soft'
                                    : 'text-ink/80 hover:bg-ink/5 hover:text-ink',
                            ]"
                            @click="handleTriggerClick"
                            @keydown="handleTriggerKeydown"
                        >
                            <Icon
                                v-if="icon"
                                :name="icon"
                                :class="[
                                    'size-5 transition-colors',
                                    hasActiveChild ? 'text-brand' : 'text-muted lg:sidebar-collapsed:group-hover:text-ink',
                                ]"
                            />

                            <span
                                :class="[
                                    'sidebar-label flex min-w-0 flex-1 items-center gap-2 pr-3 lg:sidebar-collapsed:w-[178px] lg:sidebar-collapsed:flex-none',
                                    { 'ml-3': icon },
                                ]"
                            >
                                <span class="min-w-0 flex-1 truncate">{{ label }}</span>
                                <Icon
                                    name="chevron-down"
                                    :class="['size-4 text-muted transition-transform duration-200', { 'rotate-180': isOpen }]"
                                />
                            </span>
                        </button>
                    </template>

                    <DropdownMenuLabel>{{ label }}</DropdownMenuLabel>

                    <div class="max-h-[60vh] overflow-y-auto">
                        <!-- Without `href` the item for the open page only closes the menu instead of reloading it. -->
                        <DropdownMenuItem
                            v-for="child in children"
                            :key="child.href"
                            :href="isCurrentPage(child.href) ? null : child.href"
                        >
                            <span :class="{ 'font-medium text-brand-ink': child.active }">{{ child.label }}</span>
                        </DropdownMenuItem>
                    </div>
                </DropdownMenu>
            </div>
        </Tooltip>

        <CollapsibleContent
            class="overflow-hidden data-[state=closed]:animate-collapsible-up data-[state=open]:animate-collapsible-down motion-reduce:animate-none"
        >
            <ul
                class="mt-0.5 ml-6 flex flex-col gap-0.5 border-l border-line py-1 pl-3 transition-opacity duration-100 motion-reduce:transition-none lg:sidebar-collapsed:w-48 lg:sidebar-collapsed:opacity-0"
            >
                <li v-for="child in children" :key="child.href" class="relative">
                    <!-- Brand dot with a soft halo on the vertical line next to the active link, centered on the 1px border. -->
                    <span
                        v-if="child.active"
                        class="absolute top-1/2 -left-[calc(1rem+0.5px)] size-2 -translate-y-1/2 rounded-full bg-brand ring-4 ring-brand-soft"
                        aria-hidden="true"
                    />

                    <component
                        :is="isCurrentPage(child.href) ? 'span' : Link"
                        :href="isCurrentPage(child.href) ? undefined : child.href"
                        :aria-current="child.active ? 'page' : undefined"
                        :class="[
                            'flex h-9 items-center truncate rounded-lg px-2.5 text-[0.875rem] transition-colors',
                            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                            child.active ? 'bg-brand-soft font-medium text-brand-ink' : 'text-ink/70 hover:bg-ink/5 hover:text-ink',
                            { 'cursor-default': isCurrentPage(child.href) },
                        ]"
                    >
                        {{ child.label }}
                    </component>
                </li>
            </ul>
        </CollapsibleContent>
    </CollapsibleRoot>
</template>
