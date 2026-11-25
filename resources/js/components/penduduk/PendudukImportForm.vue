<script setup>
/**
 * Upload an Excel or CSV file to add many residents at once. The server checks every row; when one
 * is invalid nothing is saved and the errors are listed per row (`form.errors['baris.N']`).
 *
 * Usage: <PendudukImportForm :max-file-size-mb="5" :max-rows="1000" :columns="['NIK', 'Nama', ...]" />
 */
import { useForm } from '@inertiajs/vue3';
import { computed, ref, useTemplateRef } from 'vue';
import { route } from 'ziggy-js';
import Alert from '@/components/ui/Alert.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Icon from '@/components/ui/Icon.vue';
import { formatFileSize } from '@/lib/format';

defineProps({
    maxFileSizeMb: { type: Number, required: true },
    maxRows: { type: Number, required: true },
    /** Column names expected in the first row of the file. */
    columns: { type: Array, required: true },
});

/** Columns whose cells must be filled; the other columns must exist but their cells may be empty. */
const REQUIRED_VALUE_COLUMNS = ['NIK', 'Nama', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin'];

const form = useForm({
    file: null,
});

const fileInput = useTemplateRef('fileInput');
const isDragging = ref(false);

/** Per-row errors (`baris.12`, `baris.lainnya`) in row order. */
const rowErrors = computed(() =>
    Object.entries(form.errors)
        .filter(([key]) => key.startsWith('baris.'))
        .map(([, message]) => message),
);

function selectFile(file) {
    form.file = file ?? null;
    form.clearErrors();
}

function dropFile(event) {
    isDragging.value = false;
    selectFile(event.dataTransfer.files[0]);
}

function removeFile() {
    form.file = null;
    form.clearErrors();

    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

/** On success the server redirects to the list with a message. */
function upload() {
    form.post(route('penduduk.import'));
}

/** File responses can't be Inertia visits, so the template is downloaded with a normal browser navigation. */
function downloadTemplate() {
    window.location.href = route('penduduk.import-template');
}
</script>

<template>
    <div class="grid gap-4 lg:grid-cols-5">
        <Card class="p-5 sm:p-6 lg:col-span-3">
            <h2 class="text-lg font-medium">Upload file Excel</h2>
            <p class="mt-1 text-sm text-muted">
                Format .xlsx, .xls, atau .csv. Maksimal {{ maxFileSizeMb }} MB dan {{ maxRows.toLocaleString('id-ID') }} baris.
            </p>

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
                        <span class="text-xs text-muted">Excel (.xlsx, .xls) atau CSV</span>
                        <input
                            ref="fileInput"
                            type="file"
                            accept=".xlsx,.xls,.csv"
                            class="sr-only"
                            @change="selectFile($event.target.files[0])"
                        />
                    </label>

                    <div v-else class="flex items-center gap-3 rounded-xl border border-line bg-canvas/60 p-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-brand-soft text-brand-ink">
                            <Icon name="file" class="size-5" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ form.file.name }}</p>
                            <p class="text-xs text-muted">{{ formatFileSize(form.file.size) }}</p>
                        </div>

                        <Button variant="ghost" size="icon-sm" icon="x" aria-label="Hapus file yang dipilih" :disabled="form.processing" @click="removeFile" />
                    </div>

                    <div v-if="form.progress" class="mt-3 h-1.5 overflow-hidden rounded-full bg-ink/8">
                        <div class="h-full rounded-full bg-brand transition-[width]" :style="{ width: `${form.progress.percentage}%` }" />
                    </div>
                </div>

                <Alert v-if="form.errors.file" variant="danger">
                    <p>{{ form.errors.file }}</p>

                    <ul v-if="rowErrors.length > 0" class="mt-2 flex max-h-64 list-disc flex-col gap-1 overflow-y-auto pl-5 text-sm">
                        <li v-for="message in rowErrors" :key="message">{{ message }}</li>
                    </ul>
                </Alert>

                <div class="flex flex-wrap gap-2">
                    <Button type="submit" icon="upload" :disabled="!form.file || form.processing">
                        {{ form.processing ? 'Mengimpor…' : 'Import penduduk' }}
                    </Button>
                    <Button :href="route('penduduk.index')" variant="secondary">Batal</Button>
                </div>
            </form>
        </Card>

        <Card class="flex flex-col p-5 sm:p-6 lg:col-span-2">
            <h2 class="text-lg font-medium">Format file</h2>
            <p class="mt-1 text-sm text-muted">Baris pertama berisi semua nama kolom di bawah, urutannya bebas. Satu baris untuk satu penduduk.</p>

            <ul class="mt-4 flex flex-wrap gap-1.5">
                <li
                    v-for="column in columns"
                    :key="column"
                    :class="[
                        'rounded-md px-2 py-1 text-xs font-medium',
                        REQUIRED_VALUE_COLUMNS.includes(column) ? 'bg-brand-soft text-brand-ink' : 'bg-ink/6 text-ink',
                    ]"
                >
                    {{ column }}<span v-if="REQUIRED_VALUE_COLUMNS.includes(column)" aria-label="isi wajib">*</span>
                </li>
            </ul>
            <p class="mt-2 text-xs text-muted">Semua kolom harus ada. Kolom bertanda * isinya wajib diisi, yang lain boleh kosong.</p>

            <ul class="mt-4 flex list-disc flex-col gap-1.5 pl-5 text-sm text-ink/85 marker:text-muted">
                <li>NIK 16 angka dan belum terdaftar.</li>
                <li>Tanggal lahir boleh format tanggal Excel atau teks seperti 01-05-1990.</li>
                <li>Jenis kelamin: Laki-laki / Perempuan (boleh L / P).</li>
                <li>Jika ada satu baris yang salah, tidak ada data yang disimpan.</li>
            </ul>

            <div class="mt-5 rounded-xl bg-canvas/70 p-4 text-sm lg:mt-auto">
                <p class="font-medium">Gunakan template</p>
                <p class="mt-1 text-muted">Template berisi nama kolom yang benar dan petunjuk pengisian.</p>
                <Button variant="secondary" size="sm" icon="download" class="mt-3" @click="downloadTemplate">Unduh template Excel</Button>
            </div>
        </Card>
    </div>
</template>
