<script setup>
defineProps({
    heading: { type: String, default: null },
});
</script>

<template>
    <div class="flex flex-col gap-0.5">
        <div
            v-if="heading"
            class="px-3 pb-1.5 text-xs font-medium tracking-[0.12em] text-muted uppercase lg:sidebar-collapsed:hidden"
        >
            {{ heading }}
        </div>

        <slot />
    </div>
</template>
