/**
 * Tiptap extensions for the document editor (`pages/Editor.vue`). The body, header and footer are separate
 * editors, and each needs its own extension instances, so call this once per editor.
 *
 * Usage: useEditor({ extensions: createEditorExtensions({ placeholder: 'Mulai menulis…', characterCount: true }) })
 */
import { Highlight } from '@tiptap/extension-highlight';
import { Image } from '@tiptap/extension-image';
import { TaskItem, TaskList } from '@tiptap/extension-list';
import { Subscript } from '@tiptap/extension-subscript';
import { Superscript } from '@tiptap/extension-superscript';
import { TableKit } from '@tiptap/extension-table';
import { TextAlign } from '@tiptap/extension-text-align';
import { TextStyleKit } from '@tiptap/extension-text-style';
import { CharacterCount, Placeholder } from '@tiptap/extensions';
import { StarterKit } from '@tiptap/starter-kit';

/**
 * @param {{ placeholder: string, characterCount?: boolean, showPlaceholderWhenReadOnly?: boolean }} options
 */
export function createEditorExtensions({ placeholder, characterCount = false, showPlaceholderWhenReadOnly = false }) {
    return [
        StarterKit.configure({
            link: { openOnClick: false, autolink: true, defaultProtocol: 'https' },
        }),
        TextStyleKit,
        Highlight.configure({ multicolor: true }),
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Subscript,
        Superscript,
        TaskList,
        TaskItem.configure({ nested: true }),
        TableKit.configure({ table: { resizable: true } }),
        Image.configure({ allowBase64: true }),
        Placeholder.configure({ placeholder, showOnlyWhenEditable: !showPlaceholderWhenReadOnly }),
        ...(characterCount ? [CharacterCount] : []),
    ];
}
