<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import Tree from '@/components/ui/Tree.vue';

const archive = [
    {
        id: 'surat-masuk',
        label: 'Surat Masuk',
        children: [
            {
                id: 'surat-masuk-2026',
                label: '2026',
                children: [
                    { id: 'sm-045', label: '045-UM-XI-2026 Undangan Rapat.pdf', icon: 'file-text' },
                    { id: 'sm-046', label: '046-KP-XI-2026 Permohonan Data.pdf', icon: 'file-text' },
                    { id: 'sm-047', label: 'Lampiran Denah Ruang.png', icon: 'image' },
                ],
            },
            {
                id: 'surat-masuk-2025',
                label: '2025',
                children: [{ id: 'sm-210', label: '210-UM-XII-2025 Laporan Akhir.pdf', icon: 'file-text' }],
            },
        ],
    },
    {
        id: 'surat-keluar',
        label: 'Surat Keluar',
        children: [
            { id: 'sk-112', label: '112-KP-XI-2026 Surat Keputusan.docx', icon: 'file-text' },
            { id: 'sk-113', label: '113-ND-XI-2026 Nota Dinas.docx', icon: 'file-text' },
        ],
    },
    {
        id: 'template',
        label: 'Template',
        children: [
            { id: 'tpl-kop', label: 'Kop Surat Resmi.docx', icon: 'file-text' },
            { id: 'tpl-ttd', label: 'Tanda Tangan Elektronik.png', icon: 'image' },
        ],
    },
    { id: 'catatan', label: 'Catatan Rapat.txt' },
];

/** Keys of every folder, for the "open all" button. */
function collectFolderKeys(items) {
    return items.flatMap((item) => (item.children ? [item.id, ...collectFolderKeys(item.children)] : []));
}

const allFolderKeys = collectFolderKeys(archive);

const selectedFile = ref();
const checkedFiles = ref([]);
const openFolders = ref(['surat-masuk']);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Tree from '@/components/ui/Tree.vue';

const archive = [
    {
        id: 'surat-masuk',
        label: 'Surat Masuk',
        children: [
            { id: 'sm-045', label: '045-UM-XI-2026 Undangan Rapat.pdf', icon: 'file-text' },
        ],
    },
    { id: 'catatan', label: 'Catatan Rapat.txt' },
];

const selected = ref();
<\/script>

<template>
    <Tree v-model="selected" :items="archive" :default-expanded="['surat-masuk']" />

    <p>Dipilih: {{ selected?.label ?? '—' }}</p>
</template>
`;

const multipleCode = `
<script setup>
import { ref } from 'vue';

const checked = ref([]);
<\/script>

<template>
    <!-- Memilih folder ikut memilih isinya; folder terpilih jika semua isinya terpilih -->
    <Tree v-model="checked" :items="archive" multiple propagate-select bubble-select />

    <p>{{ checked.filter((item) => !item.children).length }} berkas dipilih</p>
</template>
`;

const expandedCode = `
<script setup>
import { ref } from 'vue';

const openFolders = ref(['surat-masuk']);
<\/script>

<template>
    <Button variant="secondary" size="sm" @click="openFolders = ['surat-masuk', 'surat-masuk-2026', ...]">Buka semua</Button>
    <Button variant="ghost" size="sm" @click="openFolders = []">Tutup semua</Button>

    <Tree v-model:expanded="openFolders" :items="archive" />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar + v-model"
            description="Klik atau Enter untuk memilih; panah kanan/kiri membuka dan menutup folder."
            :code="basicCode"
            preview-class="flex min-h-56 flex-col items-center justify-center gap-4 p-6 sm:p-10"
        >
            <Tree
                v-model="selectedFile"
                :items="archive"
                :default-expanded="['surat-masuk', 'surat-masuk-2026']"
                class="w-full max-w-sm rounded-2xl border border-line p-2"
            />
            <p class="text-sm text-muted">Dipilih: {{ selectedFile?.label ?? '—' }}</p>
        </DocExample>

        <DocExample
            title="Banyak pilihan"
            description="Dengan multiple, propagate-select, dan bubble-select; folder menampilkan status sebagian."
            :code="multipleCode"
            preview-class="flex min-h-56 flex-col items-center justify-center gap-4 p-6 sm:p-10"
        >
            <Tree
                v-model="checkedFiles"
                :items="archive"
                :default-expanded="['surat-keluar', 'template']"
                multiple
                propagate-select
                bubble-select
                class="w-full max-w-sm rounded-2xl border border-line p-2"
            />
            <p class="text-sm text-muted">{{ checkedFiles.filter((item) => !item.children).length }} berkas dipilih untuk diekspor</p>
        </DocExample>

        <DocExample
            title="Kontrol folder terbuka"
            description="v-model:expanded berisi key folder yang terbuka, jadi bisa diatur dari luar."
            :code="expandedCode"
            preview-class="flex min-h-56 flex-col items-center justify-center gap-4 p-6 sm:p-10"
        >
            <div class="flex gap-2">
                <Button variant="secondary" size="sm" icon="folder-open" @click="openFolders = [...allFolderKeys]">Buka semua</Button>
                <Button variant="ghost" size="sm" icon="folder" @click="openFolders = []">Tutup semua</Button>
            </div>
            <Tree v-model:expanded="openFolders" :items="archive" class="w-full max-w-sm rounded-2xl border border-line p-2" />
            <p class="text-sm text-muted">Terbuka: {{ openFolders.join(', ') || '—' }}</p>
        </DocExample>

        <DocProps
            title="Tree"
            :rows="[
                { name: 'v-model', type: 'object | object[]', description: 'Item terpilih (salinan item); array jika multiple.' },
                { name: 'v-model:expanded', type: 'string[]', description: 'Key folder yang sedang terbuka.' },
                { name: 'items', type: 'Array<{ id, label, icon?, children? }>', description: 'Data pohon; item dengan children menjadi folder (wajib).' },
                { name: 'get-key', type: '(item) => string', default: 'item => item.id', description: 'Key unik setiap item.' },
                { name: 'default-expanded', type: 'string[]', description: 'Folder yang terbuka awalnya, tanpa v-model:expanded.' },
                { name: 'multiple', type: 'boolean', default: 'false', description: 'Boleh memilih banyak item (dengan kotak centang).' },
                { name: 'propagate-select', type: 'boolean', default: 'false', description: 'Multiple: memilih folder ikut memilih isinya.' },
                { name: 'bubble-select', type: 'boolean', default: 'false', description: 'Multiple: folder terpilih jika semua isinya terpilih.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan seluruh pohon.' },
                { name: '#item', type: 'slot { item, isExpanded, isSelected }', description: 'Ganti label bawaan setiap baris.' },
            ]"
        />
    </DocPage>
</template>
