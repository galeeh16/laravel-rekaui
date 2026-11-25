<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import Switch from '@/components/ui/Switch.vue';
import Tooltip from '@/components/ui/Tooltip.vue';

const letterActions = [
    { label: 'Lihat surat', icon: 'eye' },
    { label: 'Teruskan', icon: 'forward' },
    { label: 'Unduh PDF', icon: 'download' },
    { label: 'Arsipkan', icon: 'archive' },
];

const placements = [
    { side: 'top', label: 'Atas', kbd: '⌘↑' },
    { side: 'right', label: 'Kanan', kbd: '⌘→' },
    { side: 'bottom', label: 'Bawah', kbd: '⌘↓' },
    { side: 'left', label: 'Kiri', kbd: '⌘←' },
];

/* Example 3: controlled hint */
const isHintOpen = ref(false);

const basicCode = `
<script setup>
import Button from '@/components/ui/Button.vue';
import Tooltip from '@/components/ui/Tooltip.vue';
<\/script>

<template>
    <Tooltip content="Teruskan">
        <Button variant="ghost" size="icon-md" icon="forward" aria-label="Teruskan" />
    </Tooltip>

    <Tooltip content="Unduh PDF">
        <Button variant="ghost" size="icon-md" icon="download" aria-label="Unduh PDF" />
    </Tooltip>
</template>
`;

const placementCode = `
<template>
    <Tooltip content="Cari surat" kbd="⌘K" side="bottom">
        <Button variant="secondary" icon="search">Cari</Button>
    </Tooltip>

    <Tooltip side="right">
        <Button variant="secondary">Kanan</Button>

        <template #content>
            <strong>Isi kaya</strong> lewat slot #content
        </template>
    </Tooltip>
</template>
`;

const controlledCode = `
<script setup>
import { ref } from 'vue';
import Switch from '@/components/ui/Switch.vue';

const isHintOpen = ref(false);
<\/script>

<template>
    <Switch v-model="isHintOpen" aria-label="Tampilkan petunjuk" />

    <Tooltip v-model:open="isHintOpen" content="Klik di sini untuk mengirim surat ke penerima" side="bottom">
        <Button icon="send">Kirim</Button>
    </Tooltip>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Muncul saat kursor diarahkan atau tombol difokus dengan Tab. Tetap berikan aria-label pada tombol ikon."
            :code="basicCode"
        >
            <div class="flex items-center gap-1 rounded-xl border border-line p-1 shadow-xs">
                <Tooltip v-for="action in letterActions" :key="action.label" :content="action.label">
                    <Button variant="ghost" size="icon-md" :icon="action.icon" :aria-label="action.label" />
                </Tooltip>

                <Tooltip content="Hapus surat">
                    <Button variant="ghost" size="icon-md" icon="trash" aria-label="Hapus surat" />
                </Tooltip>
            </div>
        </DocExample>

        <DocExample
            title="Posisi, shortcut, dan isi kaya"
            description="Atur sisi dengan side, tampilkan pintasan dengan kbd, atau pakai slot #content."
            :code="placementCode"
        >
            <div class="flex flex-wrap justify-center gap-2">
                <Tooltip v-for="placement in placements" :key="placement.side" :side="placement.side" :kbd="placement.kbd" :content="placement.label">
                    <Button variant="secondary">{{ placement.label }}</Button>
                </Tooltip>

                <Tooltip side="bottom">
                    <Button variant="secondary" icon="info">Isi kaya</Button>

                    <template #content>
                        <span><strong class="font-semibold">Tenggat:</strong> balasan paling lambat 30 hari.</span>
                    </template>
                </Tooltip>
            </div>
        </DocExample>

        <DocExample
            title="Dikontrol dengan v-model:open"
            description="Berguna untuk petunjuk onboarding: tooltip dibuka dari state, bukan dari hover."
            :code="controlledCode"
        >
            <div class="flex flex-col items-center gap-6">
                <label class="flex items-center gap-3 text-sm">
                    <Switch v-model="isHintOpen" aria-label="Tampilkan petunjuk" />
                    Tampilkan petunjuk
                </label>

                <Tooltip v-model:open="isHintOpen" content="Klik di sini untuk mengirim surat ke penerima" side="bottom">
                    <Button icon="send">Kirim</Button>
                </Tooltip>
            </div>
        </DocExample>

        <DocProps
            title="Tooltip"
            :rows="[
                { name: 'content', type: 'string', description: 'Teks tooltip; ganti dengan slot #content untuk isi kaya.' },
                { name: 'kbd', type: 'string', description: 'Pintasan keyboard di sebelah teks, mis. ⌘K.' },
                { name: 'v-model:open', type: 'boolean', description: 'Status terbuka; tanpa v-model tooltip mengikuti hover/fokus.' },
                { name: 'side', type: `'top' | 'right' | 'bottom' | 'left'`, default: `'top'`, description: 'Sisi trigger tempat tooltip muncul.' },
                { name: 'align', type: `'start' | 'center' | 'end'`, default: `'center'`, description: 'Perataan terhadap trigger.' },
                { name: 'delay-duration', type: 'number', default: '300', description: 'Jeda (ms) sebelum tooltip muncul saat hover.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Matikan tooltip.' },
                { name: '#default', type: 'slot', description: 'Elemen pemicu (satu elemen yang bisa difokus).' },
                { name: '#content', type: 'slot', description: 'Isi tooltip kustom.' },
            ]"
        />
    </DocPage>
</template>
