import { ref } from 'vue';

/**
 * Shared open state for the "Surat baru" modal. The modal is rendered once in
 * `layouts/AppLayout.vue`; any button can open it through `openNewLetterModal()`.
 */
const isOpen = ref(false);

export function useNewLetterModal() {
    function openNewLetterModal() {
        isOpen.value = true;
    }

    return { isOpen, openNewLetterModal };
}
