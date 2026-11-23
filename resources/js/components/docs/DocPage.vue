<script setup>
/**
 * Shell of a component documentation page. Title, description and the reka-ui link
 * come from the `component` page prop sent by ComponentController@show.
 *
 * Usage (in resources/js/pages/components/Xxx.vue):
 * <DocPage>
 *     <DocExample title="Dasar" :code="basicCode">...preview...</DocExample>
 *     <DocProps title="Xxx" :rows="[...]" />
 * </DocPage>
 */
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/components/ui/Button.vue';
import Icon from '@/components/ui/Icon.vue';

const page = usePage();
const component = computed(() => page.props.component);
</script>

<template>
    <Head :title="component.label" />

    <div class="flex flex-col gap-10 pt-6 lg:pt-10">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="flex items-center gap-1.5 text-sm font-medium text-brand">
                    <Icon name="component" class="size-4" />
                    Components
                </p>
                <h1 class="mt-2 text-4xl font-medium tracking-tight">{{ component.label }}</h1>
                <p class="mt-3 max-w-2xl text-muted">{{ component.description }}</p>
            </div>

            <Button
                :href="component.docsUrl"
                variant="secondary"
                icon="book-open"
                icon-trailing="external-link"
                target="_blank"
                rel="noopener noreferrer"
                class="self-start sm:self-auto"
            >
                Dokumentasi reka-ui
            </Button>
        </header>

        <slot />
    </div>
</template>
