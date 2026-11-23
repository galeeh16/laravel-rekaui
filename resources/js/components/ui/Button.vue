<script setup>
/**
 * Variants: primary, dark, secondary, ghost, soft, danger
 * Sizes: sm, md, lg, icon-sm (32px), icon-md (36px), icon (40px), icon-lg (44px)
 *
 * Usage: <Button variant="secondary" icon="send">Kirim</Button>
 *        <Button :href="route('dashboard')">Dashboard</Button>   (renders an Inertia <Link>)
 *        <Button href="https://reka-ui.com" target="_blank">Docs</Button>   (other origins render a plain <a>)
 */
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    icon: { type: String, default: null },
    iconTrailing: { type: String, default: null },
    href: { type: String, default: null },
    type: { type: String, default: 'button' },
});

const variantClasses = {
    primary: 'bg-brand text-white shadow-sm shadow-brand/25 hover:bg-brand-hover',
    dark: 'bg-ink text-canvas hover:bg-ink/85',
    secondary: 'border border-line bg-surface text-ink shadow-xs hover:bg-canvas',
    ghost: 'text-ink hover:bg-ink/5',
    soft: 'bg-brand-soft text-brand-ink hover:bg-brand-soft/70',
    danger: 'bg-red-600 text-white shadow-sm shadow-red-600/25 hover:bg-red-700',
};

const sizeClasses = {
    sm: 'h-8 gap-1.5 rounded-lg px-3 text-sm',
    md: 'h-10 gap-2 rounded-xl px-4 text-sm',
    lg: 'h-11 gap-2 rounded-xl px-5 text-base',
    'icon-sm': 'size-8 rounded-lg',
    'icon-md': 'size-9 rounded-lg',
    icon: 'size-10 rounded-xl',
    'icon-lg': 'size-11 rounded-xl',
};

const classes = computed(() => [
    'inline-flex shrink-0 cursor-pointer items-center justify-center font-medium whitespace-nowrap transition-colors',
    'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
    'disabled:pointer-events-none disabled:opacity-50',
    variantClasses[props.variant],
    sizeClasses[props.size],
]);

const iconClass = computed(() => (['sm', 'icon-sm', 'icon-md'].includes(props.size) ? 'size-4' : 'size-4.5'));

/** Links to another origin can't be Inertia visits, so they render as a normal anchor. */
const isExternal = computed(() => {
    if (!props.href || typeof window === 'undefined') {
        return false;
    }

    return new URL(props.href, window.location.href).origin !== window.location.origin;
});
</script>

<template>
    <a v-if="href && isExternal" :href="href" :class="classes">
        <Icon v-if="icon" :name="icon" :class="iconClass" />
        <slot />
        <Icon v-if="iconTrailing" :name="iconTrailing" :class="iconClass" />
    </a>

    <Link v-else-if="href" :href="href" :class="classes">
        <Icon v-if="icon" :name="icon" :class="iconClass" />
        <slot />
        <Icon v-if="iconTrailing" :name="iconTrailing" :class="iconClass" />
    </Link>

    <button v-else :type="type" :class="classes">
        <Icon v-if="icon" :name="icon" :class="iconClass" />
        <slot />
        <Icon v-if="iconTrailing" :name="iconTrailing" :class="iconClass" />
    </button>
</template>
