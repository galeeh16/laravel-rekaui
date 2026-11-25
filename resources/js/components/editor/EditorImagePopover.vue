<script setup>
/**
 * Toolbar button + popover to insert an image from a URL or from a file on the user's computer.
 * Uploaded files are embedded in the document as data URLs.
 *
 * Usage: <EditorImagePopover :editor="editor" />
 */
import { ref, useTemplateRef, watch } from 'vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Popover from '@/components/ui/Popover.vue';
import EditorToolbarButton from './EditorToolbarButton.vue';

const MAX_FILE_SIZE_MB = 5;

const props = defineProps({
    editor: { type: Object, required: true },
});

const open = ref(false);
const url = ref('');
const error = ref(null);
const fileInput = useTemplateRef('fileInput');

watch(open, (isOpen) => {
    if (isOpen) {
        url.value = '';
        error.value = null;
    }
});

function insertImage(src, alt = '') {
    props.editor.chain().focus().setImage({ src, alt }).run();
    open.value = false;
}

function insertFromUrl() {
    const src = url.value.trim();

    if (src === '') {
        error.value = 'Isi alamat gambar terlebih dahulu.';

        return;
    }

    insertImage(src);
}

function insertFromFile(event) {
    const [file] = event.target.files;
    event.target.value = '';

    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        error.value = 'File yang dipilih bukan gambar.';

        return;
    }

    if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
        error.value = `Ukuran gambar maksimal ${MAX_FILE_SIZE_MB} MB.`;

        return;
    }

    const reader = new FileReader();
    reader.onload = () => insertImage(reader.result, file.name);
    reader.readAsDataURL(file);
}
</script>

<template>
    <Popover v-model:open="open" align="start" width="w-80">
        <template #trigger>
            <EditorToolbarButton icon="image" label="Sisipkan gambar" />
        </template>

        <div class="flex flex-col gap-3">
            <form class="flex flex-col gap-3" @submit.prevent="insertFromUrl">
                <Input v-model="url" label="Alamat gambar" placeholder="https://contoh.com/gambar.png" :error="error" />
                <Button type="submit" size="sm" class="self-end">Sisipkan</Button>
            </form>

            <div class="flex items-center gap-3 text-xs text-muted">
                <span class="h-px flex-1 bg-line" />
                atau
                <span class="h-px flex-1 bg-line" />
            </div>

            <Button variant="secondary" size="sm" icon="upload" @click="fileInput?.click()">Unggah dari komputer</Button>
            <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="insertFromFile" />
        </div>
    </Popover>
</template>
