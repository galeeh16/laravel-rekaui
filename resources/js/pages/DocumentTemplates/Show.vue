<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { route } from 'ziggy-js';
import NikLookupInput from '@/components/penduduk/NikLookupInput.vue';
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Icon from '@/components/ui/Icon.vue';
import Input from '@/components/ui/Input.vue';
import { toast } from '@/composables/useToast';
import { formatDate, formatFileSize, placeholderFor } from '@/lib/format';

const props = defineProps({
    template: { type: Object, required: true },
});

const values = reactive(Object.fromEntries(props.template.fields.map((field) => [field.name, ''])));
const errors = ref({});
const isGenerating = ref(false);

/** The `${nik}` variable doubles as the penduduk search, shown first. */
const NIK_VARIABLE = 'nik';

const hasNikVariable = props.template.fields.some((field) => field.name === NIK_VARIABLE);
const otherFields = computed(() => props.template.fields.filter((field) => field.name !== NIK_VARIABLE));

/** Without a `${nik}` variable the NIK box only searches; its value is not part of the document. */
const searchOnlyNik = ref('');
const nikValue = computed({
    get: () => (hasNikVariable ? values[NIK_VARIABLE] : searchOnlyNik.value),
    set: (value) => {
        if (hasNikVariable) {
            values[NIK_VARIABLE] = value;
        } else {
            searchOnlyNik.value = value;
        }
    },
});

/** `{ nama, count }` of the resident whose data was filled in last. */
const filledFrom = ref(null);

/** Copy the resident's data into every variable named after a penduduk field. */
function fillFromResident(resident) {
    let filledCount = 0;

    for (const field of props.template.fields) {
        const value = resident.fields[field.name];

        if (value !== undefined) {
            values[field.name] = value;
            delete errors.value[field.name];
            filledCount++;
        }
    }

    filledFrom.value = { nama: resident.nama, count: filledCount };
}

/** Laravel's XSRF-TOKEN cookie, sent back as a header for requests made outside Inertia. */
function xsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/);

    return match ? decodeURIComponent(match[1]) : '';
}

function fileNameFrom(contentDisposition) {
    const match = contentDisposition?.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);

    return match ? decodeURIComponent(match[1]) : 'dokumen.docx';
}

function saveBlob(blob, fileName) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = fileName;
    link.click();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
}

/**
 * Generate the filled document. This is a plain fetch instead of an Inertia request because the
 * response is a file; validation errors come back as JSON (422) and are shown under the fields.
 */
async function generateDocument() {
    isGenerating.value = true;
    errors.value = {};

    try {
        const response = await fetch(route('document-templates.generate', props.template.id), {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': xsrfToken(),
            },
            body: JSON.stringify({ values }),
        });

        if (response.status === 422) {
            const { errors: validationErrors } = await response.json();

            errors.value = Object.fromEntries(
                Object.entries(validationErrors).map(([key, messages]) => [key.replace(/^values\./, ''), messages[0]]),
            );

            return;
        }

        if (response.status === 419) {
            toast({ title: 'Sesi berakhir', description: 'Muat ulang halaman, lalu coba lagi.', variant: 'warning' });

            return;
        }

        if (!response.ok) {
            throw new Error(`Unexpected status ${response.status}`);
        }

        saveBlob(await response.blob(), fileNameFrom(response.headers.get('Content-Disposition')));
        toast({ title: 'Dokumen berhasil dibuat', description: 'File Word sedang diunduh.', variant: 'success' });
    } catch {
        toast({ title: 'Gagal membuat dokumen', description: 'Coba lagi beberapa saat lagi.', variant: 'danger' });
    } finally {
        isGenerating.value = false;
    }
}

function resetValues() {
    Object.keys(values).forEach((name) => (values[name] = ''));
    searchOnlyNik.value = '';
    filledFrom.value = null;
    errors.value = {};
}

function downloadOriginal() {
    window.location.href = route('document-templates.download', props.template.id);
}

const isDeleteDialogOpen = ref(false);
const isDeleting = ref(false);

function destroyTemplate() {
    router.delete(route('document-templates.destroy', props.template.id), {
        onStart: () => (isDeleting.value = true),
        onFinish: () => (isDeleting.value = false),
    });
}
</script>

<template>
    <Head :title="template.name" />

    <div class="flex flex-col gap-6 pt-6 sm:flex-row sm:items-end sm:justify-between lg:pt-10">
        <div class="min-w-0">
            <Link
                :href="route('document-templates.index')"
                class="inline-flex items-center gap-1 text-sm text-muted transition-colors hover:text-ink"
            >
                <Icon name="arrow-left" class="size-4" />
                List Dokumen
            </Link>

            <h1 class="mt-3 truncate text-4xl font-medium tracking-tight sm:text-5xl">{{ template.name }}</h1>
            <p class="mt-3 text-muted">
                {{ template.original_filename }} · {{ formatFileSize(template.size) }} · diunggah
                {{ template.uploaded_by ? `oleh ${template.uploaded_by}` : '' }} {{ formatDate(template.uploaded_at) }}
                <template v-if="template.was_edited">
                    · diperbarui {{ template.updated_by ? `oleh ${template.updated_by}` : '' }} {{ formatDate(template.updated_at) }}
                </template>
            </p>
        </div>

        <div class="flex shrink-0 gap-2">
            <Button variant="secondary" icon="download" @click="downloadOriginal">Template asli</Button>
            <Button v-if="template.can_delete" variant="ghost" icon="trash" @click="isDeleteDialogOpen = true">Hapus</Button>
        </div>
    </div>

    <Card class="mt-8 p-5 sm:p-6">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
            <h2 class="text-lg font-medium">Isi data dokumen</h2>
            <p class="text-sm text-muted">{{ template.fields.length }} variabel ditemukan di template</p>
        </div>

        <form class="mt-6 flex flex-col gap-6" @submit.prevent="generateDocument">
            <div class="rounded-xl bg-canvas/70 p-4">
                <NikLookupInput
                    v-model="nikValue"
                    :label="hasNikVariable ? 'NIK' : 'Cari penduduk (NIK)'"
                    :hint="
                        hasNikVariable
                            ? `Mengganti ${placeholderFor(NIK_VARIABLE)}. Pilih penduduk untuk mengisi kolom lain otomatis.`
                            : 'Hanya untuk mencari data penduduk; NIK tidak dimasukkan ke dokumen.'
                    "
                    :error="hasNikVariable ? errors[NIK_VARIABLE] : null"
                    @select="fillFromResident"
                />

                <p v-if="filledFrom" class="mt-3 flex items-center gap-2 text-sm text-brand-ink">
                    <Icon name="circle-check" class="size-4 shrink-0 text-brand" />
                    <span>
                        {{ filledFrom.count }} kolom diisi otomatis dari data <strong class="font-medium">{{ filledFrom.nama }}</strong>.
                        Periksa kembali sebelum membuat dokumen.
                    </span>
                </p>
            </div>

            <div class="grid gap-x-5 gap-y-4 sm:grid-cols-2">
                <Input
                    v-for="field in otherFields"
                    :key="field.name"
                    v-model="values[field.name]"
                    :label="field.label"
                    :hint="`Mengganti ${placeholderFor(field.name)}`"
                    :error="errors[field.name]"
                />
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-line pt-5">
                <Button type="submit" icon="download" :disabled="isGenerating">
                    {{ isGenerating ? 'Membuat dokumen…' : 'Buat & unduh dokumen' }}
                </Button>
                <Button variant="ghost" :disabled="isGenerating" @click="resetValues">Kosongkan isian</Button>
            </div>
        </form>
    </Card>

    <AlertDialog
        v-model:open="isDeleteDialogOpen"
        variant="danger"
        title="Hapus template ini?"
        :description="`Template “${template.name}” dan filenya akan dihapus permanen.`"
        confirm-label="Hapus"
        :close-on-confirm="false"
        :processing="isDeleting"
        @confirm="destroyTemplate"
    />
</template>
