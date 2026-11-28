<script setup>
defineProps({
    heading: { type: String, default: null },
});
</script>

<template>
    <div class="flex flex-col">
        <div
            v-if="heading"
            class="h-6 overflow-hidden px-3.5 transition-[height,opacity] duration-240 ease-sidebar motion-reduce:transition-none lg:sidebar-collapsed:h-0 lg:sidebar-collapsed:opacity-0"
        >
            <span class="block pb-2 text-xs font-medium tracking-[0.12em] whitespace-nowrap text-muted uppercase">
                {{ heading }}
            </span>
        </div>

        <div class="flex flex-col gap-0.5">
            <slot />
        </div>
    </div>
</template>
