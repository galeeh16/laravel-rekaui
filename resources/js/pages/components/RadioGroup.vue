<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import RadioGroup from '@/components/ui/RadioGroup.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const urgency = ref('biasa');

const urgencyOptions = [
    { value: 'biasa', label: 'Biasa' },
    { value: 'segera', label: 'Segera' },
    { value: 'sangat-segera', label: 'Sangat segera' },
    { value: 'kilat', label: 'Kilat', disabled: true },
];

const delivery = ref('email');

const deliveryOptions = [
    { value: 'email', label: 'Email', hint: 'Langsung diterima, dengan tanda tangan elektronik.' },
    { value: 'kurir', label: 'Kurir internal', hint: 'Diantar dalam 1 hari kerja.' },
    { value: 'pos', label: 'Pos tercatat', hint: 'Untuk penerima di luar kota, 2–4 hari kerja.' },
];

const classification = ref(null);

const basicCode = `
<script setup>
import { ref } from 'vue';
import RadioGroup from '@/components/ui/RadioGroup.vue';

const urgency = ref('biasa');
<\/script>

<template>
    <RadioGroup
        v-model="urgency"
        label="Sifat surat"
        name="urgency"
        :options="[
            { value: 'biasa', label: 'Biasa' },
            { value: 'segera', label: 'Segera' },
            { value: 'sangat-segera', label: 'Sangat segera' },
            { value: 'kilat', label: 'Kilat', disabled: true },
        ]"
    />

    <p>Dipilih: {{ urgency }}</p>
</template>
`;

const cardCode = `
<template>
    <RadioGroup
        v-model="delivery"
        label="Metode pengiriman"
        variant="card"
        orientation="vertical"
        :options="[
            { value: 'email', label: 'Email', hint: 'Langsung diterima, dengan tanda tangan elektronik.' },
            { value: 'kurir', label: 'Kurir internal', hint: 'Diantar dalam 1 hari kerja.' },
            { value: 'pos', label: 'Pos tercatat', hint: 'Untuk penerima di luar kota, 2–4 hari kerja.' },
        ]"
    />
</template>
`;

const errorCode = `
<template>
    <RadioGroup
        v-model="form.classification"
        label="Klasifikasi"
        variant="card"
        :options="['Terbuka', 'Terbatas', 'Rahasia']"
        :error="form.errors.classification"
        required
    />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Satu pilihan dari beberapa opsi; tombol panah berpindah antar opsi. Opsi bisa dinonaktifkan satu per satu."
            :code="basicCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-4">
                <RadioGroup v-model="urgency" label="Sifat surat" name="urgency" :options="urgencyOptions" />

                <p class="text-sm text-muted">Dipilih: {{ urgency }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Card dengan keterangan"
            description="variant=&quot;card&quot; membuat tiap opsi berupa kartu; hint menambahkan keterangan di bawah label."
            :code="cardCode"
        >
            <div class="flex w-full max-w-md flex-col gap-4">
                <RadioGroup v-model="delivery" label="Metode pengiriman" variant="card" orientation="vertical" :options="deliveryOptions" />

                <p class="text-sm text-muted">Dipilih: {{ delivery }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Opsi sederhana + error"
            description="options boleh berupa array string; error menandai semua opsi dan menampilkan pesan, misalnya dari form.errors Inertia."
            :code="errorCode"
        >
            <RadioGroup
                v-model="classification"
                label="Klasifikasi"
                variant="card"
                :options="['Terbuka', 'Terbatas', 'Rahasia']"
                :error="classification ? null : 'Klasifikasi surat wajib dipilih.'"
                class="w-full max-w-lg"
                required
            />
        </DocExample>

        <DocProps
            title="RadioGroup"
            :rows="[
                { name: 'v-model', type: 'string | number', default: 'null', description: 'Nilai opsi yang dipilih.' },
                { name: 'options', type: 'Array<{ value, label, hint?, disabled? } | string | number>', description: 'Daftar opsi (wajib).' },
                { name: 'label', type: 'string', description: 'Judul grup.' },
                { name: 'variant', type: `'default' | 'card'`, default: `'default'`, description: 'Radio biasa atau kartu berbingkai.' },
                { name: 'orientation', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Susunan opsi; horizontal menjadi satu baris mulai layar sm.' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Wajib memilih salah satu opsi.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua opsi.' },
                { name: 'error', type: 'string', description: 'Pesan error; opsi ikut berbingkai merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah grup.' },
            ]"
        />
    </DocPage>
</template>
