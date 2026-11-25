<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PendudukForm from '@/components/penduduk/PendudukForm.vue';
import Icon from '@/components/ui/Icon.vue';

const props = defineProps({
    resident: { type: Object, required: true },
    options: { type: Object, required: true },
});

const form = useForm({
    nik: props.resident.nik,
    nama: props.resident.nama,
    tempat_lahir: props.resident.tempat_lahir,
    tanggal_lahir: props.resident.tanggal_lahir,
    jenis_kelamin: props.resident.jenis_kelamin,
    alamat_lengkap: props.resident.alamat_lengkap ?? '',
    agama: props.resident.agama,
    status_perkawinan: props.resident.status_perkawinan,
    pekerjaan: props.resident.pekerjaan ?? '',
    kewarganegaraan: props.resident.kewarganegaraan,
});

function submit() {
    form.put(route('penduduk.update', props.resident.id));
}
</script>

<template>
    <Head :title="`Edit ${resident.nama}`" />

    <div class="pt-6 lg:pt-10">
        <Link :href="route('penduduk.index')" class="inline-flex items-center gap-1 text-sm text-muted transition-colors hover:text-ink">
            <Icon name="arrow-left" class="size-4" />
            List Penduduk
        </Link>

        <h1 class="mt-3 text-4xl font-medium tracking-tight sm:text-5xl">Edit Penduduk</h1>
        <p class="mt-3 text-muted">{{ resident.nama }} · NIK {{ resident.nik }}</p>
    </div>

    <div class="mt-8 max-w-4xl">
        <PendudukForm :form="form" :options="options" submit-label="Simpan perubahan" @submit="submit" />
    </div>
</template>
