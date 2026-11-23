<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Switch from '@/components/ui/Switch.vue';
import Toaster from '@/components/ui/Toaster.vue';
import { useToast } from '@/composables/useToast';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const { toast, promise } = useToast();

const variantExamples = [
    {
        variant: 'success',
        label: 'Sukses',
        icon: 'circle-check',
        title: 'Surat berhasil dikirim',
        description: 'Surat 045/UM/XI/2026 terkirim ke Dinas Pendidikan.',
    },
    {
        variant: 'danger',
        label: 'Gagal',
        icon: 'triangle-alert',
        title: 'Gagal menyimpan draf',
        description: 'Koneksi terputus. Coba lagi beberapa saat lagi.',
    },
    {
        variant: 'info',
        label: 'Info',
        icon: 'info',
        title: 'Disposisi baru',
        description: 'Kabag Umum meneruskan 1 surat kepada Anda.',
    },
    {
        variant: 'warning',
        label: 'Peringatan',
        icon: 'clock',
        title: 'Batas waktu hampir habis',
        description: 'Surat 112/KP/XI/2026 harus dibalas besok.',
    },
];

const isArchived = ref(false);

function archiveLetter() {
    isArchived.value = true;

    toast({
        title: 'Surat dipindahkan ke arsip',
        description: 'Undangan Rapat Koordinasi',
        action: {
            label: 'Batalkan',
            onClick: () => {
                isArchived.value = false;
                toast({ title: 'Surat dikembalikan ke kotak masuk', variant: 'success' });
            },
        },
    });
}

const shouldFail = ref(false);

/** Simulated request that settles after 1.5 s. */
function sendLetter() {
    const willFail = shouldFail.value;
    const request = new Promise((resolve, reject) => {
        setTimeout(() => (willFail ? reject(new Error('Server tidak merespons.')) : resolve('045/UM/XI/2026')), 1500);
    });

    promise(request, {
        loading: { title: 'Mengirim surat…', description: 'Mohon tunggu sebentar.' },
        success: (number) => ({ title: 'Surat terkirim', description: `Nomor ${number} sudah diterima tujuan.` }),
        error: (error) => ({ title: 'Gagal mengirim surat', description: error.message }),
    }).catch(() => {
        // The error toast already informs the user.
    });
}

const variantsCode = `
<script setup>
import Button from '@/components/ui/Button.vue';
import { useToast } from '@/composables/useToast';

const { toast } = useToast();
<\/script>

<template>
    <!-- <Toaster /> dipasang sekali, mis. di layout -->
    <Button
        variant="secondary"
        @click="toast({ title: 'Surat berhasil dikirim', description: 'Surat 045/UM/XI/2026 terkirim.', variant: 'success' })"
    >
        Sukses
    </Button>
    <Button variant="secondary" @click="toast({ title: 'Gagal menyimpan draf', variant: 'danger' })">Gagal</Button>
    <Button variant="secondary" @click="toast({ title: 'Disposisi baru', variant: 'info' })">Info</Button>
    <Button variant="secondary" @click="toast({ title: 'Batas waktu hampir habis', variant: 'warning' })">Peringatan</Button>
</template>
`;

const actionCode = `
<script setup>
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

const { toast } = useToast();
const isArchived = ref(false);

function archiveLetter() {
    isArchived.value = true;

    toast({
        title: 'Surat dipindahkan ke arsip',
        description: 'Undangan Rapat Koordinasi',
        action: {
            label: 'Batalkan',
            onClick: () => (isArchived.value = false),
        },
    });
}
<\/script>

<template>
    <Badge :variant="isArchived ? 'neutral' : 'info'">{{ isArchived ? 'Diarsipkan' : 'Kotak masuk' }}</Badge>
    <Button icon="archive" :disabled="isArchived" @click="archiveLetter">Arsipkan</Button>
</template>
`;

const promiseCode = `
<script setup>
import { useToast } from '@/composables/useToast';

const { promise } = useToast();

function sendLetter() {
    promise(axios.post('/surat/045/kirim'), {
        loading: { title: 'Mengirim surat…', description: 'Mohon tunggu sebentar.' },
        success: () => ({ title: 'Surat terkirim', description: 'Sudah diterima tujuan.' }),
        error: (error) => ({ title: 'Gagal mengirim surat', description: error.message }),
    }).catch(() => {});
}
<\/script>
`;
</script>

<template>
    <DocPage>
        <Toaster />

        <DocExample
            title="Varian"
            description="Panggil toast() dari mana saja; notifikasi muncul di kanan bawah dan bisa digeser ke kanan untuk ditutup."
            :code="variantsCode"
        >
            <div class="flex flex-wrap justify-center gap-2">
                <Button
                    v-for="example in variantExamples"
                    :key="example.variant"
                    variant="secondary"
                    :icon="example.icon"
                    @click="toast({ title: example.title, description: example.description, variant: example.variant })"
                >
                    {{ example.label }}
                </Button>
            </div>
        </DocExample>

        <DocExample
            title="Dengan aksi"
            description="Tombol aksi di dalam toast, mis. untuk membatalkan tindakan; state halaman ikut berubah."
            :code="actionCode"
        >
            <div class="flex w-full max-w-md items-center justify-between gap-4 rounded-2xl border border-line p-4">
                <div class="flex min-w-0 flex-col gap-1">
                    <span class="truncate text-sm font-medium text-ink">Undangan Rapat Koordinasi</span>
                    <Badge :variant="isArchived ? 'neutral' : 'info'" class="self-start">
                        {{ isArchived ? 'Diarsipkan' : 'Kotak masuk' }}
                    </Badge>
                </div>
                <Button variant="secondary" size="sm" icon="archive" :disabled="isArchived" @click="archiveLetter">Arsipkan</Button>
            </div>
        </DocExample>

        <DocExample
            title="Proses (promise)"
            description="Toast memuat berubah menjadi sukses atau gagal saat promise selesai."
            :code="promiseCode"
        >
            <div class="flex flex-col items-center gap-4">
                <label class="flex items-center gap-3 text-sm text-ink">
                    <Switch v-model="shouldFail" />
                    Simulasikan gagal
                </label>
                <Button icon="send" @click="sendLetter">Kirim surat</Button>
            </div>
        </DocExample>

        <DocProps
            title="toast(options)"
            :rows="[
                { name: 'title', type: 'string', description: 'Judul notifikasi (wajib).' },
                { name: 'description', type: 'string', description: 'Teks pendukung di bawah judul.' },
                { name: 'variant', type: `'success' | 'danger' | 'info' | 'warning'`, default: `'info'`, description: 'Warna dan ikon.' },
                { name: 'duration', type: 'number', description: 'Durasi tampil (ms); default dari Toaster.' },
                { name: 'action', type: '{ label, onClick, altText? }', description: 'Tombol aksi di dalam toast; menutup toast saat diklik.' },
                { name: 'id', type: 'string', description: 'Id sendiri; toast dengan id sama diperbarui, bukan diduplikasi.' },
            ]"
        />

        <DocProps
            title="useToast()"
            :rows="[
                { name: 'toast', type: '(options) => string', description: 'Tampilkan toast, mengembalikan id.' },
                { name: 'promise', type: '(promise, { loading, success, error }) => Promise', description: 'Toast memuat yang berubah sesuai hasil promise.' },
                { name: 'update', type: '(id, updates) => void', description: 'Ubah toast yang masih terbuka.' },
                { name: 'dismiss', type: '(id?) => void', description: 'Tutup satu toast, atau semua jika tanpa id.' },
            ]"
        />

        <DocProps
            title="Toaster"
            :rows="[
                { name: 'duration', type: 'number', default: '5000', description: 'Durasi tampil default (ms).' },
                { name: 'limit', type: 'number', default: '3', description: 'Jumlah toast yang terlihat bersamaan.' },
            ]"
        />
    </DocPage>
</template>
