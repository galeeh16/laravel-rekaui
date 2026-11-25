<script setup>
import { ref, useTemplateRef } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import Icon from '@/components/ui/Icon.vue';
import Splitter from '@/components/ui/Splitter.vue';
import SplitterHandle from '@/components/ui/SplitterHandle.vue';
import SplitterPanel from '@/components/ui/SplitterPanel.vue';

const letters = [
    { subject: 'Undangan Rapat Koordinasi', sender: 'Dinas Pendidikan', time: '09.12' },
    { subject: 'Permohonan Data Pegawai', sender: 'BKD Provinsi', time: '08.40' },
    { subject: 'Laporan Realisasi Anggaran', sender: 'Bagian Keuangan', time: 'Kemarin' },
    { subject: 'Nota Dinas Cuti Bersama', sender: 'Biro Umum', time: 'Kemarin' },
];

const folders = [
    { label: 'Kotak masuk', icon: 'inbox', count: 12 },
    { label: 'Terkirim', icon: 'send', count: 48 },
    { label: 'Draf', icon: 'pen-line', count: 3 },
    { label: 'Arsip', icon: 'archive', count: 214 },
];

const sidebarPanel = useTemplateRef('sidebarPanel');
const sidebarSize = ref(30);
const isSidebarCollapsed = ref(false);

function toggleSidebar() {
    if (isSidebarCollapsed.value) {
        sidebarPanel.value?.expand();
    } else {
        sidebarPanel.value?.collapse();
    }
}

const horizontalCode = `
<script setup>
import Splitter from '@/components/ui/Splitter.vue';
import SplitterHandle from '@/components/ui/SplitterHandle.vue';
import SplitterPanel from '@/components/ui/SplitterPanel.vue';
<\/script>

<template>
    <Splitter class="h-72 rounded-2xl border border-line">
        <SplitterPanel :default-size="40" :min-size="25">
            ...daftar surat...
        </SplitterPanel>
        <SplitterHandle />
        <SplitterPanel :default-size="60" :min-size="30">
            ...pratinjau surat...
        </SplitterPanel>
    </Splitter>
</template>
`;

const nestedCode = `
<template>
    <Splitter class="h-80 rounded-2xl border border-line">
        <SplitterPanel :default-size="28" :min-size="20">...folder...</SplitterPanel>
        <SplitterHandle />
        <SplitterPanel :default-size="72">
            <!-- Splitter bertingkat: isi panel induk dengan h-full -->
            <Splitter direction="vertical" class="h-full">
                <SplitterPanel :default-size="65" :min-size="30">...isi surat...</SplitterPanel>
                <SplitterHandle />
                <SplitterPanel :default-size="35" :min-size="20">...catatan disposisi...</SplitterPanel>
            </Splitter>
        </SplitterPanel>
    </Splitter>
</template>
`;

const collapsibleCode = `
<script setup>
import { ref, useTemplateRef } from 'vue';

const sidebar = useTemplateRef('sidebar');
const sidebarSize = ref(30);
const isCollapsed = ref(false);

function toggleSidebar() {
    isCollapsed.value ? sidebar.value.expand() : sidebar.value.collapse();
}
<\/script>

<template>
    <Splitter class="h-64 rounded-2xl border border-line">
        <SplitterPanel
            ref="sidebar"
            :default-size="30"
            :min-size="20"
            collapsible
            :collapsed-size="0"
            @resize="(size) => (sidebarSize = size)"
            @collapse="isCollapsed = true"
            @expand="isCollapsed = false"
        >
            ...folder...
        </SplitterPanel>
        <SplitterHandle />
        <SplitterPanel :default-size="70">
            <Button @click="toggleSidebar">{{ isCollapsed ? 'Tampilkan folder' : 'Sembunyikan folder' }}</Button>
            <p>Lebar panel folder: {{ Math.round(sidebarSize) }}%</p>
        </SplitterPanel>
    </Splitter>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Horizontal"
            description="Geser garis pemisah dengan mouse, atau fokuskan lalu tekan tombol panah."
            :code="horizontalCode"
        >
            <Splitter class="h-72 w-full rounded-2xl border border-line">
                <SplitterPanel :default-size="40" :min-size="25">
                    <ul class="flex h-full flex-col gap-1 overflow-y-auto p-2">
                        <li
                            v-for="(letter, index) in letters"
                            :key="letter.subject"
                            :class="[
                                'flex flex-col gap-0.5 rounded-xl px-3 py-2.5 text-sm',
                                index === 0 ? 'bg-brand-soft text-brand-ink' : 'text-ink hover:bg-ink/5',
                            ]"
                        >
                            <span class="flex items-center justify-between gap-2">
                                <span class="truncate font-medium">{{ letter.sender }}</span>
                                <span class="shrink-0 text-xs opacity-70">{{ letter.time }}</span>
                            </span>
                            <span class="truncate opacity-80">{{ letter.subject }}</span>
                        </li>
                    </ul>
                </SplitterPanel>

                <SplitterHandle />

                <SplitterPanel :default-size="60" :min-size="30">
                    <article class="flex h-full flex-col gap-3 overflow-y-auto p-5 text-sm">
                        <h3 class="text-base font-semibold text-ink">Undangan Rapat Koordinasi</h3>
                        <p class="text-xs text-muted">Dinas Pendidikan · 045/UM/XI/2026</p>
                        <p class="leading-relaxed text-ink/80">
                            Dengan hormat, kami mengundang Bapak/Ibu untuk menghadiri rapat koordinasi akhir tahun pada
                            Jumat, 28 November 2026 pukul 09.00 WIB bertempat di Aula Utama.
                        </p>
                    </article>
                </SplitterPanel>
            </Splitter>
        </DocExample>

        <DocExample
            title="Vertikal & bertingkat"
            description="Splitter vertikal di dalam panel kanan: isi surat di atas, catatan disposisi di bawah."
            :code="nestedCode"
        >
            <Splitter class="h-80 w-full rounded-2xl border border-line">
                <SplitterPanel :default-size="28" :min-size="20">
                    <nav class="flex h-full flex-col gap-1 p-2" aria-label="Folder surat">
                        <span
                            v-for="folder in folders"
                            :key="folder.label"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm text-ink hover:bg-ink/5"
                        >
                            <Icon :name="folder.icon" class="size-4 text-muted" />
                            <span class="flex-1 truncate">{{ folder.label }}</span>
                            <span class="text-xs text-muted tabular-nums">{{ folder.count }}</span>
                        </span>
                    </nav>
                </SplitterPanel>

                <SplitterHandle />

                <SplitterPanel :default-size="72">
                    <Splitter direction="vertical" class="h-full">
                        <SplitterPanel :default-size="65" :min-size="30">
                            <div class="flex h-full flex-col gap-2 overflow-y-auto p-5 text-sm">
                                <h3 class="font-semibold text-ink">Isi surat</h3>
                                <p class="leading-relaxed text-ink/80">
                                    Sehubungan dengan evaluasi program kerja tahun 2026, dimohon kehadiran seluruh kepala
                                    bagian beserta satu orang staf pendamping.
                                </p>
                            </div>
                        </SplitterPanel>

                        <SplitterHandle />

                        <SplitterPanel :default-size="35" :min-size="20">
                            <div class="flex h-full flex-col gap-2 overflow-y-auto bg-canvas/60 p-5 text-sm">
                                <h3 class="font-semibold text-ink">Catatan disposisi</h3>
                                <p class="text-ink/80">Kabag Umum: siapkan ruang rapat dan konsumsi untuk 40 orang.</p>
                            </div>
                        </SplitterPanel>
                    </Splitter>
                </SplitterPanel>
            </Splitter>
        </DocExample>

        <DocExample
            title="Panel dapat diciutkan"
            description="Panel kiri menciut jika digeser di bawah min-size, atau lewat tombol (template ref). Ukuran dibaca dari event resize."
            :code="collapsibleCode"
        >
            <Splitter class="h-64 w-full rounded-2xl border border-line">
                <SplitterPanel
                    ref="sidebarPanel"
                    :default-size="30"
                    :min-size="20"
                    collapsible
                    :collapsed-size="0"
                    @resize="(size) => (sidebarSize = size)"
                    @collapse="isSidebarCollapsed = true"
                    @expand="isSidebarCollapsed = false"
                >
                    <nav class="flex h-full flex-col gap-1 p-2" aria-label="Folder surat">
                        <span
                            v-for="folder in folders"
                            :key="folder.label"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm whitespace-nowrap text-ink hover:bg-ink/5"
                        >
                            <Icon :name="folder.icon" class="size-4 text-muted" />
                            {{ folder.label }}
                        </span>
                    </nav>
                </SplitterPanel>

                <SplitterHandle />

                <SplitterPanel :default-size="70">
                    <div class="flex h-full flex-col items-start gap-3 p-5 text-sm">
                        <Button variant="secondary" size="sm" icon="panel-left" @click="toggleSidebar">
                            {{ isSidebarCollapsed ? 'Tampilkan folder' : 'Sembunyikan folder' }}
                        </Button>
                        <p class="text-muted">
                            Lebar panel folder: <span class="font-medium text-ink tabular-nums">{{ Math.round(sidebarSize) }}%</span>
                        </p>
                    </div>
                </SplitterPanel>
            </Splitter>
        </DocExample>

        <DocProps
            title="Splitter"
            :rows="[
                { name: 'direction', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Arah susunan panel.' },
                { name: 'auto-save-id', type: 'string', description: 'Simpan ukuran panel di localStorage dengan kunci ini.' },
                { name: 'keyboard-resize-by', type: 'number', default: '10', description: 'Persen perubahan per tombol panah.' },
                { name: 'class', type: 'string', description: 'Dipasang di kotak luar; atur tinggi di sini (h-full untuk bertingkat).' },
                { name: '@layout', type: '(sizes: number[]) => void', description: 'Ukuran semua panel setelah diubah.' },
            ]"
        />

        <DocProps
            title="SplitterPanel"
            :rows="[
                { name: 'default-size', type: 'number', description: 'Ukuran awal (persen).' },
                { name: 'min-size', type: 'number', default: '10', description: 'Ukuran minimum.' },
                { name: 'max-size', type: 'number', default: '100', description: 'Ukuran maksimum.' },
                { name: 'collapsible', type: 'boolean', default: 'false', description: 'Bisa menciut ke collapsed-size.' },
                { name: 'collapsed-size', type: 'number', default: '0', description: 'Ukuran saat menciut.' },
                { name: 'size-unit', type: `'%' | 'px'`, default: `'%'`, description: 'Satuan semua prop ukuran.' },
                { name: '@resize / @collapse / @expand', type: 'event', description: 'Ukuran berubah, panel menciut, panel dibuka.' },
                { name: 'ref', type: '{ collapse, expand, resize, isCollapsed }', description: 'Kontrol panel dari luar lewat template ref.' },
            ]"
        />

        <DocProps
            title="SplitterHandle"
            :rows="[
                { name: 'with-grip', type: 'boolean', default: 'true', description: 'Tampilkan pegangan grip di tengah garis.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan pengubahan ukuran.' },
            ]"
        />
    </DocPage>
</template>
