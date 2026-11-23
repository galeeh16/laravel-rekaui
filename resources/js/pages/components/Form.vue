<script setup>
import { useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import Field from '@/components/ui/Field.vue';
import FieldControl from '@/components/ui/FieldControl.vue';
import Form from '@/components/ui/Form.vue';
import Select from '@/components/ui/Select.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const letter = ref({ number: '', subject: '', recipientEmail: '', urgency: undefined, attachmentChecked: false });
const submittedValues = ref(null);

const urgencyOptions = [
    { value: 'biasa', label: 'Biasa' },
    { value: 'segera', label: 'Segera' },
    { value: 'sangat-segera', label: 'Sangat segera' },
];

function submitLetter(values) {
    submittedValues.value = values;
}

const form = useForm({
    number: '005/UND/XI/2026',
    subject: 'Undangan rapat koordinasi',
});

const isSending = ref(false);
const wasSaved = ref(false);
let serverTimeout = null;

/** Simulated server response: the number is already taken the first time. */
function submitToServer() {
    isSending.value = true;
    wasSaved.value = false;

    serverTimeout = setTimeout(() => {
        isSending.value = false;
        form.clearErrors();

        if (form.number === '005/UND/XI/2026') {
            form.setError('number', 'Nomor surat 005/UND/XI/2026 sudah dipakai.');
        } else {
            wasSaved.value = true;
        }
    }, 700);
}

onBeforeUnmount(() => clearTimeout(serverTimeout));

const nativeCode = `
<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import Field from '@/components/ui/Field.vue';
import FieldControl from '@/components/ui/FieldControl.vue';
import Form from '@/components/ui/Form.vue';
import Select from '@/components/ui/Select.vue';

const letter = ref({ number: '', subject: '', recipientEmail: '', urgency: undefined, attachmentChecked: false });

function submitLetter(values) {
    // Only called when every Field is valid
    console.log(values);
}
<\/script>

<template>
    <Form @submit="submitLetter">
        <Field name="number" label="Nomor surat" description="Format: 005/UND/XI/2026" :messages="{ patternMismatch: 'Format nomor surat tidak sesuai.' }" required>
            <FieldControl v-model="letter.number" pattern="\\d{3}/[A-Z]+/[IVX]+/\\d{4}" />
        </Field>

        <Field name="subject" label="Perihal" :messages="{ tooShort: 'Perihal minimal 5 karakter.' }" required>
            <FieldControl v-model="letter.subject" minlength="5" />
        </Field>

        <Field name="recipient_email" label="Email penerima">
            <FieldControl v-model="letter.recipientEmail" type="email" />
        </Field>

        <Field name="urgency" label="Sifat surat" required>
            <Select v-model="letter.urgency" :options="urgencyOptions" placeholder="Pilih sifat" />
        </Field>

        <Field name="attachment_checked" :messages="{ valueMissing: 'Centang setelah lampiran diperiksa.' }" required>
            <Checkbox v-model="letter.attachmentChecked">Lampiran sudah saya periksa</Checkbox>
        </Field>

        <Button type="submit">Simpan draf</Button>
    </Form>
</template>
`;

const inertiaCode = `
<script setup>
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Button from '@/components/ui/Button.vue';
import Field from '@/components/ui/Field.vue';
import FieldControl from '@/components/ui/FieldControl.vue';
import Form from '@/components/ui/Form.vue';

const form = useForm({
    number: '005/UND/XI/2026',
    subject: 'Undangan rapat koordinasi',
});

function submit() {
    form.post(route('letters.store'), { preserveScroll: true });
}
<\/script>

<template>
    <!-- form.errors (from Laravel validation) shows under the Field with the same name -->
    <Form :errors="form.errors" @submit="submit">
        <Field name="number" label="Nomor surat" required>
            <FieldControl v-model="form.number" />
        </Field>

        <Field name="subject" label="Perihal" required>
            <FieldControl v-model="form.subject" />
        </Field>

        <Button type="submit" :disabled="form.processing">Simpan</Button>
    </Form>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Validasi bawaan"
            description="Form memvalidasi semua Field saat submit, memfokuskan field pertama yang salah, lalu memvalidasi ulang sambil mengetik. @submit hanya terpanggil bila semua valid."
            :code="nativeCode"
        >
            <div class="flex w-full max-w-md flex-col gap-5">
                <Form @submit="submitLetter">
                    <Field
                        name="number"
                        label="Nomor surat"
                        description="Format: 005/UND/XI/2026"
                        :messages="{ patternMismatch: 'Format nomor surat tidak sesuai.' }"
                        required
                    >
                        <FieldControl v-model="letter.number" pattern="\d{3}/[A-Z]+/[IVX]+/\d{4}" />
                    </Field>

                    <Field name="subject" label="Perihal" :messages="{ tooShort: 'Perihal minimal 5 karakter.' }" required>
                        <FieldControl v-model="letter.subject" minlength="5" />
                    </Field>

                    <Field name="recipient_email" label="Email penerima">
                        <FieldControl v-model="letter.recipientEmail" type="email" />
                    </Field>

                    <Field name="urgency" label="Sifat surat" required>
                        <Select v-model="letter.urgency" :options="urgencyOptions" placeholder="Pilih sifat" />
                    </Field>

                    <Field name="attachment_checked" :messages="{ valueMissing: 'Centang setelah lampiran diperiksa.' }" required>
                        <Checkbox v-model="letter.attachmentChecked">Lampiran sudah saya periksa</Checkbox>
                    </Field>

                    <Button type="submit" class="self-start">Simpan draf</Button>
                </Form>

                <pre v-if="submittedValues" class="rounded-xl bg-ink/5 p-3 font-mono text-xs text-muted">{{ submittedValues }}</pre>
            </div>
        </DocExample>

        <DocExample
            title="Dengan useForm Inertia (error server)"
            description="Teruskan form.errors ke prop errors: pesan dari validasi Laravel muncul di Field dengan name yang sama, field pertama difokuskan, dan pesan hilang saat diedit. Di demo ini respons server disimulasikan; ubah nomornya untuk berhasil."
            :code="inertiaCode"
        >
            <div class="flex w-full max-w-md flex-col gap-5">
                <Form :errors="form.errors" @submit="submitToServer">
                    <Field name="number" label="Nomor surat" required>
                        <FieldControl v-model="form.number" />
                    </Field>

                    <Field name="subject" label="Perihal" required>
                        <FieldControl v-model="form.subject" />
                    </Field>

                    <Button type="submit" class="self-start" :disabled="isSending">{{ isSending ? 'Menyimpan…' : 'Simpan' }}</Button>
                </Form>

                <p class="text-sm text-muted">
                    form.errors: {{ form.errors }}
                    <span v-if="wasSaved" class="ml-1 font-medium text-brand">· Tersimpan</span>
                </p>
            </div>
        </DocExample>

        <DocProps
            title="Form"
            :rows="[
                { name: 'errors', type: 'Record<string, string | string[]>', description: 'Error dari server per nama field, mis. form.errors dari useForm.' },
                { name: 'validation-mode', type: `'onSubmit' | 'onBlur' | 'onChange'`, default: `'onSubmit'`, description: 'Kapan Field di dalamnya divalidasi (bisa ditimpa per Field).' },
                { name: '@submit', type: '(values, event) => void', description: 'Dipanggil saat semua Field valid; submit bawaan browser selalu dicegah.' },
                { name: 'validate()', type: '(name?) => boolean', description: 'Method lewat ref: validasi semua field atau satu field.' },
                { name: 'slot default', type: '—', description: 'Field dan tombol submit.' },
            ]"
        />
    </DocPage>
</template>
