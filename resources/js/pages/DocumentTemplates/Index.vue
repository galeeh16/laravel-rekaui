<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Icon from '@/components/ui/Icon.vue';
import Input from '@/components/ui/Input.vue';
import Modal from '@/components/ui/Modal.vue';
import Pagination from '@/components/ui/Pagination.vue';
import Select from '@/components/ui/Select.vue';
import Table from '@/components/ui/Table.vue';
import { formatDateTime, formatFileSize, placeholderFor } from '@/lib/format';

const props = defineProps({
    templates: { type: Object, required: true },
    search: { type: String, default: '' },
    perPage: { type: Number, required: true },
    perPageOptions: { type: Array, required: true },
});

/** How many variables a row shows before "+N". */
const VISIBLE_VARIABLES = 4;

/** Build a list URL, dropping empty query values (Ziggy keeps empty strings such as `q=`). */
function listUrl(query = {}) {
    const filledQuery = Object.fromEntries(
        Object.entries(query).filter(([, value]) => value !== null && value !== undefined && value !== ''),
    );

    return route('document-templates.index', filledQuery);
}

/** Reload the list with new filters, keeping the search input and scroll position. */
function reload(query) {
    router.get(listUrl(query), {}, { preserveState: true, preserveScroll: true, replace: true });
}

const search = ref(props.search);
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        reload({ q: value, per_page: props.perPage });
    }, 300);
});

onBeforeUnmount(() => clearTimeout(searchTimeout));

const perPage = computed({
    get: () => props.perPage,
    set: (value) => reload({ q: props.search, per_page: value }),
});

/** File responses can't be Inertia visits, so downloads use a normal browser navigation. */
function download(url) {
    window.location.href = url;
}

const templateToRename = ref(null);
const isRenameModalOpen = ref(false);
const renameForm = useForm({ name: '' });

function openRenameModal(template) {
    templateToRename.value = template;
    renameForm.defaults({ name: template.name });
    renameForm.reset();
    renameForm.clearErrors();
    isRenameModalOpen.value = true;
}

/** On success the server saves the new name and reloads this page with a success toast. */
function renameTemplate() {
    renameForm.patch(route('document-templates.update', templateToRename.value.id), {
        preserveScroll: true,
        onSuccess: () => (isRenameModalOpen.value = false),
    });
}

watch(
    () => renameForm.name,
    () => renameForm.clearErrors('name'),
);

const templateToDelete = ref(null);
const isDeleteDialogOpen = ref(false);
const isDeleting = ref(false);

function confirmDelete(template) {
    templateToDelete.value = template;
    isDeleteDialogOpen.value = true;
}

function destroyTemplate() {
    router.delete(route('document-templates.destroy', templateToDelete.value.id), {
        preserveScroll: true,
        onStart: () => (isDeleting.value = true),
        onFinish: () => (isDeleting.value = false),
        onSuccess: () => (isDeleteDialogOpen.value = false),
    });
}
</script>

<template>
    <Head title="List Dokumen" />

    <div class="flex flex-col gap-6 pt-6 sm:flex-row sm:items-end sm:justify-between lg:pt-10">
        <div>
            <h1 class="text-3xl font-medium tracking-tight sm:text-4xl">List Template</h1>
            <p class="mt-3 text-muted">Template Word yang sudah diunggah. Pilih salah satu untuk mengisi dan mengunduh dokumennya.</p>
        </div>

        <Button :href="route('document-templates.create')" size="lg" icon="plus">Buat Template</Button>
    </div>

    <div class="mt-8 flex justify-end">
        <form role="search" class="w-full lg:max-w-sm" @submit.prevent>
            <Input v-model="search" type="search" icon="search" placeholder="Nama template, file, atau variabel" aria-label="Cari template" />
        </form>
    </div>

    <Table class="mt-4">
        <template #head>
            <th class="w-14 text-center">No</th>
            <th>Template</th>
            <th>Variabel</th>
            <th class="hidden md:table-cell">Diunggah</th>
            <th class="hidden md:table-cell">Diperbarui</th>
            <th><span class="sr-only">Aksi</span></th>
        </template>

        <tr v-for="(template, index) in templates.data" :key="template.id">
            <td class="text-center text-muted tabular-nums">{{ templates.from + index }}</td>

            <td>
                <div class="flex items-center gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-info-soft text-info">
                        <Icon name="file-text" class="size-5" />
                    </span>

                    <div class="min-w-0">
                        <Link :href="route('document-templates.show', template.id)" class="block truncate font-medium hover:underline">
                            {{ template.name }}
                        </Link>
                        <div class="mt-0.5 truncate text-muted">{{ template.original_filename }} · {{ formatFileSize(template.size) }}</div>
                    </div>
                </div>
            </td>

            <td>
                <div class="flex flex-wrap gap-1.5">
                    <Badge v-for="variable in template.variables.slice(0, VISIBLE_VARIABLES)" :key="variable">
                        <span class="font-mono text-xs">{{ placeholderFor(variable) }}</span>
                    </Badge>
                    <Badge v-if="template.variables.length > VISIBLE_VARIABLES" variant="info">
                        +{{ template.variables.length - VISIBLE_VARIABLES }}
                    </Badge>
                </div>
            </td>

            <td class="hidden whitespace-nowrap md:table-cell">
                <div>{{ template.uploaded_by ?? 'Pengguna dihapus' }}</div>
                <div class="mt-0.5 text-muted tabular-nums">{{ formatDateTime(template.uploaded_at) }}</div>
            </td>

            <td class="hidden whitespace-nowrap md:table-cell">
                <template v-if="template.updated_by">
                    <div>{{ template.updated_by }}</div>
                    <div class="mt-0.5 text-muted tabular-nums">{{ formatDateTime(template.updated_at) }}</div>
                </template>
            </td>

            <td>
                <div class="flex items-center justify-end gap-2">
                    <Button :href="route('document-templates.show', template.id)" variant="soft" size="sm" icon="pencil">Isi</Button>

                    <DropdownMenu>
                        <template #trigger>
                            <Button variant="ghost" size="icon-sm" icon="ellipsis" :aria-label="`Aksi lain untuk ${template.name}`" />
                        </template>

                        <DropdownMenuItem icon="download" @select="download(route('document-templates.download', template.id))">
                            Unduh template asli
                        </DropdownMenuItem>

                        <DropdownMenuItem v-if="template.can_update" icon="pen-line" @select="openRenameModal(template)">
                            Edit Nama Template
                        </DropdownMenuItem>

                        <template v-if="template.can_delete">
                            <DropdownMenuSeparator />
                            <DropdownMenuItem icon="trash" variant="danger" @select="confirmDelete(template)">Hapus</DropdownMenuItem>
                        </template>
                    </DropdownMenu>
                </div>
            </td>
        </tr>

        <tr v-if="templates.data.length === 0 && props.search">
            <td colspan="6" class="py-14 text-center text-muted">Tidak ada template yang cocok dengan pencarian ini.</td>
        </tr>

        <tr v-else-if="templates.data.length === 0">
            <td colspan="6" class="py-14 text-center">
                <p class="text-muted">Belum ada template dokumen.</p>
                <Button :href="route('document-templates.create')" variant="secondary" size="sm" icon="plus" class="mt-3">
                    Buat Template
                </Button>
            </td>
        </tr>

        <template #footer>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    <span v-if="templates.total > 0">
                        Menampilkan {{ templates.from }}–{{ templates.to }} dari {{ templates.total }} template
                    </span>
                    <span v-else>Menampilkan 0 template</span>

                    <div class="flex items-center gap-2">
                        <span id="per-page-label" class="whitespace-nowrap">Baris per halaman</span>
                        <Select v-model="perPage" :options="perPageOptions" size="sm" class="w-18" aria-labelledby="per-page-label" />
                    </div>
                </div>

                <Pagination :paginator="templates" />
            </div>
        </template>
    </Table>

    <Modal
        v-model:open="isRenameModalOpen"
        title="Edit Nama Template"
        :description="templateToRename ? `File: ${templateToRename.original_filename}` : null"
        size="md"
    >
        <form id="rename-template-form" novalidate @submit.prevent="renameTemplate">
            <Input
                v-model="renameForm.name"
                label="Nama Template"
                name="name"
                maxlength="255"
                autocomplete="off"
                :error="renameForm.errors.name"
            />
        </form>

        <template #footer>
            <Button variant="secondary" @click="isRenameModalOpen = false">Batal</Button>
            <Button
                type="submit"
                form="rename-template-form"
                :disabled="renameForm.processing || !renameForm.name.trim() || !renameForm.isDirty"
            >
                {{ renameForm.processing ? 'Menyimpan…' : 'Simpan' }}
            </Button>
        </template>
    </Modal>

    <AlertDialog
        v-model:open="isDeleteDialogOpen"
        variant="danger"
        title="Hapus template ini?"
        :description="templateToDelete ? `Template “${templateToDelete.name}” dan filenya akan dihapus permanen.` : null"
        confirm-label="Hapus"
        :close-on-confirm="false"
        :processing="isDeleting"
        @confirm="destroyTemplate"
    />
</template>
