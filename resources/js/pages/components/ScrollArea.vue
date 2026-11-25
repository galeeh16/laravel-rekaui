<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Icon from '@/components/ui/Icon.vue';
import Input from '@/components/ui/Input.vue';
import ScrollArea from '@/components/ui/ScrollArea.vue';

const activityTemplates = [
    { action: 'Surat diterima', actor: 'Sekretariat' },
    { action: 'Nomor agenda diberikan', actor: 'Rina Kartika' },
    { action: 'Diteruskan ke pimpinan', actor: 'Budi Santoso' },
    { action: 'Disposisi ditambahkan', actor: 'Rektor' },
    { action: 'Lampiran diperbarui', actor: 'Siti Rahmawati' },
];

const activities = Array.from({ length: 20 }, (_, index) => ({
    id: index + 1,
    ...activityTemplates[index % activityTemplates.length],
    time: `${String(8 + Math.floor(index / 2)).padStart(2, '0')}.${index % 2 ? '30' : '05'}`,
}));

const attachments = [
    'Surat_Undangan.pdf',
    'Daftar_Hadir.xlsx',
    'Rundown_Acara.pdf',
    'Denah_Ruang.png',
    'Notulen_Rapat.docx',
    'SK_Panitia.pdf',
    'Anggaran_Kegiatan.xlsx',
];

const agencies = [
    'Badan Kepegawaian Daerah',
    'Badan Pendapatan Daerah',
    'Badan Perencanaan Pembangunan Daerah',
    'Dinas Kesehatan',
    'Dinas Komunikasi dan Informatika',
    'Dinas Pekerjaan Umum',
    'Dinas Pendidikan',
    'Dinas Perhubungan',
    'Dinas Sosial',
    'Inspektorat Daerah',
    'Kantor Wilayah Kementerian Agama',
    'Kecamatan Sukajadi',
    'Rumah Sakit Umum Daerah',
    'Satuan Polisi Pamong Praja',
    'Sekretariat DPRD',
];

const agencySearch = ref('');
const filteredAgencies = computed(() =>
    agencies.filter((agency) => agency.toLowerCase().includes(agencySearch.value.toString().toLowerCase())),
);

const verticalCode = `
<script setup>
import ScrollArea from '@/components/ui/ScrollArea.vue';
<\/script>

<template>
    <ScrollArea class="h-72 w-full max-w-sm rounded-2xl border border-line">
        <ul class="divide-y divide-line">
            <li v-for="activity in activities" :key="activity.id" class="px-4 py-3">
                {{ activity.action }}
            </li>
        </ul>
    </ScrollArea>
</template>
`;

const horizontalCode = `
<template>
    <ScrollArea orientation="horizontal" class="w-full max-w-md">
        <div class="flex w-max gap-3 pb-3">
            <div v-for="file in attachments" :key="file" class="w-40 rounded-xl border border-line p-3">
                {{ file }}
            </div>
        </div>
    </ScrollArea>
</template>
`;

const searchCode = `
<script setup>
import { computed, ref } from 'vue';
import Input from '@/components/ui/Input.vue';
import ScrollArea from '@/components/ui/ScrollArea.vue';

const search = ref('');
const filtered = computed(() => agencies.filter((agency) => agency.toLowerCase().includes(search.value.toLowerCase())));
<\/script>

<template>
    <Input v-model="search" type="search" icon="search" placeholder="Cari instansi..." />

    <!-- max-h: daftar pendek menyusut, daftar panjang bisa digulir -->
    <ScrollArea type="always" class="max-h-56 rounded-2xl border border-line">
        <ul>
            <li v-for="agency in filtered" :key="agency">{{ agency }}</li>
        </ul>
    </ScrollArea>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Vertikal"
            description="Tinggi tetap; scrollbar tipis muncul saat kursor berada di atas area."
            :code="verticalCode"
        >
            <ScrollArea class="h-72 w-full max-w-sm rounded-2xl border border-line">
                <div class="px-4 pt-4 pb-1 text-sm font-semibold text-ink">Riwayat aktivitas surat</div>
                <ul class="divide-y divide-line">
                    <li v-for="activity in activities" :key="activity.id" class="flex items-center gap-3 px-4 py-3 text-sm">
                        <span class="w-10 shrink-0 text-xs text-muted tabular-nums">{{ activity.time }}</span>
                        <span class="flex min-w-0 flex-col">
                            <span class="truncate text-ink">{{ activity.action }}</span>
                            <span class="truncate text-xs text-muted">{{ activity.actor }}</span>
                        </span>
                    </li>
                </ul>
            </ScrollArea>
        </DocExample>

        <DocExample
            title="Horizontal"
            description="Untuk deretan kartu lampiran; isi perlu lebar sendiri (mis. w-max)."
            :code="horizontalCode"
        >
            <ScrollArea orientation="horizontal" class="w-full max-w-md">
                <div class="flex w-max gap-3 pb-3">
                    <div
                        v-for="file in attachments"
                        :key="file"
                        class="flex w-40 flex-col gap-3 rounded-xl border border-line bg-surface p-3 text-sm"
                    >
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-soft text-brand-ink">
                            <Icon name="file-text" class="size-4.5" />
                        </span>
                        <span class="truncate font-medium text-ink">{{ file }}</span>
                    </div>
                </div>
            </ScrollArea>
        </DocExample>

        <DocExample
            title="Daftar dengan pencarian"
            description="Dengan max-height dan type=&quot;always&quot;: daftar menyusut saat hasil pencarian sedikit."
            :code="searchCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <Input v-model="agencySearch" type="search" icon="search" placeholder="Cari instansi tujuan..." />

                <ScrollArea type="always" class="max-h-56 rounded-2xl border border-line">
                    <ul class="flex flex-col p-1.5">
                        <li
                            v-for="agency in filteredAgencies"
                            :key="agency"
                            class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-ink hover:bg-ink/5"
                        >
                            <Icon name="mail" class="size-4 text-muted" />
                            {{ agency }}
                        </li>
                        <li v-if="filteredAgencies.length === 0" class="px-2.5 py-6 text-center text-sm text-muted">
                            Instansi tidak ditemukan.
                        </li>
                    </ul>
                </ScrollArea>

                <p class="text-sm text-muted">{{ filteredAgencies.length }} dari {{ agencies.length }} instansi</p>
            </div>
        </DocExample>

        <DocProps
            title="ScrollArea"
            :rows="[
                { name: 'orientation', type: `'vertical' | 'horizontal' | 'both'`, default: `'vertical'`, description: 'Arah scrollbar yang ditampilkan.' },
                { name: 'type', type: `'hover' | 'auto' | 'always' | 'scroll' | 'glimpse'`, default: `'hover'`, description: 'Kapan scrollbar terlihat.' },
                { name: 'scroll-hide-delay', type: 'number', default: '600', description: 'Jeda (ms) sebelum scrollbar disembunyikan.' },
                { name: 'class', type: 'string', description: 'Atur tinggi (h-*) atau tinggi maksimum (max-h-*) di sini.' },
            ]"
        />
    </DocPage>
</template>
