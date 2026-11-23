<script setup>
/**
 * Sidebar navigation link. Items without `href` render as a placeholder until the page exists.
 * Usage: <NavItem label="Dashboard" :href="route('dashboard')" icon="layout-grid" :active="true" :count="2" />
 */
import { Link } from '@inertiajs/vue3';
import Count from '@/components/ui/Count.vue';
import Icon from '@/components/ui/Icon.vue';

defineProps({
    label: { type: String, required: true },
    href: { type: String, default: null },
    icon: { type: String, default: null },
    active: { type: Boolean, default: false },
    count: { type: Number, default: null },
});
</script>

<template>
    <component
        :is="href ? Link : 'a'"
        :href="href ?? '#'"
        :title="label"
        :aria-current="active ? 'page' : undefined"
        :class="[
            'flex h-11 items-center gap-3 rounded-xl px-3 text-[0.875rem] transition-colors lg:sidebar-collapsed:justify-center lg:sidebar-collapsed:px-0',
            'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
            active ? 'bg-brand-soft font-medium text-brand-ink' : 'text-ink/80 hover:bg-ink/5 hover:text-ink',
        ]"
        @click="href ? undefined : $event.preventDefault()"
    >
        <Icon v-if="icon" :name="icon" :class="['size-5', active ? 'text-brand' : 'text-muted']" />
        <span class="flex-1 truncate lg:sidebar-collapsed:hidden">{{ label }}</span>
        <Count v-if="count !== null" variant="dark" class="lg:sidebar-collapsed:hidden">{{ count }}</Count>
    </component>
</template>
