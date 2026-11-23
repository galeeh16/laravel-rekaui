<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

/* Example 1: trigger slot */
const lastAction = ref(null);

/* Example 2: one dialog for a whole list, controlled with v-model:open */
const initialDrafts = [
    { number: '015/SK/XI/2026', subject: 'Pengajuan anggaran kegiatan', recipient: 'Koperasi Maju Bersama' },
    { number: '014/SK/XI/2026', subject: 'Undangan rapat anggota tahunan', recipient: 'PT Sinar Mandiri' },
    { number: '013/SK/XI/2026', subject: 'Penawaran kerja sama', recipient: 'CV Arunika Desain' },
];

const drafts = ref([...initialDrafts]);
const draftToDelete = ref(null);
const isDeleteDialogOpen = ref(false);

const deleteDescription = computed(() =>
    draftToDelete.value ? `Draf ${draftToDelete.value.number} akan dihapus permanen dan tidak bisa dikembalikan.` : null,
);

function askToDelete(draft) {
    draftToDelete.value = draft;
    isDeleteDialogOpen.value = true;
}

function deleteDraft() {
    drafts.value = drafts.value.filter((draft) => draft.number !== draftToDelete.value.number);
}

/* Example 3: keep the dialog open while a (simulated) request runs */
const isArchiveDialogOpen = ref(false);
const isArchiving = ref(false);
const archivedAt = ref(null);
let archiveTimeout = null;

function archiveSentLetters() {
    isArchiving.value = true;

    archiveTimeout = setTimeout(() => {
        isArchiving.value = false;
        isArchiveDialogOpen.value = false;
        archivedAt.value = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }, 1500);
}

onBeforeUnmount(() => clearTimeout(archiveTimeout));

const basicCode = `
<script setup>
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';
<\/script>

<template>
    <AlertDialog
        title="Kirim surat sekarang?"
        description="Surat 015/SK/XI/2026 akan dikirim ke Koperasi Maju Bersama dan tidak bisa diubah lagi."
        icon="send"
        confirm-label="Kirim"
        @confirm="send"
    >
        <template #trigger>
            <Button icon="send">Kirim surat</Button>
        </template>
    </AlertDialog>

    <AlertDialog
        variant="danger"
        title="Buang draf ini?"
        description="Semua perubahan pada draf akan hilang."
        confirm-label="Buang draf"
        @confirm="discard"
    >
        <template #trigger>
            <Button variant="secondary" icon="trash">Buang draf</Button>
        </template>
    </AlertDialog>
</template>
`;

const listCode = `
<script setup>
import { computed, ref } from 'vue';
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';

const drafts = ref([...]);
const draftToDelete = ref(null);
const isDeleteDialogOpen = ref(false);

const deleteDescription = computed(() =>
    draftToDelete.value ? 'Draf ' + draftToDelete.value.number + ' akan dihapus permanen.' : null,
);

function askToDelete(draft) {
    draftToDelete.value = draft;
    isDeleteDialogOpen.value = true;
}

function deleteDraft() {
    drafts.value = drafts.value.filter((draft) => draft.number !== draftToDelete.value.number);
}
<\/script>

<template>
    <ul>
        <li v-for="draft in drafts" :key="draft.number">
            {{ draft.subject }}
            <Button variant="ghost" size="icon-sm" icon="trash" aria-label="Hapus draf" @click="askToDelete(draft)" />
        </li>
    </ul>

    <!-- One dialog for the whole list -->
    <AlertDialog
        v-model:open="isDeleteDialogOpen"
        variant="danger"
        icon="trash"
        title="Hapus draf surat?"
        :description="deleteDescription"
        confirm-label="Hapus"
        @confirm="deleteDraft"
    >
        <p>{{ draftToDelete?.subject }} — {{ draftToDelete?.recipient }}</p>
    </AlertDialog>
</template>
`;

const processingCode = `
<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';

const isOpen = ref(false);
const isArchiving = ref(false);

function archiveSentLetters() {
    router.post('/letters/archive', {}, {
        onStart: () => (isArchiving.value = true),
        onSuccess: () => (isOpen.value = false),
        onFinish: () => (isArchiving.value = false),
    });
}
<\/script>

<template>
    <AlertDialog
        v-model:open="isOpen"
        icon="archive"
        title="Arsipkan semua surat terkirim?"
        description="12 surat berstatus Terkirim akan dipindahkan ke arsip."
        confirm-label="Arsipkan"
        :close-on-confirm="false"
        :processing="isArchiving"
        @confirm="archiveSentLetters"
    >
        <template #trigger>
            <Button variant="secondary" icon="archive">Arsipkan surat terkirim</Button>
        </template>
    </AlertDialog>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Tombol di slot #trigger membuka dialog. Dialog hanya bisa ditutup lewat tombol atau Escape, tidak dengan klik di luar."
            :code="basicCode"
        >
            <div class="flex flex-col items-center gap-4">
                <div class="flex flex-wrap justify-center gap-3">
                    <AlertDialog
                        title="Kirim surat sekarang?"
                        description="Surat 015/SK/XI/2026 akan dikirim ke Koperasi Maju Bersama dan tidak bisa diubah lagi."
                        icon="send"
                        confirm-label="Kirim"
                        @confirm="lastAction = 'Surat 015/SK/XI/2026 dikirim.'"
                    >
                        <template #trigger>
                            <Button icon="send">Kirim surat</Button>
                        </template>
                    </AlertDialog>

                    <AlertDialog
                        variant="danger"
                        title="Buang draf ini?"
                        description="Semua perubahan pada draf akan hilang."
                        confirm-label="Buang draf"
                        @confirm="lastAction = 'Draf dibuang.'"
                    >
                        <template #trigger>
                            <Button variant="secondary" icon="trash">Buang draf</Button>
                        </template>
                    </AlertDialog>
                </div>

                <p class="text-sm text-muted">{{ lastAction ?? 'Belum ada aksi yang dikonfirmasi.' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Satu dialog untuk daftar"
            description="Dikontrol dengan v-model:open; isi slot default menampilkan ringkasan surat yang dipilih."
            :code="listCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <ul class="divide-y divide-line overflow-hidden rounded-xl border border-line">
                    <li v-for="draft in drafts" :key="draft.number" class="flex items-center gap-3 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ draft.subject }}</p>
                            <p class="truncate text-xs text-muted">{{ draft.number }} · {{ draft.recipient }}</p>
                        </div>
                        <Badge>Draf</Badge>
                        <Button
                            variant="ghost"
                            size="icon-sm"
                            icon="trash"
                            :aria-label="`Hapus draf ${draft.number}`"
                            @click="askToDelete(draft)"
                        />
                    </li>

                    <li v-if="drafts.length === 0" class="px-4 py-8 text-center text-sm text-muted">Semua draf sudah dihapus.</li>
                </ul>

                <Button v-if="drafts.length < initialDrafts.length" variant="ghost" size="sm" class="self-start" @click="drafts = [...initialDrafts]">
                    Kembalikan contoh
                </Button>

                <AlertDialog
                    v-model:open="isDeleteDialogOpen"
                    variant="danger"
                    icon="trash"
                    title="Hapus draf surat?"
                    :description="deleteDescription"
                    confirm-label="Hapus"
                    @confirm="deleteDraft"
                >
                    <div v-if="draftToDelete" class="rounded-xl border border-line bg-canvas px-4 py-3">
                        <p class="font-medium">{{ draftToDelete.subject }}</p>
                        <p class="text-muted">{{ draftToDelete.recipient }}</p>
                    </div>
                </AlertDialog>
            </div>
        </DocExample>

        <DocExample
            title="Menunggu proses"
            description="Dengan :close-on-confirm=&quot;false&quot; dan :processing, dialog tetap terbuka (Escape dinonaktifkan) sampai request selesai."
            :code="processingCode"
        >
            <div class="flex flex-col items-center gap-4">
                <AlertDialog
                    v-model:open="isArchiveDialogOpen"
                    icon="archive"
                    title="Arsipkan semua surat terkirim?"
                    description="12 surat berstatus Terkirim akan dipindahkan ke arsip."
                    confirm-label="Arsipkan"
                    :close-on-confirm="false"
                    :processing="isArchiving"
                    @confirm="archiveSentLetters"
                >
                    <template #trigger>
                        <Button variant="secondary" icon="archive">Arsipkan surat terkirim</Button>
                    </template>
                </AlertDialog>

                <p class="text-sm text-muted">
                    {{ archivedAt ? `Diarsipkan pukul ${archivedAt}.` : 'Simulasi request selama 1,5 detik.' }}
                </p>
            </div>
        </DocExample>

        <DocProps
            title="AlertDialog"
            :rows="[
                { name: 'v-model:open', type: 'boolean', default: 'false', description: 'Status terbuka; opsional bila memakai slot #trigger.' },
                { name: 'title', type: 'string', description: 'Pertanyaan konfirmasi (wajib).' },
                { name: 'description', type: 'string', description: 'Penjelasan akibat dari aksi.' },
                { name: 'variant', type: `'default' | 'danger'`, default: `'default'`, description: 'Danger memakai ikon dan tombol konfirmasi merah.' },
                { name: 'icon', type: 'string', default: `'circle-help'`, description: 'Ikon di lingkaran; default triangle-alert untuk danger.' },
                { name: 'confirm-label', type: 'string', default: `'Lanjutkan'`, description: 'Teks tombol konfirmasi.' },
                { name: 'cancel-label', type: 'string', default: `'Batal'`, description: 'Teks tombol batal (fokus awal ada di tombol ini).' },
                { name: 'processing', type: 'boolean', default: 'false', description: 'Tampilkan spinner, nonaktifkan tombol, dan cegah Escape.' },
                { name: 'close-on-confirm', type: 'boolean', default: 'true', description: 'Tutup otomatis setelah konfirmasi; false untuk menutup sendiri setelah request.' },
                { name: '@confirm', type: 'event', description: 'Dipanggil saat tombol konfirmasi diklik.' },
                { name: '#trigger', type: 'slot', description: 'Tombol pembuka (opsional).' },
                { name: '#default', type: 'slot', description: 'Konten tambahan di bawah deskripsi.' },
            ]"
        />
    </DocPage>
</template>
