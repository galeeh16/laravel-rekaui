<script setup>
/**
 * Renders an Inertia <Link> when `href` is given (use `method="post"` for actions such as logout),
 * otherwise a plain menu item — listen to `@select` for the click.
 * Variants: default, danger
 *
 * Usage: <DropdownMenuItem icon="copy" shortcut="⌘D" @select="duplicate">Duplikat</DropdownMenuItem>
 *        <DropdownMenuItem icon="lock" disabled>Kunci surat</DropdownMenuItem>
 */
import { Link } from '@inertiajs/vue3';
import { DropdownMenuItem } from 'reka-ui';
import { computed } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    href: { type: String, default: null },
    method: { type: String, default: 'get' },
    icon: { type: String, default: null },
    variant: { type: String, default: 'default' },
    /** Keyboard hint shown on the right, e.g. "⌘D". Display only. */
    shortcut: { type: String, default: null },
});

const isDanger = computed(() => props.variant === 'danger');

const classes = computed(() => [
    'flex w-full cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-sm outline-none select-none transition-colors',
    'data-disabled:pointer-events-none data-disabled:opacity-50',
    isDanger.value ? 'text-danger data-highlighted:bg-danger-soft' : 'text-ink data-highlighted:bg-ink/5',
]);

const iconClass = computed(() => ['size-4', isDanger.value ? '' : 'text-muted']);
</script>

<template>
    <DropdownMenuItem v-if="href" as-child>
        <Link :href="href" :method="method" :as="method === 'get' ? 'a' : 'button'" :class="classes">
            <Icon v-if="icon" :name="icon" :class="iconClass" />
            <slot />
            <span v-if="shortcut" class="ml-auto pl-4 text-xs tracking-wide text-muted">{{ shortcut }}</span>
        </Link>
    </DropdownMenuItem>

    <DropdownMenuItem v-else :class="classes">
        <Icon v-if="icon" :name="icon" :class="iconClass" />
        <slot />
        <span v-if="shortcut" class="ml-auto pl-4 text-xs tracking-wide text-muted">{{ shortcut }}</span>
    </DropdownMenuItem>
</template>
