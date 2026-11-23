<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Input from '@/components/ui/Input.vue';
import Tabs from '@/components/ui/Tabs.vue';
import TabsContent from '@/components/ui/TabsContent.vue';
import TabsList from '@/components/ui/TabsList.vue';
import TabsTrigger from '@/components/ui/TabsTrigger.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const activeTab = ref('inbox');

const segmentedCode = `
<script setup>
import Tabs from '@/components/ui/Tabs.vue';
import TabsContent from '@/components/ui/TabsContent.vue';
import TabsList from '@/components/ui/TabsList.vue';
import TabsTrigger from '@/components/ui/TabsTrigger.vue';
<\/script>

<template>
    <Tabs default-value="account">
        <TabsList>
            <TabsTrigger value="account" icon="user">Akun</TabsTrigger>
            <TabsTrigger value="password" icon="lock">Password</TabsTrigger>
        </TabsList>

        <TabsContent value="account">...form akun...</TabsContent>
        <TabsContent value="password">...form password...</TabsContent>
    </Tabs>
</template>
`;

const lineCode = `
<script setup>
import { ref } from 'vue';

const activeTab = ref('inbox');
<\/script>

<template>
    <Tabs v-model="activeTab">
        <TabsList variant="line">
            <TabsTrigger value="inbox">Surat masuk</TabsTrigger>
            <TabsTrigger value="outbox">Surat keluar</TabsTrigger>
            <TabsTrigger value="archive" disabled>Arsip</TabsTrigger>
        </TabsList>

        <TabsContent value="inbox">...</TabsContent>
        <TabsContent value="outbox">...</TabsContent>
    </Tabs>

    <p>Tab aktif: {{ activeTab }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Segmented" description="Gaya default: tombol di dalam latar pill." :code="segmentedCode">
            <Tabs default-value="account" class="w-full max-w-md">
                <TabsList>
                    <TabsTrigger value="account" icon="user">Akun</TabsTrigger>
                    <TabsTrigger value="password" icon="lock">Password</TabsTrigger>
                </TabsList>

                <TabsContent value="account">
                    <div class="flex flex-col gap-4 rounded-xl border border-line p-5">
                        <Input label="Nama" model-value="Test User" />
                        <Input label="Email" type="email" model-value="test@example.com" />
                    </div>
                </TabsContent>

                <TabsContent value="password">
                    <div class="flex flex-col gap-4 rounded-xl border border-line p-5">
                        <Input label="Password lama" type="password" />
                        <Input label="Password baru" type="password" />
                    </div>
                </TabsContent>
            </Tabs>
        </DocExample>

        <DocExample title="Line + v-model" description="Garis bawah seperti tab halaman, dengan tab nonaktif." :code="lineCode">
            <div class="flex w-full max-w-md flex-col gap-3">
                <Tabs v-model="activeTab">
                    <TabsList variant="line">
                        <TabsTrigger value="inbox">Surat masuk</TabsTrigger>
                        <TabsTrigger value="outbox">Surat keluar</TabsTrigger>
                        <TabsTrigger value="archive" disabled>Arsip</TabsTrigger>
                    </TabsList>

                    <TabsContent value="inbox" class="text-sm text-muted">Daftar surat yang masuk akan tampil di sini.</TabsContent>
                    <TabsContent value="outbox" class="text-sm text-muted">Daftar surat yang dikirim akan tampil di sini.</TabsContent>
                </Tabs>

                <p class="text-sm text-muted">Tab aktif: {{ activeTab }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Tabs"
            :rows="[
                { name: 'v-model', type: 'string | number', description: 'Tab yang aktif.' },
                { name: 'default-value', type: 'string | number', description: 'Tab aktif awal, tanpa v-model.' },
                { name: 'orientation', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Arah susunan tab dan tombol panah.' },
                { name: 'activation-mode', type: `'automatic' | 'manual'`, default: `'automatic'`, description: 'Pindah tab saat fokus, atau hanya saat diklik/Enter.' },
            ]"
        />

        <DocProps
            title="TabsList"
            :rows="[{ name: 'variant', type: `'segmented' | 'line'`, default: `'segmented'`, description: 'Gaya daftar tab.' }]"
        />

        <DocProps
            title="TabsTrigger"
            :rows="[
                { name: 'value', type: 'string | number', description: 'Nilai tab (wajib).' },
                { name: 'icon', type: 'string', description: 'Nama ikon di depan label.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan tab.' },
            ]"
        />

        <DocProps title="TabsContent" :rows="[{ name: 'value', type: 'string | number', description: 'Tab yang menampilkan panel ini (wajib).' }]" />
    </DocPage>
</template>
