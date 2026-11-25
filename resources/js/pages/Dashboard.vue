<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import Avatar from '@/components/ui/Avatar.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Chip from '@/components/ui/Chip.vue';
import DropdownMenu from '@/components/ui/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/DropdownMenuSeparator.vue';
import Input from '@/components/ui/Input.vue';
import Pagination from '@/components/ui/Pagination.vue';
import Select from '@/components/ui/Select.vue';
import StatCard from '@/components/ui/StatCard.vue';
import Table from '@/components/ui/Table.vue';
import { useNewLetterModal } from '@/composables/useNewLetterModal';

const props = defineProps({
    letters: { type: Object, required: true },
    statuses: { type: Object, required: true },
    statusCounts: { type: Object, required: true },
    totalLetters: { type: Number, required: true },
    overdueRecipients: { type: String, required: true },
    activeStatus: { type: String, default: null },
    search: { type: String, default: '' },
    perPage: { type: Number, required: true },
    perPageOptions: { type: Array, required: true },
    queryWithoutStatus: { type: [Object, Array], default: () => ({}) },
});

/** Build a dashboard URL, dropping empty query values (Ziggy keeps empty strings such as `q=`). */
function dashboardUrl(query = {}) {
    const filledQuery = Object.fromEntries(
        Object.entries(query).filter(([, value]) => value !== null && value !== undefined && value !== ''),
    );

    return route('dashboard', filledQuery);
}

/** Reload the list with new filters, keeping the search input and scroll position. */
function reload(query) {
    router.get(dashboardUrl(query), {}, { preserveState: true, preserveScroll: true, replace: true });
}

const { openNewLetterModal } = useNewLetterModal();

const search = ref(props.search);
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        reload({ status: props.activeStatus, ...props.queryWithoutStatus, q: value });
    }, 300);
});

onBeforeUnmount(() => clearTimeout(searchTimeout));

const perPage = computed({
    get: () => props.perPage,
    set: (value) => reload({ status: props.activeStatus, q: props.search, per_page: value }),
});

const statusActions = {
    draft: [{ label: 'Kirim', icon: 'send', variant: 'secondary' }],
    overdue: [
        { label: 'Ingatkan', icon: 'bell', variant: 'ghost' },
        { label: 'Selesai', icon: 'check', variant: 'primary' },
    ],
    awaiting: [{ label: 'Selesai', icon: 'check', variant: 'primary' }],
    sent: [],
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 pt-6 sm:flex-row sm:items-end sm:justify-between lg:pt-10">
        <div>
            <h1 class="text-4xl font-medium tracking-tight sm:text-5xl">Dashboard</h1>
            <p class="mt-3 text-muted">Buat, kirim, dan pantau status setiap surat keluar dari satu tempat.</p>
        </div>

        <Button size="lg" icon="plus" @click="openNewLetterModal">Surat baru</Button>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard tone="peach" icon="clock" label="Menunggu balasan" :value="statusCounts.awaiting ?? 0" caption="surat belum dibalas" />
        <StatCard tone="sky" icon="triangle-alert" label="Terlambat" :value="statusCounts.overdue ?? 0" :caption="overdueRecipients || 'Tidak ada'" />
        <StatCard tone="mint" icon="arrow-down-left" label="Sudah dibalas" :value="statusCounts.sent ?? 0" caption="bulan ini" />
        <StatCard tone="cream" icon="file" label="Draf" :value="statusCounts.draft ?? 0" caption="siap dikirim" />
    </div>

    <div class="mt-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
            <Chip :href="dashboardUrl(queryWithoutStatus)" :active="!activeStatus" :count="totalLetters">Semua</Chip>

            <Chip
                v-for="(status, statusKey) in statuses"
                :key="statusKey"
                :href="dashboardUrl({ status: statusKey, ...queryWithoutStatus })"
                :active="activeStatus === statusKey"
                :count="statusCounts[statusKey] ?? 0"
            >
                {{ status.label }}
            </Chip>
        </div>

        <form role="search" class="w-full lg:max-w-sm" @submit.prevent>
            <Input v-model="search" type="search" icon="search" placeholder="Nomor, perihal, atau penerima" aria-label="Cari surat" />
        </form>
    </div>

    <Table class="mt-4">
        <template #head>
            <th class="w-14 text-center">No</th>
            <th>Surat</th>
            <th>Penerima</th>
            <th class="hidden xl:table-cell">Perihal</th>
            <th>Tenggat</th>
            <th>Status</th>
            <th><span class="sr-only">Aksi</span></th>
        </template>

        <tr v-for="(letter, index) in letters.data" :key="letter.number">
            <td class="text-center text-muted tabular-nums">{{ letters.from + index }}</td>

            <td class="whitespace-nowrap">
                <div class="font-medium">{{ letter.number }}</div>
                <div class="mt-0.5 text-muted">{{ letter.date }}</div>
            </td>

            <td>
                <div class="flex items-center gap-3">
                    <Avatar :name="letter.recipient" />

                    <div class="min-w-0">
                        <div class="truncate font-medium">{{ letter.recipient }}</div>
                        <div class="mt-0.5 truncate text-muted">{{ letter.contact }}</div>
                    </div>
                </div>
            </td>

            <td class="hidden text-muted xl:table-cell">{{ letter.subject }}</td>

            <td :class="['whitespace-nowrap', { 'text-danger': letter.is_late }]">{{ letter.due }}</td>

            <td>
                <Badge
                    :variant="statuses[letter.status].variant"
                    :dot="['overdue', 'awaiting'].includes(letter.status)"
                >
                    {{ statuses[letter.status].label }}
                </Badge>
            </td>

            <td>
                <div class="flex items-center justify-end gap-2">
                    <Button
                        v-for="action in statusActions[letter.status]"
                        :key="action.label"
                        :variant="action.variant"
                        :icon="action.icon"
                        size="sm"
                    >
                        {{ action.label }}
                    </Button>

                    <DropdownMenu>
                        <template #trigger>
                            <Button variant="ghost" size="icon-sm" icon="ellipsis" :aria-label="`Aksi lain untuk ${letter.number}`" />
                        </template>

                        <DropdownMenuItem icon="eye">Lihat surat</DropdownMenuItem>
                        <DropdownMenuItem icon="pencil">Edit</DropdownMenuItem>
                        <DropdownMenuItem icon="copy">Duplikat</DropdownMenuItem>
                        <DropdownMenuItem icon="download">Unduh PDF</DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem icon="trash" variant="danger">Hapus</DropdownMenuItem>
                    </DropdownMenu>
                </div>
            </td>
        </tr>

        <tr v-if="letters.data.length === 0">
            <td colspan="7" class="py-14 text-center text-muted">Tidak ada surat yang cocok dengan filter ini.</td>
        </tr>

        <template #footer>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    <span v-if="letters.total > 0">
                        Menampilkan {{ letters.from }}–{{ letters.to }} dari {{ letters.total }} surat
                    </span>
                    <span v-else>Menampilkan 0 surat</span>

                    <div class="flex items-center gap-2">
                        <span id="per-page-label" class="whitespace-nowrap">Baris per halaman</span>
                        <Select v-model="perPage" :options="perPageOptions" size="sm" class="w-18" aria-labelledby="per-page-label" />
                    </div>
                </div>

                <Pagination :paginator="letters" />
            </div>
        </template>
    </Table>
</template>
