<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Pagination from '@/components/ui/Pagination.vue';
import Select from '@/components/ui/Select.vue';
import Table from '@/components/ui/Table.vue';
import { formatDateTime } from '@/lib/format';

const props = defineProps({
    applications: { type: Object, required: true },
    search: { type: String, default: '' },
    perPage: { type: Number, required: true },
    perPageOptions: { type: Array, required: true },
});

/** Build a list URL, dropping empty query values (Ziggy keeps empty strings such as `q=`). */
function listUrl(query = {}) {
    const filledQuery = Object.fromEntries(
        Object.entries(query).filter(([, value]) => value !== null && value !== undefined && value !== ''),
    );

    return route('document-applications.index', filledQuery);
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
</script>

<template>
    <Head title="List Pengajuan" />

    <div class="pt-6 lg:pt-10">
        <h1 class="text-4xl font-medium tracking-tight sm:text-5xl">List Pengajuan</h1>
        <p class="mt-3 text-muted">Riwayat dokumen dan surat yang diajukan oleh penduduk, dari yang terbaru.</p>
    </div>

    <Table class="mt-8">
        <template #toolbar>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <Button :href="route('document-applications.create')" size="lg" icon="plus" class="w-full sm:w-auto">
                    Buat Pengajuan
                </Button>

                <form role="search" class="min-w-0 w-full sm:max-w-sm" @submit.prevent>
                    <Input v-model="search" type="search" icon="search" placeholder="Nama, NIK, dokumen, atau keperluan" aria-label="Cari pengajuan" />
                </form>
            </div>
        </template>
        <template #head>
            <th class="w-14 text-center">No</th>
            <th>Pemohon</th>
            <th>Dokumen</th>
            <th class="hidden lg:table-cell">Keperluan</th>
            <th class="hidden md:table-cell">Diajukan</th>
            <th><span class="sr-only">Aksi</span></th>
        </template>

        <tr v-for="(application, index) in applications.data" :key="application.id">
            <td class="text-center text-muted tabular-nums">{{ applications.from + index }}</td>

            <td>
                <div class="font-medium">{{ application.applicant_name }}</div>
                <div class="mt-0.5 text-muted tabular-nums">{{ application.applicant_nik }}</div>
            </td>

            <td>
                <Link
                    v-if="application.document_template_id"
                    :href="route('document-templates.show', application.document_template_id)"
                    class="hover:underline"
                >
                    {{ application.template_name }}
                </Link>
                <template v-else>
                    <div>{{ application.template_name }}</div>
                    <div class="mt-0.5 text-xs text-danger">Template sudah dihapus</div>
                </template>
            </td>

            <td class="hidden max-w-xs lg:table-cell">
                <p v-if="application.purpose" class="line-clamp-2">{{ application.purpose }}</p>
                <span v-else class="text-muted">—</span>
            </td>

            <td class="hidden whitespace-nowrap md:table-cell">
                <div class="tabular-nums">{{ formatDateTime(application.created_at) }}</div>
                <div v-if="application.recorded_by" class="mt-0.5 text-muted">oleh {{ application.recorded_by }}</div>
            </td>

            <td>
                <div class="flex justify-end">
                    <Button
                        v-if="application.document_template_id"
                        :href="route('document-templates.show', { documentTemplate: application.document_template_id, nik: application.applicant_nik })"
                        variant="soft"
                        size="sm"
                        icon="pencil"
                    >
                        Buat dokumen
                    </Button>
                </div>
            </td>
        </tr>

        <tr v-if="applications.data.length === 0 && props.search">
            <td colspan="6" class="py-14 text-center text-muted">Tidak ada pengajuan yang cocok dengan pencarian ini.</td>
        </tr>

        <tr v-else-if="applications.data.length === 0">
            <td colspan="6" class="py-14 text-center">
                <p class="text-muted">Belum ada pengajuan dokumen.</p>
                <Button :href="route('document-applications.create')" variant="secondary" size="sm" icon="plus" class="mt-3">
                    Buat Pengajuan
                </Button>
            </td>
        </tr>

        <template #footer>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    <span v-if="applications.total > 0">
                        Menampilkan {{ applications.from }}–{{ applications.to }} dari {{ applications.total }} pengajuan
                    </span>
                    <span v-else>Menampilkan 0 pengajuan</span>

                    <div class="flex items-center gap-2">
                        <span id="per-page-label" class="whitespace-nowrap">Baris per halaman</span>
                        <Select v-model="perPage" :options="perPageOptions" size="sm" class="w-18" aria-labelledby="per-page-label" />
                    </div>
                </div>

                <Pagination :paginator="applications" />
            </div>
        </template>
    </Table>
</template>
