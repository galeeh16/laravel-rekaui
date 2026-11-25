<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Field from '@/components/ui/Field.vue';
import FieldControl from '@/components/ui/FieldControl.vue';

const email = ref('');
const summary = ref('');

const employeeNumber = ref('');
const letterNumber = ref('');

/** NIP: exactly 18 digits. */
function validateEmployeeNumber(value) {
    return /^\d{18}$/.test(value ?? '') ? null : 'NIP harus 18 digit angka.';
}

const usedLetterNumbers = ['005/UND/XI/2026', '006/UND/XI/2026'];

/** Pretend server check: is the letter number already used? */
function validateLetterNumber(value) {
    return new Promise((resolve) => {
        setTimeout(() => resolve(usedLetterNumbers.includes(value) ? 'Nomor surat sudah dipakai.' : null), 500);
    });
}

const password = ref('');

const basicCode = `
<script setup>
import { ref } from 'vue';
import Field from '@/components/ui/Field.vue';
import FieldControl from '@/components/ui/FieldControl.vue';

const email = ref('');
<\/script>

<template>
    <Field
        name="email"
        label="Email penerima"
        description="Salinan surat dikirim ke alamat ini."
        validation-mode="onBlur"
        :messages="{ typeMismatch: 'Format email tidak valid, contoh: nama@instansi.go.id.' }"
        required
    >
        <FieldControl v-model="email" type="email" placeholder="nama@instansi.go.id" />
    </Field>

    <Field name="summary" label="Ringkasan" validation-mode="onBlur" :messages="{ tooShort: 'Minimal 20 karakter.' }">
        <FieldControl v-model="summary" as="textarea" rows="3" minlength="20" />
    </Field>
</template>
`;

const customCode = `
<script setup>
import { ref } from 'vue';
import Field from '@/components/ui/Field.vue';
import FieldControl from '@/components/ui/FieldControl.vue';

const employeeNumber = ref('');
const letterNumber = ref('');

function validateEmployeeNumber(value) {
    return /^\\d{18}$/.test(value ?? '') ? null : 'NIP harus 18 digit angka.';
}

async function validateLetterNumber(value) {
    const response = await fetch(\`/letters/number-check?number=\${encodeURIComponent(value)}\`);
    const { taken } = await response.json();

    return taken ? 'Nomor surat sudah dipakai.' : null;
}
<\/script>

<template>
    <Field name="nip" label="NIP pemohon" validation-mode="onChange" :validate="validateEmployeeNumber" required>
        <FieldControl v-model="employeeNumber" inputmode="numeric" maxlength="18" />
    </Field>

    <Field
        name="number"
        label="Nomor surat"
        validation-mode="onChange"
        :validation-debounce-time="400"
        :validate="validateLetterNumber"
        description="Coba 005/UND/XI/2026."
    >
        <FieldControl v-model="letterNumber" />
    </Field>
</template>
`;

const validityCode = `
<template>
    <Field
        v-slot="{ validity }"
        name="password"
        label="Password baru"
        validation-mode="onChange"
        :messages="{ tooShort: 'Minimal 8 karakter.', patternMismatch: 'Harus mengandung angka.' }"
        required
    >
        <FieldControl v-model="password" type="password" minlength="8" pattern=".*\\d.*" />

        <p v-if="validity.valid === null">Belum diperiksa</p>
        <p v-else-if="validity.valid">Password memenuhi syarat</p>
    </Field>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Field menghubungkan label, deskripsi, dan pesan error ke kontrolnya (id, aria-describedby, aria-invalid) dan memakai validasi bawaan browser. Kosongkan lalu pindah fokus untuk melihat pesannya."
            :code="basicCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-5">
                <Field
                    name="email"
                    label="Email penerima"
                    description="Salinan surat dikirim ke alamat ini."
                    validation-mode="onBlur"
                    :messages="{ typeMismatch: 'Format email tidak valid, contoh: nama@instansi.go.id.' }"
                    required
                >
                    <FieldControl v-model="email" type="email" placeholder="nama@instansi.go.id" />
                </Field>

                <Field name="summary" label="Ringkasan" validation-mode="onBlur" :messages="{ tooShort: 'Minimal 20 karakter.' }">
                    <FieldControl v-model="summary" as="textarea" rows="3" minlength="20" />
                </Field>
            </div>
        </DocExample>

        <DocExample
            title="Validasi kustom & async"
            description="validate menerima nilai dan mengembalikan pesan (atau null). Boleh berupa Promise, misalnya cek ke server, dengan debounce."
            :code="customCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-5">
                <Field name="nip" label="NIP pemohon" validation-mode="onChange" :validate="validateEmployeeNumber" required>
                    <FieldControl v-model="employeeNumber" inputmode="numeric" maxlength="18" />
                </Field>

                <Field
                    name="number"
                    label="Nomor surat"
                    validation-mode="onChange"
                    :validation-debounce-time="400"
                    :validate="validateLetterNumber"
                    description="Coba 005/UND/XI/2026."
                >
                    <FieldControl v-model="letterNumber" />
                </Field>
            </div>
        </DocExample>

        <DocExample
            title="Status validasi (slot)"
            description="Slot default menerima validity (ValidityState dari reka FieldValidity), invalid, dan errors untuk tampilan sendiri."
            :code="validityCode"
        >
            <div class="w-full max-w-sm">
                <Field
                    v-slot="{ validity }"
                    name="password"
                    label="Password baru"
                    validation-mode="onChange"
                    :messages="{ tooShort: 'Minimal 8 karakter.', patternMismatch: 'Harus mengandung angka.' }"
                    required
                >
                    <FieldControl v-model="password" type="password" minlength="8" pattern=".*\d.*" />

                    <p
                        :class="[
                            'inline-flex w-fit items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-medium',
                            validity.valid === null && 'bg-ink/5 text-muted',
                            validity.valid === true && 'bg-brand-soft text-brand-ink',
                            validity.valid === false && 'bg-danger-soft text-danger',
                        ]"
                    >
                        <span v-if="validity.valid === null">Belum diperiksa</span>
                        <span v-else-if="validity.valid">Password memenuhi syarat</span>
                        <span v-else>Belum memenuhi syarat</span>
                    </p>
                </Field>
            </div>
        </DocExample>

        <DocProps
            title="Field"
            :rows="[
                { name: 'name', type: 'string', description: 'Nama field; dipakai Form untuk nilai dan error server.' },
                { name: 'label', type: 'string', description: 'Label di atas kontrol.' },
                { name: 'description', type: 'string', description: 'Keterangan di bawah kontrol (disembunyikan saat error).' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Wajib diisi; diteruskan ke kontrol dan label diberi bintang.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan kontrol dan lewati validasi.' },
                { name: 'validation-mode', type: `'onSubmit' | 'onBlur' | 'onChange'`, default: `'onSubmit'`, description: 'Kapan validasi berjalan; di luar Form pakai onBlur atau onChange.' },
                { name: 'validate', type: '(value, formValues) => string | string[] | null | Promise', description: 'Validasi kustom; kembalikan pesan bila tidak valid.' },
                { name: 'validation-debounce-time', type: 'number', description: 'Jeda (ms) validasi onChange, berguna untuk validasi async.' },
                { name: 'messages', type: 'Record<constraint, string>', description: 'Pesan per constraint (valueMissing, typeMismatch, tooShort, patternMismatch, …); default berbahasa Indonesia.' },
                { name: 'invalid', type: 'boolean', description: 'Paksa status tidak valid dari logika sendiri.' },
                { name: 'slot default', type: '{ invalid, errors, validity }', description: 'Tempat kontrol (FieldControl, Select, Checkbox).' },
            ]"
        />

        <DocProps
            title="FieldControl"
            :rows="[
                { name: 'v-model', type: 'string | number', default: `''`, description: 'Nilai input.' },
                { name: 'as', type: `'input' | 'textarea'`, default: `'input'`, description: 'Elemen yang dirender.' },
                { name: 'rows', type: 'number', default: '4', description: 'Jumlah baris (khusus textarea).' },
                { name: 'type, minlength, pattern, …', type: 'attribute', description: 'Atribut lain diteruskan ke elemen dan ikut validasi bawaan.' },
            ]"
        />
    </DocPage>
</template>
