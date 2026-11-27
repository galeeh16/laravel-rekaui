import { ref } from 'vue';

/**
 * Desktop collapse state lives on <html data-sidebar-collapsed> so the
 * `sidebar-collapsed:` Tailwind variant works and the inline script in
 * `app.blade.php` can restore it before the first paint.
 */
const isCollapsed = ref(typeof document !== 'undefined' && document.documentElement.hasAttribute('data-sidebar-collapsed'));

/** Mobile drawer state. */
const isMobileOpen = ref(false);

/**
 * Provided by `AppSidebar`: whether this copy of the sidebar is the mobile drawer, where the
 * collapsed-sidebar tooltips must stay off (the drawer always shows the labels).
 */
export const mobileSidebarKey = Symbol('mobileSidebar');

export function useSidebar() {
    function toggleCollapsed() {
        isCollapsed.value = !isCollapsed.value;
        document.documentElement.toggleAttribute('data-sidebar-collapsed', isCollapsed.value);

        try {
            localStorage.setItem('sidebar', isCollapsed.value ? 'collapsed' : 'expanded');
        } catch {
            // Storage can be unavailable (private mode, blocked site data).
        }
    }

    return { isCollapsed, isMobileOpen, toggleCollapsed };
}
