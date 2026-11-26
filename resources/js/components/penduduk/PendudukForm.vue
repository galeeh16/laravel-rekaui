<script setup>
/**
 * Fields of a resident (penduduk), shared by the create and edit pages. The page owns the Inertia
 * form and submits it; this component only renders the fields and its errors.
 *
 * Usage: <PendudukForm :form="form" :options="options" submit-label="Simpan" @submit="form.post(...)" />
 */
import { getLocalTimeZone, today } from '@internationalized/date';
import { route } from 'ziggy-js';
import { watch } from 'vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import DatePicker from '@/components/ui/DatePicker.vue';
import Input from '@/components/ui/Input.vue';
import RadioGroup from '@/components/ui/RadioGroup.vue';
import Select from '@/components/ui/Select.vue';
import Textarea from '@/components/ui/Textarea.vue';

const props = defineProps({
    /** An Inertia `useForm()` with the penduduk fields. */
    form: { type: Object, required: true },
    /** @type {{ genders: Array, religions: Array, maritalStatuses: Array, citizenships: Array }} */
    options: { type: Object, required: true },
    submitLabel: { type: String, default: 'Simpan' },
});

const emit = defineEmits(['submit']);

/** Birth date can be today at the latest (matches the `before_or_equal:today` rule). */
const latestBirthDate = today(getLocalTimeZone()).toString();

/** Hide a field's error as soon as the user changes that field. */
for (const field of Object.keys(props.form.data())) {
    watch(
        () => props.form[field],
        () => props.form.clearErrors(field),
    );
}
</script>

<template>
    <form class="flex flex-col gap-4" novalidate @submit.prevent="emit('submit')">
        <Card class="p-5 sm:p-6">
            <h2 class="text-lg font-medium">Identitas</h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <Input
                    v-model="form.nik"
                    label="NIK"
                    name="nik"
                    inputmode="numeric"
                    maxlength="16"
                    placeholder="16 digit angka"
                    :error="form.errors.nik"
                />

                <Input v-model="form.nama" label="Nama lengkap" name="nama" autocomplete="name" :error="form.errors.nama" />

                <Input v-model="form.tempat_lahir" label="Tempat lahir" name="tempat_lahir" :error="form.errors.tempat_lahir" />

                <DatePicker
                    v-model="form.tanggal_lahir"
                    label="Tanggal lahir"
                    name="tanggal_lahir"
                    min-date="1900-01-02"
                    :max-date="latestBirthDate"
                    :error="form.errors.tanggal_lahir"
                />

                <RadioGroup
                    v-model="form.jenis_kelamin"
                    label="Jenis kelamin"
                    name="jenis_kelamin"
                    variant="card"
                    :options="options.genders"
                    :error="form.errors.jenis_kelamin"
                    class="sm:col-span-2"
                />
            </div>
        </Card>

        <Card class="p-5 sm:p-6">
            <h2 class="text-lg font-medium">Data lainnya</h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <Select
                    v-model="form.agama"
                    label="Agama"
                    name="agama"
                    placeholder="Pilih agama"
                    hint="Opsional"
                    :options="options.religions"
                    clearable
                    :error="form.errors.agama"
                />

                <Select
                    v-model="form.status_perkawinan"
                    label="Status perkawinan"
                    name="status_perkawinan"
                    placeholder="Pilih status"
                    hint="Opsional"
                    :options="options.maritalStatuses"
                    clearable
                    :error="form.errors.status_perkawinan"
                />

                <Input
                    v-model="form.pekerjaan"
                    label="Pekerjaan"
                    name="pekerjaan"
                    hint="Opsional"
                    :error="form.errors.pekerjaan"
                />

                <RadioGroup
                    v-model="form.kewarganegaraan"
                    label="Kewarganegaraan"
                    name="kewarganegaraan"
                    :options="options.citizenships"
                    :error="form.errors.kewarganegaraan"
                />

                <Textarea
                    v-model="form.alamat_lengkap"
                    label="Alamat lengkap"
                    name="alamat_lengkap"
                    rows="3"
                    hint="Opsional"
                    :error="form.errors.alamat_lengkap"
                    class="sm:col-span-2"
                />
            </div>
        </Card>

        <div class="flex flex-wrap gap-2">
            <Button type="submit" icon="check" :disabled="form.processing">
                {{ form.processing ? 'Menyimpan…' : submitLabel }}
            </Button>
            <Button :href="route('penduduk.index')" variant="secondary">Batal</Button>
        </div>
    </form>
</template>
