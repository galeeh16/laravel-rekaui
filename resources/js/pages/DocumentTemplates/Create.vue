<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';
import { route } from 'ziggy-js';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Icon from '@/components/ui/Icon.vue';
import Input from '@/components/ui/Input.vue';
import { formatFileSize, placeholderFor } from '@/lib/format';

defineProps({
    maxFileSizeMb: { type: Number, required: true },
});

const exampleVariables = ['nama', 'nik', 'alamat_lengkap'];

const form = useForm({
    name: '',
    file: null,
});

const fileInput = useTemplateRef('fileInput');
const isDragging = ref(false);

function selectFile(file) {
    form.file = file ?? null;
    form.clearErrors('file');
}

function dropFile(event) {
    isDragging.value = false;
    selectFile(event.dataTransfer.files[0]);
}

function removeFile() {
    form.file = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

/** On success the server stores the file and redirects to the list with a success toast. */
function upload() {
    form.post(route('document-templates.store'));
}

/** File responses can't be Inertia visits, so the sample is downloaded with a normal browser navigation. */
function downloadSample() {
    window.location.href = route('document-templates.sample');
}
</script>

<template>
    <Head title="Create Dokumen" />

    <div class="pt-6 lg:pt-10">
        <Link
            :href="route('document-templates.index')"
            class="inline-flex items-center gap-1 text-sm text-muted transition-colors hover:text-ink"
        >
            <Icon name="arrow-left" class="size-4" />
            List Dokumen
        </Link>

        <h1 class="mt-3 text-4xl font-medium tracking-tight sm:text-5xl">Create Dokumen</h1>
        <p class="mt-3 text-muted">Unggah template Word berisi variabel. Template yang diunggah akan tersimpan di List Dokumen.</p>
    </div>

    <div class="mt-8 grid gap-4 lg:grid-cols-5">
        <!-- Upload -->
        <Card class="p-5 sm:p-6 lg:col-span-3">
            <h2 class="text-lg font-medium">Unggah template</h2>
            <p class="mt-1 text-sm text-muted">Format .docx, maksimal {{ maxFileSizeMb }} MB.</p>

            <form class="mt-5 flex flex-col gap-4" @submit.prevent="upload">
                <div>
                    <label
                        v-if="!form.file"
                        :class="[
                            'flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed px-6 py-10 text-center transition-colors',
                            isDragging ? 'border-brand bg-brand-soft/60' : 'border-line hover:border-brand/50 hover:bg-ink/2',
                            { 'border-danger': form.errors.file },
                        ]"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="dropFile"
                    >
                        <span class="flex size-11 items-center justify-center rounded-full bg-brand-soft text-brand-ink">
                            <Icon name="upload" class="size-5" />
                        </span>
                        <span class="text-sm font-medium">Tarik file ke sini atau <span class="text-brand underline underline-offset-2">pilih file</span></span>
                        <span class="text-xs text-muted">Dokumen Word (.docx)</span>
                        <input
                            ref="fileInput"
                            type="file"
                            accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            class="sr-only"
                            @change="selectFile($event.target.files[0])"
                        />
                    </label>

                    <div v-else class="flex items-center gap-3 rounded-xl border border-line bg-canvas/60 p-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-info-soft text-info">
                            <Icon name="file-text" class="size-5" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ form.file.name }}</p>
                            <p class="text-xs text-muted">{{ formatFileSize(form.file.size) }}</p>
                        </div>

                        <Button variant="ghost" size="icon-sm" icon="x" aria-label="Hapus file yang dipilih" :disabled="form.processing" @click="removeFile" />
                    </div>

                    <p v-if="form.errors.file" class="mt-1.5 text-sm text-danger">{{ form.errors.file }}</p>

                    <div v-if="form.progress" class="mt-3 h-1.5 overflow-hidden rounded-full bg-ink/8">
                        <div class="h-full rounded-full bg-brand transition-[width]" :style="{ width: `${form.progress.percentage}%` }" />
                    </div>
                </div>

                <Input
                    v-model="form.name"
                    label="Nama template"
                    placeholder="Kosongkan untuk memakai nama file"
                    :error="form.errors.name"
                />

                <div class="flex flex-wrap gap-2">
                    <Button type="submit" icon="upload" :disabled="!form.file || form.processing">
                        {{ form.processing ? 'Mengunggah…' : 'Unggah template' }}
                    </Button>
                    <Button :href="route('document-templates.index')" variant="secondary">Batal</Button>
                </div>
            </form>
        </Card>

        <!-- How to write a template -->
        <Card class="flex flex-col p-5 sm:p-6 lg:col-span-2">
            <h2 class="text-lg font-medium">Cara membuat template</h2>

            <ol class="mt-4 flex list-decimal flex-col gap-3 pl-5 text-sm text-ink/85 marker:text-muted">
                <li>Buat dokumen di Microsoft Word, Google Docs, atau LibreOffice.</li>
                <li>
                    Tulis variabel di tempat yang akan diisi, misalnya
                    <span class="inline-flex flex-wrap gap-1 align-middle">
                        <code v-for="variable in exampleVariables" :key="variable" class="rounded bg-ink/6 px-1.5 py-0.5 font-mono text-xs">
                            {{ placeholderFor(variable) }}
                        </code>
                    </span>
                </li>
                <li>Nama variabel hanya boleh berisi huruf, angka, <code class="font-mono text-xs">_</code> atau <code class="font-mono text-xs">-</code>, tanpa spasi.</li>
                <li>Simpan sebagai .docx, lalu unggah di sini.</li>
            </ol>

            <div class="mt-5 rounded-xl bg-canvas/70 p-4 text-sm lg:mt-auto">
                <p class="font-medium">Belum punya template?</p>
                <p class="mt-1 text-muted">Unduh contoh surat keterangan yang sudah berisi variabel nama, NIK, alamat, dan lainnya.</p>
                <Button variant="secondary" size="sm" icon="download" class="mt-3" @click="downloadSample">Unduh contoh template</Button>
            </div>
        </Card>
    </div>
</template>
