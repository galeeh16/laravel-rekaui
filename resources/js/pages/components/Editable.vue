<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Editable from '@/components/ui/Editable.vue';
import Icon from '@/components/ui/Icon.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const subject = ref('Undangan rapat koordinasi bulanan');
const lastSaved = ref(null);

const archiveTitle = ref('Arsip Surat Keluar 2026');

const folders = ref([
    { id: 1, name: 'Undangan' },
    { id: 2, name: 'Nota dinas' },
    { id: 3, name: 'Surat keputusan' },
]);

const activityLog = ref([]);

function logRename(name) {
    activityLog.value = [`Folder diganti menjadi “${name}”`, ...activityLog.value].slice(0, 3);
}

const basicCode = `
<script setup>
import { ref } from 'vue';
import Editable from '@/components/ui/Editable.vue';

const subject = ref('Undangan rapat koordinasi bulanan');

function save(value) {
    // e.g. router.patch(route('letters.update', letter.id), { subject: value })
}
<\/script>

<template>
    <Editable v-model="subject" aria-label="Perihal" :max-length="120" @submit="save" />

    <p>Nilai: {{ subject }}</p>
</template>
`;

const titleCode = `
<template>
    <Editable
        v-model="archiveTitle"
        size="lg"
        activation-mode="dblclick"
        placeholder="Beri judul arsip"
        aria-label="Judul arsip"
    />
</template>
`;

const listCode = `
<template>
    <ul>
        <li v-for="folder in folders" :key="folder.id" class="flex items-center gap-2">
            <Icon name="folder" class="size-4 text-muted" />
            <Editable
                v-model="folder.name"
                :controls="false"
                :aria-label="\`Nama folder \${folder.name}\`"
                class="flex-1"
                @submit="logRename"
            />
        </li>
    </ul>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik teks atau tombol pensil untuk mengubah. Enter atau klik di luar menyimpan, Escape membatalkan. @submit hanya terpanggil bila nilainya berubah."
            :code="basicCode"
        >
            <div class="flex w-full max-w-md flex-col gap-3">
                <Editable v-model="subject" aria-label="Perihal" :max-length="120" @submit="lastSaved = $event" />

                <p class="text-sm text-muted">Nilai: {{ subject || '—' }}</p>
                <p class="text-sm text-muted">Terakhir disimpan: {{ lastSaved ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Judul, klik dua kali"
            description="size=&quot;lg&quot; untuk judul halaman; activation-mode=&quot;dblclick&quot; agar tidak terpicu klik biasa. Kosongkan untuk melihat placeholder."
            :code="titleCode"
        >
            <div class="flex w-full max-w-md flex-col gap-3">
                <Editable
                    v-model="archiveTitle"
                    size="lg"
                    activation-mode="dblclick"
                    placeholder="Beri judul arsip"
                    aria-label="Judul arsip"
                />

                <p class="text-sm text-muted">Nilai: {{ archiveTitle || '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Dalam daftar"
            description="Tanpa tombol (:controls=&quot;false&quot;) agar ringkas; @submit cocok untuk mengirim perubahan ke server."
            :code="listCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-3">
                <ul class="divide-y divide-line rounded-xl border border-line">
                    <li v-for="folder in folders" :key="folder.id" class="flex items-center gap-2 px-3 py-1.5">
                        <Icon name="folder" class="size-4 text-muted" />
                        <Editable
                            v-model="folder.name"
                            :controls="false"
                            :aria-label="`Nama folder ${folder.name}`"
                            class="flex-1"
                            @submit="logRename"
                        />
                    </li>
                </ul>

                <ul class="flex flex-col gap-1 text-sm text-muted">
                    <li v-for="entry in activityLog" :key="entry">{{ entry }}</li>
                    <li v-if="activityLog.length === 0">Belum ada perubahan.</li>
                </ul>
            </div>
        </DocExample>

        <DocProps
            title="Editable"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Teks yang disimpan.' },
                { name: 'placeholder', type: 'string', default: `'Klik untuk mengisi…'`, description: 'Teks saat kosong.' },
                { name: 'activation-mode', type: `'focus' | 'dblclick' | 'none'`, default: `'focus'`, description: 'Cara mulai mengedit; none = hanya lewat tombol pensil.' },
                { name: 'submit-mode', type: `'both' | 'enter' | 'blur' | 'none'`, default: `'both'`, description: 'Cara menyimpan; none = hanya lewat tombol simpan.' },
                { name: 'controls', type: 'boolean', default: 'true', description: 'Tampilkan tombol ubah / simpan / batal.' },
                { name: 'size', type: `'md' | 'lg'`, default: `'md'`, description: 'Ukuran teks; lg untuk judul.' },
                { name: 'select-on-focus', type: 'boolean', default: 'true', description: 'Blok seluruh teks saat mulai mengedit.' },
                { name: 'max-length', type: 'number', description: 'Batas jumlah karakter.' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan pengeditan.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Hanya tampil, tidak bisa diedit.' },
                { name: '@submit', type: '(value: string) => void', description: 'Setelah disimpan dan nilainya berubah.' },
            ]"
        />
    </DocPage>
</template>
