<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import NumberField from '@/components/ui/NumberField.vue';

const copies = ref(2);
const stampFee = ref(10000);
const discount = ref(0.15);

const basicCode = `
<script setup>
import { ref } from 'vue';
import NumberField from '@/components/ui/NumberField.vue';

const copies = ref(2);
<\/script>

<template>
    <NumberField
        v-model="copies"
        label="Jumlah rangkap"
        :min="1"
        :max="10"
        hint="Maksimal 10 rangkap per surat."
    />

    <p>Nilai: {{ copies }}</p>
</template>
`;

const formatCode = `
<script setup>
import { ref } from 'vue';
import NumberField from '@/components/ui/NumberField.vue';

const stampFee = ref(10000);
const discount = ref(0.15);
<\/script>

<template>
    <NumberField
        v-model="stampFee"
        label="Biaya meterai & pengiriman"
        :min="0"
        :step="1000"
        :format-options="{ style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }"
    />

    <NumberField
        v-model="discount"
        label="Potongan anggaran"
        :min="0"
        :max="1"
        :step="0.05"
        :format-options="{ style: 'percent' }"
    />
</template>
`;

const statesCode = `
<template>
    <NumberField label="Nomor urut surat" :model-value="0" :min="1" error="Nomor urut minimal 1." />

    <NumberField label="Tahun anggaran" :model-value="2026" :format-options="{ useGrouping: false }" disabled />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik − / + (tahan untuk mengulang), gunakan panah atas/bawah, atau ketik langsung. Nilai dibatasi min dan max."
            :code="basicCode"
        >
            <div class="flex w-full max-w-xs flex-col gap-3">
                <NumberField v-model="copies" label="Jumlah rangkap" :min="1" :max="10" hint="Maksimal 10 rangkap per surat." />

                <p class="text-sm text-muted">Nilai: {{ copies ?? '—' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Format mata uang & persen"
            description="format-options diteruskan ke Intl.NumberFormat dengan locale id-ID; v-model tetap berupa angka biasa."
            :code="formatCode"
        >
            <div class="grid w-full max-w-xl gap-5 sm:grid-cols-2">
                <div class="flex flex-col gap-3">
                    <NumberField
                        v-model="stampFee"
                        label="Biaya meterai & pengiriman"
                        :min="0"
                        :step="1000"
                        :format-options="{ style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }"
                    />
                    <p class="text-sm text-muted">v-model: {{ stampFee ?? '—' }}</p>
                </div>

                <div class="flex flex-col gap-3">
                    <NumberField
                        v-model="discount"
                        label="Potongan anggaran"
                        :min="0"
                        :max="1"
                        :step="0.05"
                        :format-options="{ style: 'percent' }"
                    />
                    <p class="text-sm text-muted">v-model: {{ discount ?? '—' }}</p>
                </div>
            </div>
        </DocExample>

        <DocExample title="Error & nonaktif" description="Pesan error mewarnai border; disabled mengunci input dan tombolnya." :code="statesCode">
            <div class="grid w-full max-w-xl gap-5 sm:grid-cols-2">
                <NumberField label="Nomor urut surat" :model-value="0" :min="1" error="Nomor urut minimal 1." />
                <NumberField label="Tahun anggaran" :model-value="2026" :format-options="{ useGrouping: false }" disabled />
            </div>
        </DocExample>

        <DocProps
            title="NumberField"
            :rows="[
                { name: 'v-model', type: 'number | undefined', description: 'Nilai angka; undefined saat input dikosongkan.' },
                { name: 'label', type: 'string', description: 'Label di atas input, terhubung lewat id.' },
                { name: 'min', type: 'number', description: 'Nilai terkecil; tombol − nonaktif saat tercapai.' },
                { name: 'max', type: 'number', description: 'Nilai terbesar; tombol + nonaktif saat tercapai.' },
                { name: 'step', type: 'number', default: '1', description: 'Besar perubahan per klik / panah (Page Up/Down = 10×).' },
                { name: 'format-options', type: 'Intl.NumberFormatOptions', description: 'Format tampilan, misalnya mata uang IDR atau persen.' },
                { name: 'locale', type: 'string', default: `'id-ID'`, description: 'Locale untuk pemisah ribuan/desimal.' },
                { name: 'name', type: 'string', description: 'Nama field untuk form HTML biasa.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan input dan tombol.' },
                { name: 'readonly', type: 'boolean', default: 'false', description: 'Nilai bisa dibaca tetapi tidak diubah.' },
                { name: 'error', type: 'string', description: 'Pesan error; border berubah merah.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan di bawah input (disembunyikan saat ada error).' },
                { name: 'class / atribut lain', type: '—', description: 'class ke pembungkus; atribut lain (placeholder, autofocus…) ke <input>.' },
            ]"
        />
    </DocPage>
</template>
