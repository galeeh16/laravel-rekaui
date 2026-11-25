<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import Collapsible from '@/components/ui/Collapsible.vue';
import Icon from '@/components/ui/Icon.vue';

const dispositions = [
    { from: 'Rektor', to: 'Wakil Rektor II', note: 'Mohon ditindaklanjuti dan dikoordinasikan.', time: '20 Nov 2026, 09.12' },
    { from: 'Wakil Rektor II', to: 'Kabag Umum', note: 'Siapkan ruang rapat dan konsumsi.', time: '20 Nov 2026, 13.40' },
    { from: 'Kabag Umum', to: 'Staf Rumah Tangga', note: 'Selesaikan paling lambat Jumat.', time: '21 Nov 2026, 08.05' },
];

const attachments = [
    { name: 'Surat_Undangan_Rapat.pdf', size: '248 KB' },
    { name: 'Daftar_Peserta.xlsx', size: '36 KB' },
    { name: 'Rundown_Acara.pdf', size: '112 KB' },
    { name: 'Denah_Ruang_Rapat.png', size: '1,2 MB' },
    { name: 'Notulen_Rapat_Sebelumnya.pdf', size: '310 KB' },
];

const showAllAttachments = ref(false);

const basicCode = `
<script setup>
import Collapsible from '@/components/ui/Collapsible.vue';
<\/script>

<template>
    <Collapsible title="Riwayat disposisi" description="3 catatan · terakhir kemarin" icon="clock">
        <ol>
            <li>Rektor → Wakil Rektor II: Mohon ditindaklanjuti...</li>
            <li>Wakil Rektor II → Kabag Umum: Siapkan ruang rapat...</li>
        </ol>
    </Collapsible>
</template>
`;

const plainCode = `
<template>
    <Collapsible variant="plain" title="Detail pengirim" default-open>
        <dl>...</dl>
    </Collapsible>

    <Collapsible variant="plain" title="Catatan internal" disabled>
        ...
    </Collapsible>
</template>
`;

const triggerCode = `
<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Collapsible from '@/components/ui/Collapsible.vue';

const showAll = ref(false);
<\/script>

<template>
    <ul>
        <li>Surat_Undangan_Rapat.pdf</li>
        <li>Daftar_Peserta.xlsx</li>
    </ul>

    <Collapsible v-model:open="showAll" variant="plain">
        <template #trigger="{ open }">
            <Button variant="ghost" size="sm" :icon-trailing="open ? 'chevron-up' : 'chevron-down'">
                {{ open ? 'Sembunyikan' : 'Tampilkan 3 lampiran lainnya' }}
            </Button>
        </template>

        <ul>...</ul>
    </Collapsible>

    <p>Terbuka: {{ showAll ? 'ya' : 'tidak' }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Varian card: panel berbingkai dengan judul, deskripsi, dan ikon."
            :code="basicCode"
        >
            <Collapsible title="Riwayat disposisi" description="3 catatan · terakhir kemarin" icon="clock" class="w-full max-w-lg">
                <ol class="flex flex-col gap-4 border-l border-line pl-4">
                    <li v-for="item in dispositions" :key="item.time" class="flex flex-col gap-0.5 text-sm">
                        <span class="font-medium text-ink">{{ item.from }} → {{ item.to }}</span>
                        <span class="text-muted">{{ item.note }}</span>
                        <span class="text-xs text-muted">{{ item.time }}</span>
                    </li>
                </ol>
            </Collapsible>
        </DocExample>

        <DocExample
            title="Varian plain"
            description="Tanpa bingkai, cocok di dalam kartu atau sidebar. Bisa terbuka sejak awal atau dinonaktifkan."
            :code="plainCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3 rounded-2xl border border-line p-5">
                <Collapsible variant="plain" title="Detail pengirim" default-open>
                    <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1.5 text-sm">
                        <dt class="text-muted">Instansi</dt>
                        <dd class="text-ink">Dinas Pendidikan Provinsi</dd>
                        <dt class="text-muted">Pejabat</dt>
                        <dd class="text-ink">Dr. Hendra Wijaya, M.Pd.</dd>
                        <dt class="text-muted">Diterima</dt>
                        <dd class="text-ink">19 November 2026</dd>
                    </dl>
                </Collapsible>

                <Collapsible variant="plain" title="Catatan internal" disabled>
                    <p class="text-sm text-muted">Hanya untuk sekretariat.</p>
                </Collapsible>
            </div>
        </DocExample>

        <DocExample
            title="Trigger kustom + v-model"
            description="Slot #trigger memakai Button sendiri; status terbuka dikontrol dengan v-model:open."
            :code="triggerCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <ul class="flex flex-col gap-2">
                    <li
                        v-for="file in attachments.slice(0, 2)"
                        :key="file.name"
                        class="flex items-center gap-3 rounded-xl border border-line px-3 py-2.5 text-sm"
                    >
                        <Icon name="file-text" class="size-4.5 text-muted" />
                        <span class="flex-1 truncate text-ink">{{ file.name }}</span>
                        <span class="text-xs text-muted">{{ file.size }}</span>
                    </li>
                </ul>

                <Collapsible v-model:open="showAllAttachments" variant="plain">
                    <template #trigger="{ open }">
                        <Button variant="ghost" size="sm" :icon-trailing="open ? 'chevron-up' : 'chevron-down'" class="self-start">
                            {{ open ? 'Sembunyikan' : `Tampilkan ${attachments.length - 2} lampiran lainnya` }}
                        </Button>
                    </template>

                    <ul class="flex flex-col gap-2">
                        <li
                            v-for="file in attachments.slice(2)"
                            :key="file.name"
                            class="flex items-center gap-3 rounded-xl border border-line px-3 py-2.5 text-sm"
                        >
                            <Icon name="file-text" class="size-4.5 text-muted" />
                            <span class="flex-1 truncate text-ink">{{ file.name }}</span>
                            <span class="text-xs text-muted">{{ file.size }}</span>
                        </li>
                    </ul>
                </Collapsible>

                <p class="text-sm text-muted">Terbuka: {{ showAllAttachments ? 'ya' : 'tidak' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Collapsible"
            :rows="[
                { name: 'v-model:open', type: 'boolean', description: 'Status terbuka/tertutup.' },
                { name: 'default-open', type: 'boolean', default: 'false', description: 'Terbuka sejak awal, tanpa v-model.' },
                { name: 'title', type: 'string', description: 'Judul trigger bawaan; bisa diganti dengan slot #title.' },
                { name: 'description', type: 'string', description: 'Teks kecil di bawah judul.' },
                { name: 'icon', type: 'string', description: 'Nama ikon di depan judul.' },
                { name: 'variant', type: `'card' | 'plain'`, default: `'card'`, description: 'Panel berbingkai atau tanpa bingkai.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Trigger tidak bisa diklik.' },
                { name: 'unmount-on-hide', type: 'boolean', default: 'true', description: 'Hapus konten dari DOM saat tertutup.' },
                { name: '#trigger', type: 'slot { open }', description: 'Ganti trigger bawaan dengan satu elemen fokus (mis. Button).' },
            ]"
        />
    </DocPage>
</template>
