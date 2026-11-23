<script setup>
/**
 * Overlapping row of Avatars with a "+N" bubble for the people beyond `max`.
 * Sizes: xs, sm, md, lg, xl (same as Avatar)
 *
 * Usage: <AvatarGroup :people="[{ name: 'Rina Kartika', src: '/storage/avatars/rina.jpg' }, { name: 'Budi Santoso' }]" :max="4" />
 */
import { computed } from 'vue';
import Avatar from './Avatar.vue';

const props = defineProps({
    /** @type {Array<{ name: string, src?: string, id?: string|number }>} */
    people: { type: Array, required: true },
    max: { type: Number, default: 4 },
    size: { type: String, default: 'md' },
});

const sizeClasses = {
    xs: 'size-6 text-[0.625rem]',
    sm: 'size-8 text-xs',
    md: 'size-9 text-xs',
    lg: 'size-11 text-sm',
    xl: 'size-14 text-base',
};

const visiblePeople = computed(() => props.people.slice(0, props.max));
const hiddenCount = computed(() => Math.max(props.people.length - props.max, 0));

const accessibleLabel = computed(() => {
    const names = visiblePeople.value.map((person) => person.name).join(', ');

    return hiddenCount.value ? `${names}, dan ${hiddenCount.value} lainnya` : names;
});
</script>

<template>
    <div role="img" :aria-label="accessibleLabel" class="flex items-center -space-x-2">
        <Avatar
            v-for="person in visiblePeople"
            :key="person.id ?? person.name"
            :name="person.name"
            :src="person.src ?? null"
            :size="size"
            class="ring-2 ring-surface"
        />

        <span
            v-if="hiddenCount"
            :class="[
                'inline-flex shrink-0 items-center justify-center rounded-full bg-canvas font-semibold text-ink/75 ring-2 ring-surface',
                sizeClasses[size],
            ]"
            aria-hidden="true"
        >
            +{{ hiddenCount }}
        </span>
    </div>
</template>
