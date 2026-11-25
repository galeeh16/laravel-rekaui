<script setup>
/**
 * Toolbar button + popover to add, edit or remove a link on the current selection.
 * With an empty selection, the URL itself is inserted as linked text.
 *
 * Usage: <EditorLinkPopover :editor="editor" />
 */
import { ref, watch } from 'vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Popover from '@/components/ui/Popover.vue';
import EditorToolbarButton from './EditorToolbarButton.vue';

const props = defineProps({
    editor: { type: Object, required: true },
});

const open = ref(false);
const url = ref('');

watch(open, (isOpen) => {
    if (isOpen) {
        url.value = props.editor.getAttributes('link').href ?? '';
    }
});

/** Add `https://` when the user types a bare domain such as `example.com`. */
function normalizeUrl(value) {
    return /^([a-z][a-z0-9+.-]*:|\/|#)/i.test(value) ? value : `https://${value}`;
}

function applyLink() {
    const href = url.value.trim();

    if (href === '') {
        removeLink();

        return;
    }

    const normalizedHref = normalizeUrl(href);

    if (props.editor.state.selection.empty && !props.editor.isActive('link')) {
        props.editor
            .chain()
            .focus()
            .insertContent({ type: 'text', text: href, marks: [{ type: 'link', attrs: { href: normalizedHref } }] })
            .run();
    } else {
        props.editor.chain().focus().extendMarkRange('link').setLink({ href: normalizedHref }).run();
    }

    open.value = false;
}

function removeLink() {
    props.editor.chain().focus().extendMarkRange('link').unsetLink().run();
    open.value = false;
}
</script>

<template>
    <Popover v-model:open="open" align="start" width="w-80">
        <template #trigger>
            <EditorToolbarButton icon="link" label="Tautan" :active="editor.isActive('link')" />
        </template>

        <form class="flex flex-col gap-3" @submit.prevent="applyLink">
            <Input v-model="url" label="Alamat tautan" placeholder="https://contoh.com" autofocus />

            <div class="flex justify-end gap-2">
                <Button v-if="editor.isActive('link')" variant="ghost" size="sm" icon="unlink" @click="removeLink">Hapus tautan</Button>
                <Button type="submit" size="sm">Terapkan</Button>
            </div>
        </form>
    </Popover>
</template>
