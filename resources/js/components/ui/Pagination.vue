<script setup>
/**
 * Built on reka-ui Pagination. Two modes:
 *
 * - Laravel (Inertia): pass a LengthAwarePaginator serialized to JSON as `paginator`. Changing page
 *   visits that page with Inertia, keeping the current query string (filters, per_page).
 * - Client-side: without `paginator`, pass `total` and `items-per-page` and bind `v-model:page`.
 *
 * Small screens show "current / last" between the arrows; larger screens show page numbers.
 * Nothing is rendered when everything fits on one page.
 *
 * Usage: <Pagination :paginator="letters" />
 *        <Pagination v-model:page="page" :total="letters.length" :items-per-page="10" />
 */
import { router } from '@inertiajs/vue3';
import {
    PaginationEllipsis,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
    PaginationRoot,
} from 'reka-ui';
import { computed } from 'vue';
import Button from './Button.vue';

/** Client-side mode only; ignored when `paginator` is given. */
const page = defineModel('page', { type: Number, default: 1 });

const props = defineProps({
    /** Laravel LengthAwarePaginator JSON (`->paginate()`); switches to Inertia navigation. */
    paginator: { type: Object, default: null },
    /** Client-side mode: total number of items. */
    total: { type: Number, default: 0 },
    /** Client-side mode: items shown per page. */
    itemsPerPage: { type: Number, default: 10 },
    /** Number of page buttons shown on each side of the current page. */
    siblingCount: { type: Number, default: 1 },
});

const currentPage = computed(() => (props.paginator ? props.paginator.current_page : page.value));
const totalItems = computed(() => (props.paginator ? props.paginator.total : props.total));
const perPage = computed(() => (props.paginator ? props.paginator.per_page : props.itemsPerPage));
const lastPage = computed(() =>
    props.paginator ? props.paginator.last_page : Math.max(1, Math.ceil(props.total / (props.itemsPerPage || 1))),
);

/** Laravel's `first_page_url` already carries the current query string; swap in the new page. */
function pageUrl(newPage) {
    const url = new URL(props.paginator.first_page_url, window.location.origin);
    url.searchParams.set('page', newPage);

    return url.pathname + url.search;
}

function changePage(newPage) {
    /* The active page is not a link: re-selecting it (click or keyboard) does nothing. */
    if (newPage === currentPage.value) {
        return;
    }

    if (props.paginator) {
        router.get(pageUrl(newPage), {}, { preserveScroll: true });

        return;
    }

    page.value = newPage;
}
</script>

<template>
    <PaginationRoot
        :page="currentPage"
        :total="totalItems"
        :items-per-page="perPage"
        :sibling-count="siblingCount"
        show-edges
        aria-label="Navigasi halaman"
        @update:page="changePage"
    >
        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
            <PaginationPrev as-child>
                <Button variant="secondary" size="icon-md" icon="chevron-left" aria-label="Halaman sebelumnya" />
            </PaginationPrev>

            <span class="px-2 text-sm text-muted tabular-nums sm:hidden">
                {{ currentPage }} / {{ lastPage }}
            </span>

            <template v-for="(item, index) in items" :key="item.type === 'page' ? `page-${item.value}` : `ellipsis-${index}`">
                <PaginationListItem
                    v-if="item.type === 'page'"
                    :value="item.value"
                    :aria-label="`Halaman ${item.value}`"
                    class="hidden size-9 cursor-pointer items-center justify-center rounded-lg text-sm font-medium text-ink tabular-nums transition-colors hover:bg-ink/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand data-selected:pointer-events-none data-selected:cursor-default data-selected:bg-brand data-selected:text-white data-selected:shadow-sm data-selected:shadow-brand/25 sm:inline-flex"
                >
                    {{ item.value }}
                </PaginationListItem>

                <PaginationEllipsis
                    v-else
                    :index="index"
                    class="hidden size-9 items-center justify-center text-sm text-muted sm:inline-flex"
                >
                    …
                </PaginationEllipsis>
            </template>

            <PaginationNext as-child>
                <Button variant="secondary" size="icon-md" icon="chevron-right" aria-label="Halaman berikutnya" />
            </PaginationNext>
        </PaginationList>
    </PaginationRoot>
</template>
