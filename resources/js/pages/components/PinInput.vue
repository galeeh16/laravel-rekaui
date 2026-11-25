<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import PinInput from '@/components/ui/PinInput.vue';

const verificationCode = ref('');
const completedCode = ref(null);
const signaturePin = ref('');

function resetVerification() {
    verificationCode.value = '';
    completedCode.value = null;
}

const otpCode = `
<script setup>
import { ref } from 'vue';
import PinInput from '@/components/ui/PinInput.vue';

const verificationCode = ref('');
const completedCode = ref(null);
<\/script>

<template>
    <PinInput
        v-model="verificationCode"
        label="Kode verifikasi"
        type="number"
        otp
        hint="Kode 6 digit dikirim ke email Anda."
        @complete="(code) => (completedCode = code)"
    />

    <p>v-model: '{{ verificationCode }}'</p>
    <p v-if="completedCode">Kode lengkap: {{ completedCode }}</p>
</template>
`;

const maskCode = `
<template>
    <PinInput
        v-model="signaturePin"
        label="PIN tanda tangan elektronik"
        :length="4"
        type="number"
        mask
        placeholder="•"
    />
</template>
`;

const errorCode = `
<template>
    <PinInput
        label="Kode undangan rapat"
        model-value="RPT7Q"
        placeholder="–"
        error="Kode undangan tidak ditemukan atau sudah kedaluwarsa."
    />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Kode OTP + v-model"
            description="Ketik atau tempel kode; fokus berpindah otomatis. Event complete terpanggil saat semua kotak terisi."
            :code="otpCode"
        >
            <div class="flex flex-col gap-4">
                <PinInput
                    v-model="verificationCode"
                    label="Kode verifikasi"
                    type="number"
                    otp
                    hint="Kode 6 digit dikirim ke email Anda."
                    @complete="(code) => (completedCode = code)"
                />

                <div class="flex flex-wrap items-center gap-3 text-sm text-muted">
                    <span>v-model: '{{ verificationCode }}'</span>
                    <span v-if="completedCode" class="font-medium text-brand">Kode lengkap: {{ completedCode }}</span>
                    <Button variant="ghost" size="sm" icon="x" @click="resetVerification">Reset</Button>
                </div>
            </div>
        </DocExample>

        <DocExample title="PIN tersamar" description="Dengan mask, karakter disembunyikan seperti password." :code="maskCode">
            <div class="flex flex-col gap-3">
                <PinInput v-model="signaturePin" label="PIN tanda tangan elektronik" :length="4" type="number" mask placeholder="•" />
                <p class="text-sm text-muted">Panjang PIN: {{ signaturePin.length }} / 4</p>
            </div>
        </DocExample>

        <DocExample title="Huruf & error" description="Tanpa type number, huruf juga diterima; pesan error mewarnai setiap kotak." :code="errorCode">
            <PinInput
                label="Kode undangan rapat"
                model-value="RPT7Q"
                placeholder="–"
                error="Kode undangan tidak ditemukan atau sudah kedaluwarsa."
            />
        </DocExample>

        <DocProps
            title="PinInput"
            :rows="[
                { name: 'v-model', type: 'string', default: `''`, description: 'Kode yang diketik sebagai satu string, mis. 482915 — siap dikirim ke Laravel.' },
                { name: 'length', type: 'number', default: '6', description: 'Jumlah kotak.' },
                { name: 'type', type: `'text' | 'number'`, default: `'text'`, description: 'number hanya menerima angka dan membuka keyboard angka di ponsel.' },
                { name: 'otp', type: 'boolean', default: 'false', description: 'Isi berurutan dan izinkan autofill kode sekali pakai dari browser.' },
                { name: 'mask', type: 'boolean', default: 'false', description: 'Sembunyikan karakter seperti password.' },
                { name: 'placeholder', type: 'string', default: `'○'`, description: 'Penanda di kotak kosong.' },
                { name: 'label', type: 'string', description: 'Label di atas kotak; klik label memfokuskan kotak pertama.' },
                { name: 'name', type: 'string', description: 'Nama field tersembunyi untuk form HTML biasa.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua kotak.' },
                { name: 'error', type: 'string', description: 'Pesan error; border kotak berubah merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah kotak.' },
                { name: '@complete', type: '(value: string) => void', description: 'Dipanggil saat semua kotak terisi.' },
            ]"
        />
    </DocPage>
</template>
