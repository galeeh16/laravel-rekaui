<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Modal from '@/components/ui/Modal.vue';
import Select from '@/components/ui/Select.vue';
import Textarea from '@/components/ui/Textarea.vue';

const letterDetails = [
    { label: 'Nomor', value: '012/SK/XI/2026' },
    { label: 'Perihal', value: 'Pemberitahuan perubahan alamat' },
    { label: 'Penerima', value: 'PT Sinar Mandiri — Rina Kartika' },
    { label: 'Tanggal', value: '6 November 2026' },
];

/* Example 2: form controlled with v-model:open */
const departments = [
    { value: 'keuangan', label: 'Bagian Keuangan' },
    { value: 'umum', label: 'Bagian Umum' },
    { value: 'sdm', label: 'Bagian SDM' },
];

const isDispositionOpen = ref(false);
const disposition = ref({ department: null, note: '' });
const lastDisposition = ref(null);

function submitDisposition() {
    const department = departments.find((option) => option.value === disposition.value.department);

    lastDisposition.value = `Diteruskan ke ${department?.label ?? '—'}${disposition.value.note ? `: “${disposition.value.note}”` : '.'}`;
    disposition.value = { department: null, note: '' };
    isDispositionOpen.value = false;
}

/* Example 3: sizes */
const sizes = ['sm', 'md', 'lg', 'xl'];
const modalSize = ref('md');
const isSizeModalOpen = ref(false);

function openWithSize(size) {
    modalSize.value = size;
    isSizeModalOpen.value = true;
}

const basicCode = `
<script setup>
import Button from '@/components/ui/Button.vue';
import Modal from '@/components/ui/Modal.vue';
<\/script>

<template>
    <Modal title="Detail surat" description="Ringkasan surat yang sedang menunggu balasan.">
        <template #trigger>
            <Button variant="secondary" icon="eye">Lihat detail</Button>
        </template>

        <dl>...</dl>

        <!-- Without v-model, the slot receives close() -->
        <template #footer="{ close }">
            <Button variant="secondary" @click="close">Tutup</Button>
            <Button icon="download">Unduh PDF</Button>
        </template>
    </Modal>
</template>
`;

const formCode = `
<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Modal from '@/components/ui/Modal.vue';
import Select from '@/components/ui/Select.vue';
import Textarea from '@/components/ui/Textarea.vue';

const departments = [
    { value: 'keuangan', label: 'Bagian Keuangan' },
    { value: 'umum', label: 'Bagian Umum' },
];

const isOpen = ref(false);
const disposition = ref({ department: null, note: '' });

function submit() {
    // ...save, then:
    isOpen.value = false;
}
<\/script>

<template>
    <Button icon="forward" @click="isOpen = true">Disposisi</Button>

    <Modal
        v-model:open="isOpen"
        title="Disposisi surat"
        description="Teruskan surat 012/SK/XI/2026 ke bagian terkait."
        :dismissible="false"
    >
        <form id="disposition-form" class="flex flex-col gap-5" @submit.prevent="submit">
            <Select v-model="disposition.department" label="Teruskan ke" :options="departments" />
            <Textarea v-model="disposition.note" label="Catatan" rows="3" />
        </form>

        <template #footer>
            <Button variant="secondary" @click="isOpen = false">Batal</Button>
            <Button type="submit" form="disposition-form" :disabled="!disposition.department">Teruskan</Button>
        </template>
    </Modal>
</template>
`;

const sizeCode = `
<template>
    <Modal v-model:open="isOpen" size="lg" title="Pratinjau surat">
        ...
    </Modal>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Wrapper-nya bernama Modal (@/components/ui/Modal.vue). Tombol di slot #trigger membuka modal; Escape, klik di luar, atau tombol X menutupnya."
            :code="basicCode"
        >
            <Modal title="Detail surat" description="Ringkasan surat yang sedang menunggu balasan.">
                <template #trigger>
                    <Button variant="secondary" icon="eye">Lihat detail</Button>
                </template>

                <dl class="divide-y divide-line rounded-xl border border-line text-sm">
                    <div v-for="detail in letterDetails" :key="detail.label" class="flex gap-4 px-4 py-3">
                        <dt class="w-24 shrink-0 text-muted">{{ detail.label }}</dt>
                        <dd class="font-medium">{{ detail.value }}</dd>
                    </div>
                    <div class="flex gap-4 px-4 py-3">
                        <dt class="w-24 shrink-0 text-muted">Status</dt>
                        <dd><Badge variant="info" dot>Menunggu</Badge></dd>
                    </div>
                </dl>

                <template #footer="{ close }">
                    <Button variant="secondary" @click="close">Tutup</Button>
                    <Button icon="download">Unduh PDF</Button>
                </template>
            </Modal>
        </DocExample>

        <DocExample
            title="Form + v-model:open"
            description="Dengan :dismissible=&quot;false&quot;, Escape dan klik di luar tidak menutup modal sehingga isian tidak hilang. Field pertama otomatis mendapat fokus."
            :code="formCode"
        >
            <div class="flex flex-col items-center gap-4 text-center">
                <Button icon="forward" @click="isDispositionOpen = true">Disposisi</Button>
                <p class="max-w-sm text-sm text-muted">{{ lastDisposition ?? 'Belum ada disposisi.' }}</p>
            </div>

            <Modal
                v-model:open="isDispositionOpen"
                title="Disposisi surat"
                description="Teruskan surat 012/SK/XI/2026 ke bagian terkait."
                :dismissible="false"
            >
                <form id="disposition-form" class="flex flex-col gap-5" novalidate @submit.prevent="submitDisposition">
                    <Select
                        v-model="disposition.department"
                        label="Teruskan ke"
                        placeholder="Pilih bagian"
                        :options="departments"
                    />
                    <Textarea v-model="disposition.note" label="Catatan" rows="3" placeholder="Mohon ditindaklanjuti…" hint="Opsional" />
                </form>

                <template #footer>
                    <Button variant="secondary" @click="isDispositionOpen = false">Batal</Button>
                    <Button type="submit" form="disposition-form" icon="forward" :disabled="!disposition.department">Teruskan</Button>
                </template>
            </Modal>
        </DocExample>

        <DocExample title="Ukuran" description="Lebar maksimum: sm, md (default), lg, atau xl." :code="sizeCode">
            <div class="flex flex-wrap justify-center gap-2">
                <Button v-for="size in sizes" :key="size" variant="secondary" @click="openWithSize(size)">Ukuran {{ size }}</Button>
            </div>

            <Modal v-model:open="isSizeModalOpen" :size="modalSize" :title="`Modal ukuran ${modalSize}`">
                <p class="text-sm text-muted">
                    Gunakan sm untuk konfirmasi singkat, md untuk form biasa, lg untuk form dua kolom, dan xl untuk pratinjau surat
                    atau tabel.
                </p>

                <template #footer>
                    <Button @click="isSizeModalOpen = false">Mengerti</Button>
                </template>
            </Modal>
        </DocExample>

        <DocProps
            title="Modal"
            :rows="[
                { name: 'v-model:open', type: 'boolean', default: 'false', description: 'Status terbuka; opsional bila memakai slot #trigger.' },
                { name: 'title', type: 'string', description: 'Judul modal (wajib, dibacakan screen reader).' },
                { name: 'description', type: 'string', description: 'Teks penjelas di bawah judul.' },
                { name: 'size', type: `'sm' | 'md' | 'lg' | 'xl'`, default: `'md'`, description: 'Lebar maksimum modal.' },
                { name: 'dismissible', type: 'boolean', default: 'true', description: 'Tutup dengan Escape dan klik di luar; false agar hanya tombol yang menutup.' },
                { name: '#trigger', type: 'slot', description: 'Tombol pembuka (opsional).' },
                { name: '#default', type: 'slot { close }', description: 'Isi modal; bisa di-scroll bila panjang.' },
                { name: '#footer', type: 'slot { close }', description: 'Tombol aksi di bagian bawah, rata kanan.' },
            ]"
        />
    </DocPage>
</template>
