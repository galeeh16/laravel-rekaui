<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import TagGroup from '@/components/ui/TagGroup.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const categories = ['Undangan', 'Keuangan', 'Kepegawaian', 'Pengadaan', 'Hukum', 'Umum'];
const selectedCategories = ref(['Undangan', 'Keuangan']);

const initialRecipients = ['Kepala Dinas', 'Sekretaris', 'Kabid Anggaran', 'Kasubag Umum', 'Bendahara'];
const recipients = ref([...initialRecipients]);
const lastRemoved = ref([]);

const priorities = [
    { value: 'low', label: 'Biasa', icon: 'clock' },
    { value: 'high', label: 'Penting', icon: 'star' },
    { value: 'urgent', label: 'Segera', icon: 'triangle-alert' },
    { value: 'secret', label: 'Rahasia', icon: 'lock', disabled: true },
];
const priority = ref('high');

function resetRecipients() {
    recipients.value = [...initialRecipients];
    lastRemoved.value = [];
}

const multipleCode = `
<script setup>
import { ref } from 'vue';
import TagGroup from '@/components/ui/TagGroup.vue';

const categories = ['Undangan', 'Keuangan', 'Kepegawaian', 'Pengadaan', 'Hukum', 'Umum'];
const selectedCategories = ref(['Undangan', 'Keuangan']);
<\/script>

<template>
    <TagGroup
        v-model="selectedCategories"
        :items="categories"
        selection-mode="multiple"
        label="Filter kategori surat"
    />

    <p>Dipilih: {{ selectedCategories.join(', ') }}</p>
</template>
`;

const removableCode = `
<script setup>
import { ref } from 'vue';
import TagGroup from '@/components/ui/TagGroup.vue';

const recipients = ref(['Kepala Dinas', 'Sekretaris', 'Kabid Anggaran', 'Kasubag Umum', 'Bendahara']);
const lastRemoved = ref([]);
<\/script>

<template>
    <!-- Removed tags disappear from \`recipients\` through v-model:items. -->
    <TagGroup
        v-model:items="recipients"
        removable
        label="Daftar distribusi"
        @remove="(values) => (lastRemoved = values)"
    />
</template>
`;

const singleCode = `
<script setup>
import { ref } from 'vue';
import TagGroup from '@/components/ui/TagGroup.vue';

const priorities = [
    { value: 'low', label: 'Biasa', icon: 'clock' },
    { value: 'high', label: 'Penting', icon: 'star' },
    { value: 'urgent', label: 'Segera', icon: 'triangle-alert' },
    { value: 'secret', label: 'Rahasia', icon: 'lock', disabled: true },
];
const priority = ref('high');
<\/script>

<template>
    <TagGroup
        v-model="priority"
        :items="priorities"
        selection-mode="single"
        disallow-empty-selection
        label="Sifat surat"
    />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Pilihan banyak + v-model"
            description="Klik, Enter atau Spasi untuk memilih; panah kiri/kanan untuk berpindah tag. Escape mengosongkan pilihan."
            :code="multipleCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <TagGroup v-model="selectedCategories" :items="categories" selection-mode="multiple" label="Filter kategori surat" />

                <p class="text-sm text-muted">Dipilih: {{ selectedCategories.join(', ') || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Bisa dihapus"
            description="Dengan removable, tag dihapus lewat tombol × atau tombol Delete/Backspace saat tag terfokus."
            :code="removableCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <TagGroup v-model:items="recipients" removable label="Daftar distribusi" @remove="(values) => (lastRemoved = values)" />

                <p v-if="recipients.length === 0" class="text-sm text-muted">Semua penerima sudah dihapus.</p>

                <div class="flex flex-wrap items-center gap-3 text-sm text-muted">
                    <span>Terakhir dihapus: {{ lastRemoved.join(', ') || '—' }}</span>
                    <Button variant="ghost" size="sm" @click="resetRecipients">Kembalikan</Button>
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Pilihan tunggal dengan ikon"
            description="selection-mode single dengan disallow-empty-selection sehingga selalu ada satu yang terpilih; item bisa dinonaktifkan."
            :code="singleCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <TagGroup v-model="priority" :items="priorities" selection-mode="single" disallow-empty-selection label="Sifat surat" />

                <p class="text-sm text-muted">Sifat: {{ priority }}</p>
            </div>
        </DocExample>

        <DocProps
            title="TagGroup"
            :rows="[
                { name: 'items / v-model:items', type: 'Array<string | { value, label, icon?, disabled? }>', default: '[]', description: 'Daftar tag. Pakai v-model:items agar tag yang dihapus langsung hilang.' },
                { name: 'v-model', type: 'string | string[]', description: 'Tag yang dipilih (array untuk selection-mode multiple).' },
                { name: 'selection-mode', type: `'none' | 'single' | 'multiple'`, default: `'none'`, description: 'Apakah tag bisa dipilih, satu atau banyak.' },
                { name: 'disallow-empty-selection', type: 'boolean', default: 'false', description: 'Cegah pilihan terakhir dibatalkan.' },
                { name: 'removable', type: 'boolean', default: 'false', description: 'Tampilkan tombol × dan aktifkan Delete/Backspace.' },
                { name: 'label', type: 'string', description: 'Judul grup, dipakai juga sebagai aria-labelledby.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua tag.' },
                { name: '@remove', type: '(values: string[]) => void', description: 'Nilai tag yang dihapus.' },
            ]"
        />
    </DocPage>
</template>
