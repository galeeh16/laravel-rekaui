<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Toggle from '@/components/ui/Toggle.vue';

const isImportant = ref(true);
const showArchived = ref(false);

const isConfidential = ref(false);
const isNotified = ref(true);
const isPinned = ref(false);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Toggle from '@/components/ui/Toggle.vue';

const isImportant = ref(true);
const showArchived = ref(false);
<\/script>

<template>
    <Toggle v-model="isImportant" icon="star">Penting</Toggle>
    <Toggle v-slot="{ pressed }" v-model="showArchived" icon="archive">
        {{ pressed ? 'Sembunyikan arsip' : 'Tampilkan arsip' }}
    </Toggle>

    <p>Penting: {{ isImportant }} · Arsip: {{ showArchived }}</p>
</template>
`;

const iconCode = `
<template>
    <!-- Icon-only toggles need an aria-label. -->
    <Toggle v-model="isConfidential" icon="lock" size="icon" aria-label="Tandai rahasia" />
    <Toggle v-model="isNotified" icon="bell" size="icon" aria-label="Notifikasi balasan" />

    <Toggle v-model="isPinned" variant="ghost" size="icon-md" icon="heart" aria-label="Simpan ke favorit" />
    <Toggle variant="ghost" size="sm" icon="eye">Pratinjau</Toggle>
    <Toggle :model-value="true" icon="check" disabled>Terkunci</Toggle>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar + v-model"
            description="Tombol yang tetap tertekan sampai diklik lagi; v-model berupa boolean. Slot prop pressed bisa mengganti label saat aktif."
            :code="basicCode"
        >
            <div class="flex flex-col items-center gap-4">
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <Toggle v-model="isImportant" icon="star">Penting</Toggle>
                    <Toggle v-slot="{ pressed }" v-model="showArchived" icon="archive">
                        {{ pressed ? 'Sembunyikan arsip' : 'Tampilkan arsip' }}
                    </Toggle>
                </div>

                <p class="text-sm text-muted">Penting: {{ isImportant }} · Arsip: {{ showArchived }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Ikon, varian & nonaktif"
            description="Ukuran icon untuk tombol ikon saja (wajib aria-label), varian ghost untuk tampilan tanpa border."
            :code="iconCode"
        >
            <div class="flex flex-col items-center gap-4">
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <Toggle v-model="isConfidential" icon="lock" size="icon" aria-label="Tandai rahasia" />
                    <Toggle v-model="isNotified" icon="bell" size="icon" aria-label="Notifikasi balasan" />
                    <Toggle v-model="isPinned" variant="ghost" size="icon-md" icon="heart" aria-label="Simpan ke favorit" />
                    <Toggle variant="ghost" size="sm" icon="eye">Pratinjau</Toggle>
                    <Toggle :model-value="true" icon="check" disabled>Terkunci</Toggle>
                </div>

                <p class="text-sm text-muted">Rahasia: {{ isConfidential }} · Notifikasi: {{ isNotified }} · Favorit: {{ isPinned }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Toggle"
            :rows="[
                { name: 'v-model', type: 'boolean', default: 'false', description: 'Status tertekan.' },
                { name: 'icon', type: 'string', description: 'Nama ikon di depan label.' },
                { name: 'variant', type: `'outline' | 'ghost'`, default: `'outline'`, description: 'Dengan border atau polos.' },
                { name: 'size', type: `'sm' | 'md' | 'icon-sm' | 'icon-md' | 'icon'`, default: `'md'`, description: 'Ukuran seperti Button; ukuran icon-* untuk tombol ikon saja.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan toggle.' },
                { name: '#default', type: '{ pressed: boolean }', description: 'Label; slot prop pressed untuk mengganti teks saat aktif.' },
            ]"
        />
    </DocPage>
</template>
