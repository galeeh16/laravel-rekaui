<script setup>
/**
 * Built on reka-ui AspectRatio: keeps its content at a fixed width / height ratio at any width.
 * The `class` attribute goes to the outer element, so set the width there (inside a flex
 * container it has no width of its own). Content is clipped to rounded corners.
 *
 * Usage:
 * <AspectRatio :ratio="16 / 9" class="w-full max-w-md">
 *     <img src="/storage/banner.jpg" alt="Banner pengumuman" class="size-full object-cover" />
 * </AspectRatio>
 */
import { AspectRatio } from 'reka-ui';

defineProps({
    /** Width divided by height, e.g. 16 / 9, 1, 210 / 297 (A4 portrait). */
    ratio: { type: Number, default: 16 / 9 },
});
</script>

<template>
    <div>
        <AspectRatio :ratio="ratio" class="overflow-hidden rounded-2xl bg-ink/5">
            <slot />
        </AspectRatio>
    </div>
</template>
