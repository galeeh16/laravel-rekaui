import { usePage } from '@inertiajs/vue3';

/** Path of a URL without a trailing slash, e.g. `https://app.test/penduduk/?q=a` → `/penduduk`. */
function pathOf(url) {
    return new URL(url, window.location.origin).pathname.replace(/\/+$/, '') || '/';
}

/**
 * Whether a link points at the page that is open now, so navigation can render it as plain text
 * instead of a link that reloads the same page. Compares paths only: a list with a search or on
 * another page number still counts as the same page.
 *
 * Usage: const { isCurrentPage } = useCurrentPage();  isCurrentPage(route('penduduk.index'))
 */
export function useCurrentPage() {
    const page = usePage();

    const isCurrentPage = (href) => Boolean(href) && pathOf(href) === pathOf(page.url);

    return { isCurrentPage };
}
