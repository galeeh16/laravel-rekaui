<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';
import NikLookupInput from '@/components/penduduk/NikLookupInput.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Icon from '@/components/ui/Icon.vue';
import Textarea from '@/components/ui/Textarea.vue';

const props = defineProps({
    /** Document templates as `{ value: id, label: name }`. */
    templates: { type: Array, required: true },
    maxPurposeLength: { type: Number, required: true },
});

const form = useForm({
    penduduk_id: null,
    document_template_id: null,
    purpose: '',
});

const nik = ref('');

/** `{ id, nik, nama, fields }` of the chosen applicant, from the NIK search. */
const applicant = ref(null);

function selectApplicant(resident) {
    applicant.value = resident;
    form.penduduk_id = resident.id;
}

/** Typing a different NIK drops the applicant that was chosen before. */
watch(nik, (value) => {
    if (applicant.value && value !== applicant.value.nik) {
        applicant.value = null;
        form.penduduk_id = null;
    }
});

/** Hide a field's error as soon as the user changes that field. */
for (const field of Object.keys(form.data())) {
    watch(
        () => form[field],
        () => form.clearErrors(field),
    );
}

/** On success the server records the application and redirects to the list with a success toast. */
function submit() {
    form.post(route('document-applications.store'));
}
</script>

<template>
    <Head title="Buat Pengajuan" />

    <div class="pt-6 lg:pt-10">
        <Link
            :href="route('document-applications.index')"
            class="inline-flex items-center gap-1 text-sm text-muted transition-colors hover:text-ink"
        >
            <Icon name="arrow-left" class="size-4" />
            List Pengajuan
        </Link>

        <h1 class="mt-3 text-4xl font-medium tracking-tight sm:text-5xl">Buat Pengajuan</h1>
        <p class="mt-3 text-muted">Catat dokumen atau surat yang diajukan oleh penduduk. Pengajuan tersimpan sebagai riwayat.</p>
    </div>

    <Card class="mt-8 max-w-3xl p-5 sm:p-6">
        <form class="flex flex-col gap-6" novalidate @submit.prevent="submit">
            <div class="rounded-xl bg-canvas/70 p-4">
                <NikLookupInput
                    v-model="nik"
                    label="Pemohon (NIK)"
                    hint="Ketik minimal 3 digit NIK, lalu pilih penduduk dari daftar."
                    not-found-hint="NIK tidak ditemukan. Tambahkan penduduknya dulu di menu Penduduk."
                    :error="form.errors.penduduk_id"
                    @select="selectApplicant"
                />

                <div v-if="applicant" class="mt-3 flex items-center gap-3 rounded-lg border border-line bg-surface p-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand-ink">
                        <Icon name="user" class="size-5" />
                    </span>

                    <div class="min-w-0 text-sm">
                        <p class="truncate font-medium">{{ applicant.nama }}</p>
                        <p class="truncate text-muted">
                            <span class="tabular-nums">{{ applicant.nik }}</span>
                            <template v-if="applicant.fields.alamat_lengkap"> · {{ applicant.fields.alamat_lengkap }}</template>
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="templates.length === 0" class="rounded-xl border border-dashed border-line p-4 text-sm">
                <p class="text-muted">Belum ada template dokumen yang bisa diajukan.</p>
                <Button :href="route('document-templates.create')" variant="secondary" size="sm" icon="plus" class="mt-3">
                    Create Dokumen
                </Button>
            </div>

            <Combobox
                v-else
                v-model="form.document_template_id"
                label="Dokumen yang diajukan"
                name="document_template_id"
                placeholder="Cari atau pilih dokumen"
                empty-text="Tidak ada dokumen dengan nama ini."
                :options="templates"
                :error="form.errors.document_template_id"
            />

            <Textarea
                v-model="form.purpose"
                label="Keperluan"
                name="purpose"
                rows="3"
                :maxlength="props.maxPurposeLength"
                placeholder="Misalnya: melamar pekerjaan, mendaftar sekolah"
                hint="Opsional"
                :error="form.errors.purpose"
            />

            <div class="flex flex-wrap gap-2 border-t border-line pt-5">
                <Button type="submit" icon="send" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan…' : 'Simpan pengajuan' }}
                </Button>
                <Button :href="route('document-applications.index')" variant="secondary">Batal</Button>
            </div>
        </form>
    </Card>
</template>
