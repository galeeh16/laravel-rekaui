<script setup>
/**
 * Sidebar item with nested links (multi-level navigation).
 * Expanded sidebar: a reka-ui Collapsible that opens automatically when a child is active.
 * Collapsed desktop sidebar: the icon opens the children as a flyout menu, and shows the label in a tooltip.
 *
 * Usage: <NavCollapsible label="Components" icon="component" :children="[{ label, href, active }]" />
 */
import { Link } from '@inertiajs/vue3';
import { CollapsibleContent, CollapsibleRoot, CollapsibleTrigger } from 'reka-ui';
import { computed, ref, watch } from 'vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/DropdownMenuLabel.vue';
import Icon from '@/components/ui/Icon.vue';
import Tooltip from '@/components/ui/Tooltip.vue';
import { useCurrentPage } from '@/composables/useCurrentPage';
import { useSidebar } from '@/composables/useSidebar';

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

/**
 * The tooltip is attached to the wrapper around the flyout menu, because its trigger cannot also be the
 * menu's trigger button. Hover works through the wrapper; keyboard focus does not bubble as `focus`,
 * so the tooltip is opened from `focusin` here (keyboard focus only, not a mouse click).
 */
const isTooltipOpen = ref(false);

function showTooltipOnKeyboardFocus(event) {
    if (isCollapsed.value && event.target.matches(':focus-visible')) {
        isTooltipOpen.value = true;
    }
}
</script>

<template>
    <!-- Expanded sidebar -->
    <CollapsibleRoot v-model:open="isOpen" class="lg:sidebar-collapsed:hidden">
        <CollapsibleTrigger
            :class="[
                'flex h-11 w-full cursor-pointer items-center gap-3 rounded-xl px-3 text-left text-[0.875rem] transition-colors',
                'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                hasActiveChild ? 'font-medium text-ink' : 'text-ink/80 hover:bg-ink/5 hover:text-ink',
            ]"
        >
            <Icon v-if="icon" :name="icon" :class="['size-5', hasActiveChild ? 'text-brand' : 'text-muted']" />
            <span class="flex-1 truncate">{{ label }}</span>
            <Icon name="chevron-down" :class="['size-4 text-muted transition-transform duration-200', { 'rotate-180': isOpen }]" />
        </CollapsibleTrigger>

        <CollapsibleContent class="overflow-hidden data-[state=closed]:animate-collapsible-up data-[state=open]:animate-collapsible-down">
            <ul class="mt-0.5 ml-5 flex flex-col gap-0.5 border-l border-line py-1 pl-3">
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

    <!-- Collapsed desktop sidebar -->
    <Tooltip v-model:open="isTooltipOpen" :content="label" side="right" :delay-duration="100" :disabled="!isCollapsed">
        <div class="hidden lg:sidebar-collapsed:block" @focusin="showTooltipOnKeyboardFocus" @focusout="isTooltipOpen = false">
            <DropdownMenu side="right" align="start" width="w-60">
                <template #trigger>
                    <button
                        type="button"
                        :aria-label="label"
                        :class="[
                            'flex h-11 w-full cursor-pointer items-center justify-center rounded-xl transition-colors',
                            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                            hasActiveChild ? 'bg-brand-soft text-brand' : 'text-muted hover:bg-ink/5 hover:text-ink',
                        ]"
                    >
                        <Icon v-if="icon" :name="icon" class="size-5" />
                    </button>
                </template>

                <DropdownMenuLabel>{{ label }}</DropdownMenuLabel>

                <div class="max-h-[60vh] overflow-y-auto">
                    <!-- Without `href` the item for the open page only closes the menu instead of reloading it. -->
                    <DropdownMenuItem v-for="child in children" :key="child.href" :href="isCurrentPage(child.href) ? null : child.href">
                        <span :class="{ 'font-medium text-brand-ink': child.active }">{{ child.label }}</span>
                    </DropdownMenuItem>
                </div>
            </DropdownMenu>
        </div>
    </Tooltip>
</template>
