<script setup>
/**
 * Built on reka-ui Avatar: shows the `src` image once it has loaded, otherwise the initials
 * on a colour picked consistently from the name (also while loading and when the image fails).
 * Sizes: xs, sm, md, lg, xl
 *
 * Usage: <Avatar name="Rina Kartika" />
 *        <Avatar name="Rina Kartika" src="/storage/avatars/rina.jpg" size="lg" />
 *
 * Decorative by default (the name is usually shown next to it); pass `alt` to expose it to screen readers.
 */
import { AvatarFallback, AvatarImage, AvatarRoot } from 'reka-ui';
import { computed } from 'vue';

const props = defineProps({
    name: { type: String, required: true },
    size: { type: String, default: 'md' },
    src: { type: String, default: null },
    alt: { type: String, default: null },
});

const tones = [
    'bg-tone-sky text-info',
    'bg-tone-cream text-warning',
    'bg-tone-peach text-danger',
    'bg-tone-mint text-brand-ink',
];

const sizeClasses = {
    xs: 'size-6 text-[0.625rem]',
    sm: 'size-8 text-xs',
    md: 'size-9 text-xs',
    lg: 'size-11 text-sm',
    xl: 'size-14 text-base',
};

const initials = computed(() =>
    props.name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0])
        .join(''),
);

const tone = computed(() => {
    const hash = [...props.name].reduce((total, character) => total + character.charCodeAt(0), 0);

    return tones[hash % tones.length];
});
</script>

<template>
    <AvatarRoot
        :class="['inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-ink/6 select-none', sizeClasses[size]]"
        :aria-hidden="alt ? undefined : 'true'"
    >
        <AvatarImage v-if="src" :src="src" :alt="alt ?? ''" class="size-full object-cover" />

        <!-- With an image, wait briefly so a fast load doesn't flash the initials first. -->
        <AvatarFallback
            :delay-ms="src ? 300 : undefined"
            :role="alt ? 'img' : undefined"
            :aria-label="alt ?? undefined"
            :class="['flex size-full items-center justify-center font-semibold uppercase', tone]"
        >
            {{ initials }}
        </AvatarFallback>
    </AvatarRoot>
</template>
