import { ref } from 'vue';

/**
 * Shared dark-mode state. The initial value is applied by the inline script in
 * `resources/views/app.blade.php` before the first paint.
 */
const isDark = ref(typeof document !== 'undefined' && document.documentElement.classList.contains('dark'));

export function useAppearance() {
    function setDark(value) {
        isDark.value = value;
        document.documentElement.classList.toggle('dark', value);

        try {
            localStorage.setItem('theme', value ? 'dark' : 'light');
        } catch {
            // Storage can be unavailable (private mode, blocked site data).
        }
    }

    return { isDark, setDark };
}
