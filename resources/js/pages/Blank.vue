<script setup>
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ label: 'Dashboard', href: route('dashboard') }, { label: 'Blank' }],
    },
});
</script>

<template>
    <Head title="Blank" />

    <div class="pt-6 lg:pt-10">
        <h1 class="text-4xl font-medium tracking-tight sm:text-5xl">Blank</h1>
    </div>
</template>
