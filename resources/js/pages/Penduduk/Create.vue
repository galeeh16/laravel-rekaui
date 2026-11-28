<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import PendudukForm from '@/components/penduduk/PendudukForm.vue';
import PendudukImportForm from '@/components/penduduk/PendudukImportForm.vue';
import Icon from '@/components/ui/Icon.vue';
import Tabs from '@/components/ui/Tabs.vue';
import TabsContent from '@/components/ui/TabsContent.vue';
import TabsList from '@/components/ui/TabsList.vue';
import TabsTrigger from '@/components/ui/TabsTrigger.vue';

defineProps({
    options: { type: Object, required: true },
    /** @type {{ maxFileSizeMb: number, maxRows: number, columns: string[] }} */
    importSettings: { type: Object, required: true },
});

/** `single` (one resident with the form) or `excel` (many residents from a file). */
const activeTab = ref('single');

const form = useForm({
    nik: '',
    nama: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    jenis_kelamin: null,
    alamat_lengkap: '',
    agama: null,
    status_perkawinan: null,
    pekerjaan: '',
    kewarganegaraan: 'WNI',
});

function submit() {
    form.post(route('penduduk.store'));
}
</script>

<template>
    <Head title="Tambah Penduduk" />

    <div class="pt-6 lg:pt-10">
        <Link :href="route('penduduk.index')" class="inline-flex items-center gap-1 text-sm text-muted transition-colors hover:text-ink">
            <Icon name="arrow-left" class="size-4" />
            List Penduduk
        </Link>

        <h1 class="mt-3 text-4xl font-medium tracking-tight sm:text-5xl">Tambah Penduduk</h1>
        <p class="mt-3 text-muted">Tambahkan data penduduk satu per satu, atau sekaligus dari file Excel.</p>
    </div>

    <Tabs v-model="activeTab" class="mt-8">
        <TabsList>
            <TabsTrigger value="single" icon="user">Input satuan</TabsTrigger>
            <TabsTrigger value="excel" icon="upload">Upload Excel</TabsTrigger>
        </TabsList>

        <TabsContent value="single" class="mt-2 max-w-4xl">
            <PendudukForm :form="form" :options="options" submit-label="Simpan penduduk" @submit="submit" />
        </TabsContent>

        <TabsContent value="excel" class="mt-2">
            <PendudukImportForm
                :max-file-size-mb="importSettings.maxFileSizeMb"
                :max-rows="importSettings.maxRows"
                :columns="importSettings.columns"
            />
        </TabsContent>
    </Tabs>
</template>
