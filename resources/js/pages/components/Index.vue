<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import Icon from '@/components/ui/Icon.vue';
import Input from '@/components/ui/Input.vue';

defineOptions({
    layout: { breadcrumbs: [{ label: 'Components' }] },
});

const props = defineProps({
    /** @type {Array<{ slug: string, label: string, description: string, route: string }>} */
    components: { type: Array, required: true },
});

const search = ref('');

const filteredComponents = computed(() => {
    const term = search.value.trim().toLowerCase();

    return term === ''
        ? props.components
        : props.components.filter((component) => `${component.label} ${component.description}`.toLowerCase().includes(term));
});
</script>

<template>
    <Head title="Components" />

    <div class="flex flex-col gap-8 pt-6 lg:pt-10">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="flex items-center gap-1.5 text-sm font-medium text-brand">
                    <Icon name="component" class="size-4" />
                    reka-ui + Inertia + Vue 3
                </p>
                <h1 class="mt-2 text-4xl font-medium tracking-tight sm:text-5xl">Components</h1>
                <p class="mt-3 max-w-2xl text-muted">
                    {{ components.length }} komponen reka-ui yang sudah dibungkus dengan gaya aplikasi ini. Setiap halaman berisi contoh
                    langsung, kode yang bisa disalin, dan daftar props.
                </p>
            </div>

            <Input v-model="search" type="search" icon="search" placeholder="Cari komponen…" aria-label="Cari komponen" class="w-full sm:max-w-xs" />
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <Link
                v-for="component in filteredComponents"
                :key="component.slug"
                :href="route(component.route)"
                class="group flex flex-col gap-1.5 rounded-2xl border border-line bg-surface p-5 shadow-xs transition-colors hover:border-brand/40 hover:bg-brand-soft/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
            >
                <span class="flex items-center justify-between gap-2 font-medium">
                    {{ component.label }}
                    <Icon name="arrow-right" class="size-4 text-muted transition-transform group-hover:translate-x-0.5 group-hover:text-brand" />
                </span>
                <span class="text-sm text-muted">{{ component.description }}</span>
            </Link>
        </div>

        <p v-if="filteredComponents.length === 0" class="py-12 text-center text-muted">Tidak ada komponen yang cocok dengan "{{ search }}".</p>
    </div>
</template>
