<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Avatar from '@/components/ui/Avatar.vue';
import Badge from '@/components/ui/Badge.vue';
import HoverCard from '@/components/ui/HoverCard.vue';
import Icon from '@/components/ui/Icon.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const contacts = [
    { name: 'Rina Kartika', role: 'Sekretaris', company: 'PT Sinar Mandiri', email: 'rina@sinarmandiri.co.id', letters: 4 },
    { name: 'Bagus Pratama', role: 'Direktur', company: 'CV Arunika Desain', email: 'bagus@arunika.id', letters: 2 },
    { name: 'Anisa Rahma', role: 'Ketua Yayasan', company: 'Yayasan Pelita Harapan', email: 'anisa@pelitaharapan.or.id', letters: 1 },
];

/* Example 2: v-model:open */
const isPreviewOpen = ref(false);

const basicCode = `
<script setup>
import Avatar from '@/components/ui/Avatar.vue';
import HoverCard from '@/components/ui/HoverCard.vue';
<\/script>

<template>
    <p>
        Surat untuk
        <HoverCard>
            <template #trigger>
                <a href="#" class="font-medium text-brand hover:underline">Rina Kartika</a>
            </template>

            <div class="flex gap-3">
                <Avatar name="Rina Kartika" size="lg" />
                <div>
                    <p class="font-semibold">Rina Kartika</p>
                    <p class="text-sm text-muted">Sekretaris · PT Sinar Mandiri</p>
                </div>
            </div>
        </HoverCard>
        sudah dikirim.
    </p>
</template>
`;

const previewCode = `
<script setup>
import { ref } from 'vue';

const isPreviewOpen = ref(false);
<\/script>

<template>
    <HoverCard v-model:open="isPreviewOpen" side="right" :open-delay="200" width="w-72">
        <template #trigger>
            <a href="#">012/SK/XI/2026</a>
        </template>

        ...ringkasan surat...
    </HoverCard>

    <p>Pratinjau {{ isPreviewOpen ? 'terbuka' : 'tertutup' }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Arahkan kursor (atau fokus dengan Tab) ke nama kontak. Kartu tetap terbuka selama kursor berada di atasnya."
            :code="basicCode"
        >
            <ul class="flex w-full max-w-md flex-col divide-y divide-line rounded-xl border border-line text-sm">
                <li v-for="contact in contacts" :key="contact.email" class="flex items-center justify-between gap-3 px-4 py-3">
                    <span>
                        Surat untuk
                        <HoverCard>
                            <template #trigger>
                                <a
                                    :href="`mailto:${contact.email}`"
                                    class="rounded-sm font-medium text-brand hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                                >
                                    {{ contact.name }}
                                </a>
                            </template>

                            <div class="flex gap-3">
                                <Avatar :name="contact.name" size="lg" />
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold">{{ contact.name }}</p>
                                    <p class="text-sm text-muted">{{ contact.role }} · {{ contact.company }}</p>

                                    <p class="mt-3 flex items-center gap-2 text-sm text-muted">
                                        <Icon name="mail" class="size-4" />
                                        <span class="truncate">{{ contact.email }}</span>
                                    </p>
                                    <p class="mt-1.5 flex items-center gap-2 text-sm text-muted">
                                        <Icon name="file-text" class="size-4" />
                                        {{ contact.letters }} surat aktif
                                    </p>
                                </div>
                            </div>
                        </HoverCard>
                    </span>
                    <Badge variant="success">Terkirim</Badge>
                </li>
            </ul>
        </DocExample>

        <DocExample
            title="Posisi + v-model:open"
            description="Muncul di kanan dengan jeda lebih singkat; status terbuka bisa dibaca lewat v-model:open."
            :code="previewCode"
        >
            <div class="flex flex-col items-center gap-3">
                <HoverCard v-model:open="isPreviewOpen" side="right" :open-delay="200" width="w-72">
                    <template #trigger>
                        <a
                            href="#"
                            class="inline-flex items-center gap-2 rounded-lg border border-line px-3 py-2 text-sm font-medium shadow-xs hover:bg-canvas focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                            @click.prevent
                        >
                            <Icon name="file-text" class="size-4 text-muted" />
                            012/SK/XI/2026
                        </a>
                    </template>

                    <div class="flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-semibold">Pemberitahuan perubahan alamat</p>
                            <Badge variant="info" dot>Menunggu</Badge>
                        </div>
                        <p class="text-sm text-muted">
                            Kepada PT Sinar Mandiri, dikirim 6 November 2026. Jatuh tempo balasan besok.
                        </p>
                        <p class="flex items-center gap-2 text-xs text-muted">
                            <Icon name="clock" class="size-3.5" />
                            Diperbarui 2 jam lalu
                        </p>
                    </div>
                </HoverCard>

                <p class="text-sm text-muted">Pratinjau {{ isPreviewOpen ? 'terbuka' : 'tertutup' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="HoverCard"
            :rows="[
                { name: 'v-model:open', type: 'boolean', default: 'false', description: 'Status terbuka kartu.' },
                { name: 'open-delay', type: 'number', default: '500', description: 'Jeda (ms) sebelum kartu muncul.' },
                { name: 'close-delay', type: 'number', default: '200', description: 'Jeda (ms) sebelum kartu hilang setelah kursor keluar.' },
                { name: 'side', type: `'top' | 'right' | 'bottom' | 'left'`, default: `'bottom'`, description: 'Sisi trigger tempat kartu muncul.' },
                { name: 'align', type: `'start' | 'center' | 'end'`, default: `'center'`, description: 'Perataan terhadap trigger.' },
                { name: 'width', type: 'string', default: `'w-80'`, description: 'Class lebar kartu.' },
                { name: '#trigger', type: 'slot', description: 'Link atau elemen yang bisa difokus.' },
                { name: '#default', type: 'slot', description: 'Isi kartu. Tidak muncul di layar sentuh, jadi jangan taruh info penting saja di sini.' },
            ]"
        />
    </DocPage>
</template>
