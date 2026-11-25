<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Switch from '@/components/ui/Switch.vue';
import Textarea from '@/components/ui/Textarea.vue';

const letterNumber = ref('');
const summary = ref('');
const isConfidential = ref(false);

const basicCode = `
<script setup>
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
<\/script>

<template>
    <Label for="letter-number">Nomor surat</Label>
    <Input id="letter-number" v-model="letterNumber" placeholder="005/UND/XI/2026" />
</template>
`;

const requiredCode = `
<template>
    <Label for="summary" required>Ringkasan isi</Label>
    <Textarea id="summary" v-model="summary" rows="3" required />

    <div class="flex items-center justify-between">
        <Label for="confidential">Tandai sebagai rahasia</Label>
        <Switch id="confidential" v-model="isConfidential" />
    </div>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Hubungkan ke kontrol lewat for/id; klik label memfokuskan input. Input, Select, dsb. sudah memakai Label lewat prop label."
            :code="basicCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-1.5">
                <Label for="doc-letter-number">Nomor surat</Label>
                <Input id="doc-letter-number" v-model="letterNumber" placeholder="005/UND/XI/2026" />
            </div>
        </DocExample>

        <DocExample
            title="Wajib diisi & kontrol lain"
            description="required menambahkan tanda bintang merah. Label juga bisa dipasangkan dengan Switch; klik label untuk menyalakannya."
            :code="requiredCode"
        >
            <div class="flex w-full max-w-sm flex-col gap-5">
                <div class="flex flex-col gap-1.5">
                    <Label for="doc-summary" required>Ringkasan isi</Label>
                    <Textarea id="doc-summary" v-model="summary" rows="3" required />
                </div>

                <div class="flex items-center justify-between gap-4">
                    <Label for="doc-confidential">Tandai sebagai rahasia</Label>
                    <Switch id="doc-confidential" v-model="isConfidential" />
                </div>

                <p class="text-sm text-muted">Rahasia: {{ isConfidential }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Label"
            :rows="[
                { name: 'for', type: 'string', description: 'Id kontrol yang dilabeli.' },
                { name: 'required', type: 'boolean', default: 'false', description: 'Tampilkan tanda bintang merah (hanya visual; set required di kontrolnya juga).' },
                { name: 'slot default', type: '—', description: 'Teks label.' },
            ]"
        />
    </DocPage>
</template>
