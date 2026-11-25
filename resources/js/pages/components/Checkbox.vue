<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import CheckboxGroup from '@/components/ui/CheckboxGroup.vue';

const isImportant = ref(true);
const sendCopy = ref(false);

const letters = [
    { id: 1, subject: 'Undangan rapat koordinasi bulanan' },
    { id: 2, subject: 'Permohonan data kepegawaian' },
    { id: 3, subject: 'Laporan realisasi anggaran Q3' },
];

const selectedLetters = ref([1]);

/** `true` when every letter is selected, `'indeterminate'` when only some are. */
const allSelected = computed({
    get: () => {
        if (selectedLetters.value.length === letters.length) {
            return true;
        }

        return selectedLetters.value.length > 0 ? 'indeterminate' : false;
    },
    set: (value) => {
        selectedLetters.value = value === true ? letters.map((letter) => letter.id) : [];
    },
});

function toggleLetter(id, isChecked) {
    selectedLetters.value = isChecked
        ? [...selectedLetters.value, id]
        : selectedLetters.value.filter((selectedId) => selectedId !== id);
}

const channels = ref(['email']);

const channelOptions = [
    { value: 'email', label: 'Email', hint: 'Ke alamat email dinas' },
    { value: 'whatsapp', label: 'WhatsApp', hint: 'Ke nomor yang terdaftar di profil' },
    { value: 'app', label: 'Notifikasi aplikasi' },
    { value: 'sms', label: 'SMS', disabled: true, hint: 'Belum tersedia' },
];

const basicCode = `
<script setup>
import { ref } from 'vue';
import Checkbox from '@/components/ui/Checkbox.vue';

const isImportant = ref(true);
const sendCopy = ref(false);
<\/script>

<template>
    <Checkbox v-model="isImportant">Tandai sebagai penting</Checkbox>

    <Checkbox v-model="sendCopy" hint="Salinan PDF dikirim setelah surat ditandatangani.">
        Kirim salinan ke email saya
    </Checkbox>

    <Checkbox disabled>Arsipkan otomatis (khusus admin)</Checkbox>

    <p>Penting: {{ isImportant }} · Salinan: {{ sendCopy }}</p>
</template>
`;

const indeterminateCode = `
<script setup>
import { computed, ref } from 'vue';
import Checkbox from '@/components/ui/Checkbox.vue';

const letters = [
    { id: 1, subject: 'Undangan rapat koordinasi bulanan' },
    { id: 2, subject: 'Permohonan data kepegawaian' },
    { id: 3, subject: 'Laporan realisasi anggaran Q3' },
];

const selectedLetters = ref([1]);

const allSelected = computed({
    get: () => {
        if (selectedLetters.value.length === letters.length) {
            return true;
        }

        return selectedLetters.value.length > 0 ? 'indeterminate' : false;
    },
    set: (value) => {
        selectedLetters.value = value === true ? letters.map((letter) => letter.id) : [];
    },
});

function toggleLetter(id, isChecked) {
    selectedLetters.value = isChecked
        ? [...selectedLetters.value, id]
        : selectedLetters.value.filter((selectedId) => selectedId !== id);
}
<\/script>

<template>
    <Checkbox v-model="allSelected">Pilih semua surat</Checkbox>

    <Checkbox
        v-for="letter in letters"
        :key="letter.id"
        :model-value="selectedLetters.includes(letter.id)"
        @update:model-value="toggleLetter(letter.id, $event)"
    >
        {{ letter.subject }}
    </Checkbox>
</template>
`;

const groupCode = `
<script setup>
import { ref } from 'vue';
import CheckboxGroup from '@/components/ui/CheckboxGroup.vue';

const channels = ref(['email']);

const channelOptions = [
    { value: 'email', label: 'Email', hint: 'Ke alamat email dinas' },
    { value: 'whatsapp', label: 'WhatsApp', hint: 'Ke nomor yang terdaftar di profil' },
    { value: 'app', label: 'Notifikasi aplikasi' },
    { value: 'sms', label: 'SMS', disabled: true, hint: 'Belum tersedia' },
];
<\/script>

<template>
    <CheckboxGroup
        v-model="channels"
        label="Kirim notifikasi disposisi lewat"
        name="channels"
        :options="channelOptions"
        :max="2"
        hint="Pilih maksimal dua saluran."
    />

    <p>Dipilih: {{ channels.join(', ') }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="v-model berupa boolean. Klik teksnya juga ikut mencentang; hint tampil di bawah label."
            :code="basicCode"
        >
            <div class="flex w-full max-w-md flex-col gap-4">
                <Checkbox v-model="isImportant">Tandai sebagai penting</Checkbox>

                <Checkbox v-model="sendCopy" hint="Salinan PDF dikirim setelah surat ditandatangani.">
                    Kirim salinan ke email saya
                </Checkbox>

                <Checkbox disabled>Arsipkan otomatis (khusus admin)</Checkbox>

                <p class="text-sm text-muted">Penting: {{ isImportant }} · Salinan: {{ sendCopy }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Indeterminate (pilih semua)"
            description="Nilai 'indeterminate' menampilkan tanda strip, cocok untuk checkbox induk saat hanya sebagian item yang terpilih."
            :code="indeterminateCode"
        >
            <div class="flex w-full max-w-md flex-col gap-3">
                <div class="rounded-xl border border-line">
                    <div class="border-b border-line bg-canvas/60 px-4 py-3">
                        <Checkbox v-model="allSelected" class="font-medium">Pilih semua surat</Checkbox>
                    </div>

                    <div class="flex flex-col gap-3 px-4 py-3">
                        <Checkbox
                            v-for="letter in letters"
                            :key="letter.id"
                            :model-value="selectedLetters.includes(letter.id)"
                            @update:model-value="toggleLetter(letter.id, $event)"
                        >
                            {{ letter.subject }}
                        </Checkbox>
                    </div>
                </div>

                <p class="text-sm text-muted">
                    Terpilih: {{ selectedLetters.length }} dari {{ letters.length }} surat · v-model induk: {{ allSelected }}
                </p>
            </div>
        </DocExample>

        <DocExample
            title="CheckboxGroup"
            description="Beberapa checkbox dengan satu v-model array, navigasi panah, opsi nonaktif, dan batas max."
            :code="groupCode"
        >
            <div class="flex w-full max-w-md flex-col gap-4">
                <CheckboxGroup
                    v-model="channels"
                    label="Kirim notifikasi disposisi lewat"
                    name="channels"
                    :options="channelOptions"
                    :max="2"
                    hint="Pilih maksimal dua saluran."
                />

                <p class="text-sm text-muted">Dipilih: {{ channels.join(', ') || '—' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Checkbox"
            :rows="[
                { name: 'v-model', type: `boolean | 'indeterminate'`, default: 'false', description: `Status centang; 'indeterminate' menampilkan tanda strip.` },
                { name: 'hint', type: 'string', description: 'Teks bantuan abu-abu di bawah label.' },
                { name: 'error', type: 'string', description: 'Pesan error; kotak ikut berwarna merah.' },
                { name: 'value', type: 'string | number', default: `'on'`, description: 'Nilai yang dikirim form, dan nilai item di dalam CheckboxGroup.' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan checkbox.' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Wajib dicentang (dipakai validasi Field/Form).' },
                { name: 'slot default', type: '—', description: 'Teks label di samping kotak.' },
            ]"
        />

        <DocProps
            title="CheckboxGroup"
            :rows="[
                { name: 'v-model', type: 'array', default: '[]', description: 'Nilai-nilai yang dicentang.' },
                { name: 'options', type: 'Array<{ value, label, hint?, disabled? } | string | number>', description: 'Daftar pilihan (wajib).' },
                { name: 'label', type: 'string', description: 'Judul grup.' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'orientation', type: `'vertical' | 'horizontal'`, default: `'vertical'`, description: 'Susunan pilihan; horizontal menjadi satu baris mulai layar sm.' },
                { name: 'max', type: 'number', description: 'Batas jumlah pilihan; sisanya nonaktif saat batas tercapai.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua pilihan.' },
                { name: 'error', type: 'string', description: 'Pesan error di bawah grup.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah grup.' },
            ]"
        />
    </DocPage>
</template>
