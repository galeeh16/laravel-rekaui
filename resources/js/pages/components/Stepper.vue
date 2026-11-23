<script setup>
import { computed, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Alert from '@/components/ui/Alert.vue';
import Button from '@/components/ui/Button.vue';
import Stepper from '@/components/ui/Stepper.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const composeSteps = [
    { title: 'Data surat', description: 'Nomor & perihal', hint: 'Isi nomor surat, perihal, sifat, dan tujuan surat.' },
    { title: 'Isi & lampiran', description: 'Teks dan berkas', hint: 'Tulis isi surat lalu unggah lampiran pendukung.' },
    { title: 'Tanda tangan', description: 'Pilih pejabat', hint: 'Pilih pejabat penandatangan dan atur tanda tangan elektronik.' },
    { title: 'Kirim', description: 'Periksa & kirim', hint: 'Periksa kembali seluruh data sebelum surat dikirim.' },
];

const composeStep = ref(1);
const isSent = computed(() => composeStep.value > composeSteps.length);

const dispositionSteps = [
    { title: 'Surat diterima', description: '20 Nov 2026, 09.12 · Sekretariat', icon: 'inbox' },
    { title: 'Diverifikasi', description: '20 Nov 2026, 10.30 · Rina Kartika', icon: 'search' },
    { title: 'Didisposisikan', description: 'Menunggu tindak lanjut Kabag Umum', icon: 'forward' },
    { title: 'Selesai', description: 'Diarsipkan setelah ditindaklanjuti', icon: 'archive' },
];

const templateSteps = [
    { title: 'Kop surat' },
    { title: 'Isi' },
    { title: 'Penandatangan' },
    { title: 'Pratinjau', icon: 'eye' },
];

const templateStep = ref(1);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Stepper from '@/components/ui/Stepper.vue';

const steps = [
    { title: 'Data surat', description: 'Nomor & perihal' },
    { title: 'Isi & lampiran', description: 'Teks dan berkas' },
    { title: 'Tanda tangan', description: 'Pilih pejabat' },
    { title: 'Kirim', description: 'Periksa & kirim' },
];

const step = ref(1);
<\/script>

<template>
    <Stepper v-model="step" :steps="steps" />

    <Button variant="secondary" :disabled="step === 1" @click="step--">Kembali</Button>
    <Button v-if="step < steps.length" @click="step++">Lanjut</Button>
    <!-- step di atas jumlah langkah = semua langkah selesai -->
    <Button v-else icon="send" @click="step++">Kirim surat</Button>
</template>
`;

const verticalCode = `
<template>
    <Stepper
        orientation="vertical"
        :default-value="3"
        :steps="[
            { title: 'Surat diterima', description: '20 Nov 2026, 09.12 · Sekretariat', icon: 'inbox' },
            { title: 'Diverifikasi', description: '20 Nov 2026, 10.30 · Rina Kartika', icon: 'search' },
            { title: 'Didisposisikan', description: 'Menunggu tindak lanjut Kabag Umum', icon: 'forward' },
            { title: 'Selesai', description: 'Diarsipkan setelah ditindaklanjuti', icon: 'archive' },
        ]"
    />
</template>
`;

const nonLinearCode = `
<script setup>
import { ref } from 'vue';

const step = ref(1);
<\/script>

<template>
    <!-- Semua langkah bisa diklik langsung -->
    <Stepper
        v-model="step"
        :linear="false"
        :steps="[{ title: 'Kop surat' }, { title: 'Isi' }, { title: 'Penandatangan' }, { title: 'Pratinjau', icon: 'eye' }]"
    />

    <p>Langkah aktif: {{ step }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar + v-model"
            description="Alur membuat surat keluar; tombol di luar mengubah langkah lewat v-model."
            :code="basicCode"
        >
            <div class="flex w-full max-w-2xl flex-col gap-6">
                <Stepper v-model="composeStep" :steps="composeSteps" />

                <Alert v-if="isSent" variant="success">Surat berhasil dikirim ke Dinas Pendidikan.</Alert>
                <div v-else class="rounded-xl border border-line bg-canvas/60 p-4 text-sm">
                    <p class="font-medium text-ink">Langkah {{ composeStep }}: {{ composeSteps[composeStep - 1].title }}</p>
                    <p class="mt-1 text-muted">{{ composeSteps[composeStep - 1].hint }}</p>
                </div>

                <div class="flex justify-between gap-3">
                    <Button variant="secondary" :disabled="composeStep === 1 || isSent" @click="composeStep--">Kembali</Button>
                    <Button v-if="isSent" variant="soft" icon="plus" @click="composeStep = 1">Buat surat lagi</Button>
                    <Button v-else-if="composeStep < composeSteps.length" icon-trailing="arrow-right" @click="composeStep++">
                        Lanjut
                    </Button>
                    <Button v-else icon="send" @click="composeStep++">Kirim surat</Button>
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Vertikal"
            description="Melacak alur disposisi surat, lengkap dengan ikon dan keterangan tiap langkah."
            :code="verticalCode"
        >
            <Stepper orientation="vertical" :default-value="3" :steps="dispositionSteps" class="w-full max-w-sm" />
        </DocExample>

        <DocExample
            title="Non-linear"
            description="Dengan :linear=&quot;false&quot; pengguna bisa melompat ke langkah mana pun."
            :code="nonLinearCode"
        >
            <div class="flex w-full max-w-xl flex-col gap-4">
                <Stepper v-model="templateStep" :linear="false" :steps="templateSteps" />
                <p class="text-center text-sm text-muted">Langkah aktif: {{ templateStep }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Stepper"
            :rows="[
                { name: 'v-model', type: 'number', description: 'Langkah aktif, mulai dari 1. Lebih dari jumlah langkah = semua selesai.' },
                { name: 'steps', type: 'Array<{ title, description?, icon?, disabled?, completed? }>', description: 'Daftar langkah (wajib).' },
                { name: 'default-value', type: 'number', default: '1', description: 'Langkah aktif awal, tanpa v-model.' },
                { name: 'orientation', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Arah susunan langkah.' },
                { name: 'linear', type: 'boolean', default: 'true', description: 'Hanya bisa maju satu langkah; false = bebas melompat.' },
            ]"
        />
    </DocPage>
</template>
