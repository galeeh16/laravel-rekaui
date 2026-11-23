<script setup>
/**
 * Sidebar item with nested links (multi-level navigation).
 * Expanded sidebar: a reka-ui Collapsible that opens automatically when a child is active.
 * Collapsed desktop sidebar: the icon opens the children as a flyout menu.
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

const props = defineProps({
    label: { type: String, required: true },
    icon: { type: String, default: null },
    /** @type {Array<{ label: string, href: string, active?: boolean }>} */
    children: { type: Array, required: true },
});

const hasActiveChild = computed(() => props.children.some((child) => child.active));
const isOpen = ref(hasActiveChild.value);

watch(hasActiveChild, (active) => {
    if (active) {
        isOpen.value = true;
    }
});
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
                <li v-for="child in children" :key="child.href">
                    <Link
                        :href="child.href"
                        :aria-current="child.active ? 'page' : undefined"
                        :class="[
                            'flex h-9 items-center truncate rounded-lg px-2.5 text-[0.875rem] transition-colors',
                            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                            child.active ? 'bg-brand-soft font-medium text-brand-ink' : 'text-ink/70 hover:bg-ink/5 hover:text-ink',
                        ]"
                    >
                        {{ child.label }}
                    </Link>
                </li>
            </ul>
        </CollapsibleContent>
    </CollapsibleRoot>

    <!-- Collapsed desktop sidebar -->
    <div class="hidden lg:sidebar-collapsed:block">
        <DropdownMenu side="right" align="start" width="w-60">
            <template #trigger>
                <button
                    type="button"
                    :title="label"
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
                <DropdownMenuItem v-for="child in children" :key="child.href" :href="child.href">
                    <span :class="{ 'font-medium text-brand-ink': child.active }">{{ child.label }}</span>
                </DropdownMenuItem>
            </div>
        </DropdownMenu>
    </div>
</template>
