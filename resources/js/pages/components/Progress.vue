<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Progress from '@/components/ui/Progress.vue';

const uploadProgress = ref(0);
let uploadTimer = null;

const isUploading = computed(() => uploadProgress.value > 0 && uploadProgress.value < 100);
const isUploaded = computed(() => uploadProgress.value === 100);

/** Simulated upload: grows by a random step until it reaches 100. */
function startUpload() {
    clearInterval(uploadTimer);
    uploadProgress.value = 0;

    uploadTimer = setInterval(() => {
        uploadProgress.value = Math.min(100, uploadProgress.value + 4 + Math.round(Math.random() * 12));

        if (uploadProgress.value === 100) {
            clearInterval(uploadTimer);
        }
    }, 350);
}

onBeforeUnmount(() => clearInterval(uploadTimer));

const basicCode = `
<script setup>
import Progress from '@/components/ui/Progress.vue';
<\/script>

<template>
    <Progress :model-value="68" label="Kuota penyimpanan arsip" show-value />
    <Progress :model-value="45" :max="60" label="Surat dibalas bulan ini" variant="info" show-value />
    <Progress :model-value="30" label="Draf menunggu tanda tangan" variant="warning" size="lg" />
    <Progress :model-value="12" label="Disposisi melewati tenggat" variant="danger" size="sm" />
</template>
`;

const uploadCode = `
<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Progress from '@/components/ui/Progress.vue';

const progress = ref(0);

function startUpload() {
    progress.value = 0;
    const timer = setInterval(() => {
        progress.value = Math.min(100, progress.value + 10);
        if (progress.value === 100) clearInterval(timer);
    }, 350);
}
<\/script>

<template>
    <Progress v-model="progress" label="Lampiran_SK_Rektor.pdf" show-value />
    <Button icon="upload" @click="startUpload">Unggah lampiran</Button>
</template>
`;

const indeterminateCode = `
<template>
    <!-- null = durasi belum diketahui -->
    <Progress :model-value="null" label="Menyiapkan pratinjau surat…" />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Dasar" description="Label, persentase, varian warna, ukuran, dan max kustom." :code="basicCode">
            <div class="flex w-full max-w-md flex-col gap-6">
                <Progress :model-value="68" label="Kuota penyimpanan arsip" show-value />
                <Progress :model-value="45" :max="60" label="Surat dibalas bulan ini" variant="info" show-value />
                <Progress :model-value="30" label="Draf menunggu tanda tangan" variant="warning" size="lg" />
                <Progress :model-value="12" label="Disposisi melewati tenggat" variant="danger" size="sm" />
            </div>
        </DocExample>

        <DocExample title="Unggah lampiran (v-model)" description="Nilai reaktif; bar bergerak halus setiap kali nilai berubah." :code="uploadCode">
            <div class="flex w-full max-w-md flex-col gap-4">
                <Progress v-model="uploadProgress" label="Lampiran_SK_Rektor.pdf" show-value />

                <div class="flex items-center justify-between gap-3">
                    <Button icon="upload" size="sm" :disabled="isUploading" @click="startUpload">
                        {{ isUploaded ? 'Unggah ulang' : 'Unggah lampiran' }}
                    </Button>

                    <Badge v-if="isUploaded" variant="success" dot>Selesai</Badge>
                    <Badge v-else-if="isUploading" variant="info" dot>Mengunggah…</Badge>
                    <Badge v-else>Belum diunggah</Badge>
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Tak tentu"
            description="Nilai null menandakan proses yang durasinya belum diketahui."
            :code="indeterminateCode"
        >
            <Progress :model-value="null" label="Menyiapkan pratinjau surat…" class="max-w-md" />
        </DocExample>

        <DocProps
            title="Progress"
            :rows="[
                { name: 'v-model', type: 'number | null', default: 'null', description: 'Nilai saat ini (0 sampai max); null = tak tentu.' },
                { name: 'max', type: 'number', default: '100', description: 'Nilai maksimum.' },
                { name: 'label', type: 'string', description: 'Label di atas bar; juga menjadi nama aksesibel.' },
                { name: 'show-value', type: 'boolean', default: 'false', description: 'Tampilkan persentase di kanan label.' },
                { name: 'variant', type: `'brand' | 'info' | 'warning' | 'danger'`, default: `'brand'`, description: 'Warna bar.' },
                { name: 'size', type: `'sm' | 'md' | 'lg'`, default: `'md'`, description: 'Tinggi bar.' },
            ]"
        />
    </DocPage>
</template>
