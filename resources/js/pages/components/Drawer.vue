<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Avatar from '@/components/ui/Avatar.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Drawer from '@/components/ui/Drawer.vue';
import Select from '@/components/ui/Select.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

/* Example 1: bottom sheet with a trigger slot */
const recipients = [
    { name: 'Rina Kartika', company: 'PT Sinar Mandiri' },
    { name: 'Bagus Pratama', company: 'CV Arunika Desain' },
    { name: 'Anisa Rahma', company: 'Yayasan Pelita Harapan' },
    { name: 'Sri Wahyuni', company: 'Koperasi Maju Bersama' },
];

/* Example 2: right-side filter panel controlled with v-model:open */
const statusOptions = [
    { value: 'all', label: 'Semua status' },
    { value: 'draft', label: 'Draf' },
    { value: 'awaiting', label: 'Menunggu' },
    { value: 'overdue', label: 'Terlambat' },
    { value: 'sent', label: 'Terkirim' },
];

const periodOptions = [
    { value: '7', label: '7 hari terakhir' },
    { value: '30', label: '30 hari terakhir' },
    { value: '90', label: '3 bulan terakhir' },
];

const isFilterOpen = ref(false);
const appliedFilter = ref({ status: 'all', period: '30' });
const draftFilter = ref({ ...appliedFilter.value });

const appliedSummary = computed(() => {
    const status = statusOptions.find((option) => option.value === appliedFilter.value.status)?.label;
    const period = periodOptions.find((option) => option.value === appliedFilter.value.period)?.label;

    return `${status} · ${period}`;
});

function openFilter() {
    draftFilter.value = { ...appliedFilter.value };
    isFilterOpen.value = true;
}

function applyFilter() {
    appliedFilter.value = { ...draftFilter.value };
    isFilterOpen.value = false;
}

const bottomCode = `
<script setup>
import Avatar from '@/components/ui/Avatar.vue';
import Button from '@/components/ui/Button.vue';
import Drawer from '@/components/ui/Drawer.vue';
<\/script>

<template>
    <Drawer title="Pilih penerima" description="Geser ke bawah atau tekan Escape untuk menutup.">
        <template #trigger>
            <Button variant="secondary" icon="users">Pilih penerima</Button>
        </template>

        <template #default="{ close }">
            <button v-for="recipient in recipients" :key="recipient.name" type="button" @click="close">
                <Avatar :name="recipient.name" />
                {{ recipient.name }}
            </button>
        </template>
    </Drawer>
</template>
`;

const sideCode = `
<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Drawer from '@/components/ui/Drawer.vue';
import Select from '@/components/ui/Select.vue';

const isFilterOpen = ref(false);
const filter = ref({ status: 'all', period: '30' });
<\/script>

<template>
    <Button variant="secondary" icon="list" @click="isFilterOpen = true">Filter</Button>

    <Drawer v-model:open="isFilterOpen" side="right" title="Filter surat" description="Saring daftar surat keluar.">
        <div class="flex flex-col gap-5">
            <Select v-model="filter.status" label="Status" :options="statusOptions" />
            <Select v-model="filter.period" label="Periode" :options="periodOptions" />
        </div>

        <template #footer="{ close }">
            <Button variant="secondary" @click="close">Batal</Button>
            <Button @click="applyFilter">Terapkan</Button>
        </template>
    </Drawer>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dari bawah"
            description="Default side=&quot;bottom&quot;: panel dengan pegangan yang bisa digeser ke bawah untuk menutup (sentuh atau seret dengan mouse)."
            :code="bottomCode"
        >
            <Drawer title="Pilih penerima" description="Geser ke bawah atau tekan Escape untuk menutup.">
                <template #trigger>
                    <Button variant="secondary" icon="users">Pilih penerima</Button>
                </template>

                <template #default="{ close }">
                    <ul class="flex flex-col gap-1">
                        <li v-for="recipient in recipients" :key="recipient.name">
                            <button
                                type="button"
                                class="flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-left transition-colors hover:bg-ink/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                                @click="close"
                            >
                                <Avatar :name="recipient.name" />
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium">{{ recipient.name }}</span>
                                    <span class="block text-xs text-muted">{{ recipient.company }}</span>
                                </span>
                            </button>
                        </li>
                    </ul>
                </template>
            </Drawer>
        </DocExample>

        <DocExample
            title="Dari kanan + v-model:open"
            description="side=&quot;right&quot; cocok untuk panel filter atau detail; filter baru dipakai setelah Terapkan."
            :code="sideCode"
        >
            <div class="flex flex-col items-center gap-3">
                <Button variant="secondary" icon="list" @click="openFilter">Filter</Button>
                <Badge variant="info">{{ appliedSummary }}</Badge>
            </div>

            <Drawer v-model:open="isFilterOpen" side="right" title="Filter surat" description="Saring daftar surat keluar.">
                <div class="flex flex-col gap-5">
                    <Select v-model="draftFilter.status" label="Status" :options="statusOptions" />
                    <Select v-model="draftFilter.period" label="Periode" :options="periodOptions" />
                </div>

                <template #footer="{ close }">
                    <Button variant="secondary" @click="close">Batal</Button>
                    <Button icon="check" @click="applyFilter">Terapkan</Button>
                </template>
            </Drawer>
        </DocExample>

        <DocProps
            title="Drawer"
            :rows="[
                { name: 'v-model:open', type: 'boolean', default: 'false', description: 'Status terbuka; opsional bila memakai slot #trigger.' },
                { name: 'title', type: 'string', description: 'Judul panel (wajib, dibacakan screen reader).' },
                { name: 'description', type: 'string', description: 'Teks penjelas di bawah judul.' },
                { name: 'side', type: `'bottom' | 'right'`, default: `'bottom'`, description: 'Tepi layar asal panel; arah geser untuk menutup mengikuti sisi ini.' },
                { name: '#trigger', type: 'slot', description: 'Tombol pembuka (opsional).' },
                { name: '#default', type: 'slot { close }', description: 'Isi panel; bisa di-scroll bila panjang.' },
                { name: '#footer', type: 'slot { close }', description: 'Tombol aksi di bagian bawah panel.' },
            ]"
        />
    </DocPage>
</template>
