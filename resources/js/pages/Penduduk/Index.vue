<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Avatar from '@/components/ui/Avatar.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Pagination from '@/components/ui/Pagination.vue';
import Select from '@/components/ui/Select.vue';
import Table from '@/components/ui/Table.vue';
import { formatCalendarDate, formatDateTime } from '@/lib/format';

const props = defineProps({
    residents: { type: Object, required: true },
    search: { type: String, default: '' },
    /** Selected gender filter (`male` / `female`), or null for everyone. */
    gender: { type: String, default: null },
    genderOptions: { type: Array, required: true },
    perPage: { type: Number, required: true },
    perPageOptions: { type: Array, required: true },
});

/** The actions column stays visible on the right while the wide table scrolls horizontally. */
const STICKY_ACTIONS_CLASS = 'sticky right-0 bg-surface shadow-[-12px_0_12px_-12px_rgb(0_0_0/0.15)]';

/** Build a list URL, dropping empty query values (Ziggy keeps empty strings such as `q=`). */
function listUrl(query = {}) {
    const filledQuery = Object.fromEntries(
        Object.entries(query).filter(([, value]) => value !== null && value !== undefined && value !== ''),
    );

    return route('penduduk.index', filledQuery);
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
        reload({ q: value, jenis_kelamin: props.gender, per_page: props.perPage });
    }, 300);
});

onBeforeUnmount(() => clearTimeout(searchTimeout));

/** Clearing the select (null) shows every gender again. */
const gender = computed({
    get: () => props.gender,
    set: (value) => reload({ q: props.search, jenis_kelamin: value, per_page: props.perPage }),
});

const perPage = computed({
    get: () => props.perPage,
    set: (value) => reload({ q: props.search, jenis_kelamin: props.gender, per_page: value }),
});

const residentToDelete = ref(null);
const isDeleteDialogOpen = ref(false);
const isDeleting = ref(false);

function confirmDelete(resident) {
    residentToDelete.value = resident;
    isDeleteDialogOpen.value = true;
}

function destroyResident() {
    router.delete(route('penduduk.destroy', residentToDelete.value.id), {
        preserveScroll: true,
        onStart: () => (isDeleting.value = true),
        onFinish: () => (isDeleting.value = false),
        onSuccess: () => (isDeleteDialogOpen.value = false),
    });
}
</script>

<template>
    <Head title="List Penduduk" />

    <div class="pt-6 lg:pt-10">
        <h1 class="text-4xl font-medium tracking-tight sm:text-5xl">List Penduduk</h1>
        <p class="mt-3 text-muted">Data penduduk yang terdaftar. Cari berdasarkan nama atau NIK, dan saring menurut jenis kelamin.</p>
    </div>

    <Table class="mt-8">
        <template #toolbar>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <Button :href="route('penduduk.create')" size="lg" icon="plus" class="w-full sm:w-auto sm:self-start lg:self-auto">
                    Tambah Penduduk
                </Button>

                <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-center lg:flex-1 lg:justify-end">
                    <Select
                        v-model="gender"
                        :options="genderOptions"
                        placeholder="Semua jenis kelamin"
                        clearable
                        class="shrink-0 sm:w-56"
                        aria-label="Filter jenis kelamin"
                    />

                    <form role="search" class="min-w-0 w-full sm:flex-1 lg:max-w-sm" @submit.prevent>
                        <Input v-model="search" type="search" icon="search" placeholder="Nama atau NIK" aria-label="Cari penduduk" />
                    </form>
                </div>
            </div>
        </template>

        <template #head>
            <th class="w-14 text-center">No</th>
            <th>Nama</th>
            <th>NIK</th>
            <th>Tempat lahir</th>
            <th>Tanggal lahir</th>
            <th>Jenis kelamin</th>
            <th>Agama</th>
            <th>Status perkawinan</th>
            <th>Pekerjaan</th>
            <th>Kewarganegaraan</th>
            <th>Alamat lengkap</th>
            <th>Dibuat</th>
            <th>Diperbarui</th>
            <th :class="STICKY_ACTIONS_CLASS"><span class="sr-only">Aksi</span></th>
        </template>

        <tr v-for="(resident, index) in residents.data" :key="resident.id" class="[&>td]:whitespace-nowrap">
            <td class="text-center text-muted tabular-nums">{{ residents.from + index }}</td>

            <td>
                <div class="flex items-center gap-3">
                    <Avatar :name="resident.nama" />
                    <span class="font-medium">{{ resident.nama }}</span>
                </div>
            </td>

            <td class="tabular-nums">{{ resident.nik }}</td>
            <td>{{ resident.tempat_lahir }}</td>
            <td>{{ formatCalendarDate(resident.tanggal_lahir) }}</td>
            <td>{{ resident.jenis_kelamin }}</td>
            <td>{{ resident.agama ?? '—' }}</td>
            <td>{{ resident.status_perkawinan ?? '—' }}</td>
            <td>{{ resident.pekerjaan || '—' }}</td>
            <td>{{ resident.kewarganegaraan }}</td>

            <!-- Long addresses wrap (up to two lines) instead of widening the table -->
            <td class="min-w-64 whitespace-normal!">
                <span class="line-clamp-2 text-muted">{{ resident.alamat_lengkap || '—' }}</span>
            </td>

            <td class="text-muted tabular-nums">{{ resident.created_at ? formatDateTime(resident.created_at) : '—' }}</td>
            <td class="text-muted tabular-nums">{{ resident.updated_at ? formatDateTime(resident.updated_at) : '—' }}</td>

            <td :class="STICKY_ACTIONS_CLASS">
                <div class="flex items-center justify-end gap-2">
                    <Button :href="route('penduduk.edit', resident.id)" variant="soft" size="sm" icon="pencil">Edit</Button>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        icon="trash"
                        :aria-label="`Hapus ${resident.nama}`"
                        @click="confirmDelete(resident)"
                    />
                </div>
            </td>
        </tr>

        <tr v-if="residents.data.length === 0 && (props.search || props.gender)">
            <td colspan="14" class="py-14 text-center text-muted">Tidak ada penduduk yang cocok dengan pencarian atau filter ini.</td>
        </tr>

        <tr v-else-if="residents.data.length === 0">
            <td colspan="14" class="py-14 text-center">
                <p class="text-muted">Belum ada data penduduk.</p>
                <Button :href="route('penduduk.create')" variant="secondary" size="sm" icon="plus" class="mt-3">Tambah Penduduk</Button>
            </td>
        </tr>

        <template #footer>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    <span v-if="residents.total > 0">
                        Menampilkan {{ residents.from }}–{{ residents.to }} dari {{ residents.total }} penduduk
                    </span>
                    <span v-else>Menampilkan 0 penduduk</span>

                    <div class="flex items-center gap-2">
                        <span id="per-page-label" class="whitespace-nowrap">Baris per halaman</span>
                        <Select v-model="perPage" :options="perPageOptions" size="sm" class="w-18" aria-labelledby="per-page-label" />
                    </div>
                </div>

                <Pagination :paginator="residents" />
            </div>
        </template>
    </Table>

    <AlertDialog
        v-model:open="isDeleteDialogOpen"
        variant="danger"
        title="Hapus data penduduk ini?"
        :description="residentToDelete ? `Data ${residentToDelete.nama} (NIK ${residentToDelete.nik}) akan dihapus permanen.` : null"
        confirm-label="Hapus"
        :close-on-confirm="false"
        :processing="isDeleting"
        @confirm="destroyResident"
    />
</template>
