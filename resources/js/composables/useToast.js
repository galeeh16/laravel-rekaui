import { createToastManager } from 'reka-ui';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * App-wide toast notifications on top of reka-ui's toast manager; `components/ui/Toaster.vue` renders them.
 *
 * Usage:
 * const { toast } = useToast();
 * toast({ title: 'Surat terkirim', description: 'Nomor 045/UM/XI/2026.', variant: 'success' });
 * toast({ title: 'Surat diarsipkan', action: { label: 'Batalkan', onClick: restoreLetter } });
 *
 * Variants: success, danger, info (default), warning.
 * Outside components: import { toast } from '@/composables/useToast';
 */
export const toastManager = createToastManager();

/**
 * Show a toast and return its id.
 *
 * @param {{ title: string, description?: string, variant?: 'success'|'danger'|'info'|'warning', duration?: number, id?: string, action?: { label: string, onClick: (event: Event) => void, altText?: string } }} options
 * @returns {string}
 */
export function toast({ title, description, variant = 'info', duration, id, action }) {
    return toastManager.add({
        id,
        title,
        description,
        variant,
        duration,
        // Errors and warnings interrupt screen readers; the rest is announced politely.
        type: ['danger', 'warning'].includes(variant) ? 'foreground' : 'background',
        actionProps: action
            ? { label: action.label, altText: action.altText ?? action.label, onClick: action.onClick }
            : undefined,
    });
}

/**
 * Close one toast, or every toast when no id is given.
 *
 * @param {string} [id]
 */
export function dismiss(id) {
    toastManager.close(id);
}

/**
 * Loading toast that turns into a success or error toast when the promise settles.
 * Each state is a string (the description) or `{ title, description }`; success/error may be a function of the result.
 * Returns the original promise, so it still rejects when the promise rejects.
 *
 * @param {Promise} promise
 * @param {{ loading: string|object, success: string|object|Function, error: string|object|Function }} states
 * @returns {Promise}
 */
function promiseToast(promise, { loading, success, error }) {
    return toastManager.promise(promise, { loading, success, error });
}

/**
 * Update the title, description, variant… of a toast that is still open.
 *
 * @param {string} id
 * @param {object} updates
 */
function updateToast(id, updates) {
    toastManager.update(id, updates);
}

export function useToast() {
    return { toast, promise: promiseToast, update: updateToast, dismiss };
}

/** Mounted <Toaster /> instances in mount order; only the first renders, so a page-level one doesn't duplicate a global one. */
const mountedToasters = ref([]);
let toasterCount = 0;

/**
 * Used by Toaster.vue: registers the instance and tells whether it is the one that renders toasts.
 *
 * @returns {{ isActive: import('vue').ComputedRef<boolean> }}
 */
export function useToasterRegistration() {
    const id = ++toasterCount;

    onMounted(() => {
        mountedToasters.value = [...mountedToasters.value, id];
    });

    onBeforeUnmount(() => {
        mountedToasters.value = mountedToasters.value.filter((other) => other !== id);
    });

    return { isActive: computed(() => mountedToasters.value[0] === id) };
}
