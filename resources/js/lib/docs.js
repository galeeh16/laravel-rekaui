import { route } from 'ziggy-js';

/**
 * Layout props for component documentation pages (breadcrumbs from the `component` page prop).
 *
 * Usage in a page: defineOptions({ layout: componentLayout });
 *
 * Must stay an arrow function: Inertia only treats a layout function as a props callback when it
 * has no prototype. A regular `function` is mistaken for a Vue component and renders "[object Object]".
 */
export const componentLayout = (props) => ({
    breadcrumbs: [{ label: 'Components', href: route('components.index') }, { label: props.component.label }],
});
