<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Avatar from '@/components/ui/Avatar.vue';
import AvatarGroup from '@/components/ui/AvatarGroup.vue';
import Button from '@/components/ui/Button.vue';

/** Inline SVG "photo" so the demo needs no external image host. */
function portrait({ background, skin, hair, shirt }) {
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80"><rect width="80" height="80" fill="${background}"/><path d="M14 80c1-17 12-27 26-27s25 10 26 27z" fill="${shirt}"/><circle cx="40" cy="35" r="15" fill="${skin}"/><path d="M24 34c0-11 7-18 16-18s16 7 16 18c-4-6-9-8-16-8s-12 2-16 8z" fill="${hair}"/></svg>`;

    return `data:image/svg+xml,${encodeURIComponent(svg)}`;
}

const rinaPhoto = portrait({ background: '#ddeee9', skin: '#e3b48f', hair: '#2b2118', shirt: '#17795a' });
const budiPhoto = portrait({ background: '#dfe4ec', skin: '#c68e6a', hair: '#1d1d1b', shirt: '#2d5bb0' });
const dewiPhoto = portrait({ background: '#f8e3d4', skin: '#d9a47c', hair: '#4a2f1d', shirt: '#c03a26' });

/** Deliberately unreadable image (no network request): the fallback initials are shown instead. */
const brokenPhoto = 'data:image/png;base64,AAAA';

const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
const names = ['Rina Kartika', 'Budi Santoso', 'Siti Rahmawati', 'Agus Prasetyo', 'Dewi Lestari'];

const simulateBrokenImage = ref(false);

const team = [
    { name: 'Rina Kartika', src: rinaPhoto },
    { name: 'Budi Santoso', src: budiPhoto },
    { name: 'Siti Rahmawati' },
    { name: 'Dewi Lestari', src: dewiPhoto },
    { name: 'Agus Prasetyo' },
    { name: 'Hendra Wijaya' },
    { name: 'Maya Putri' },
];

const initialsCode = `
<script setup>
import Avatar from '@/components/ui/Avatar.vue';
<\/script>

<template>
    <Avatar name="Rina Kartika" size="xs" />
    <Avatar name="Rina Kartika" size="sm" />
    <Avatar name="Rina Kartika" />
    <Avatar name="Rina Kartika" size="lg" />
    <Avatar name="Rina Kartika" size="xl" />

    <!-- Warna dipilih konsisten dari nama -->
    <Avatar name="Budi Santoso" />
    <Avatar name="Siti Rahmawati" />
</template>
`;

const imageCode = `
<script setup>
import { ref } from 'vue';
import Avatar from '@/components/ui/Avatar.vue';

const isBroken = ref(false);
<\/script>

<template>
    <Avatar name="Rina Kartika" src="/storage/avatars/rina.jpg" size="xl" />

    <!-- Gagal dimuat: otomatis kembali ke inisial -->
    <Avatar name="Agus Prasetyo" src="/storage/avatars/tidak-ada.jpg" size="xl" />

    <!-- src reaktif -->
    <Avatar
        name="Budi Santoso"
        :src="isBroken ? '/storage/avatars/tidak-ada.jpg' : '/storage/avatars/budi.jpg'"
        alt="Budi Santoso"
        size="xl"
    />
    <Button variant="secondary" size="sm" @click="isBroken = !isBroken">Ganti foto</Button>
</template>
`;

const groupCode = `
<script setup>
import AvatarGroup from '@/components/ui/AvatarGroup.vue';

const team = [
    { name: 'Rina Kartika', src: '/storage/avatars/rina.jpg' },
    { name: 'Budi Santoso', src: '/storage/avatars/budi.jpg' },
    { name: 'Siti Rahmawati' },
    // ...
];
<\/script>

<template>
    <AvatarGroup :people="team" :max="4" />
    <AvatarGroup :people="team" :max="3" size="sm" />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Inisial & ukuran"
            description="Tanpa gambar, avatar menampilkan dua huruf awal dengan warna yang konsisten per nama."
            :code="initialsCode"
        >
            <div class="flex flex-col items-center gap-6">
                <div class="flex items-end gap-3">
                    <Avatar v-for="size in sizes" :key="size" name="Rina Kartika" :size="size" />
                </div>
                <div class="flex items-center gap-3">
                    <Avatar v-for="name in names" :key="name" :name="name" />
                </div>
            </div>
        </DocExample>

        <DocExample
            title="Gambar & fallback"
            description="Gambar tampil setelah termuat; jika gagal, inisial dipakai sebagai fallback."
            :code="imageCode"
        >
            <div class="flex flex-col items-center gap-6">
                <div class="flex items-start gap-8">
                    <figure class="flex flex-col items-center gap-2 text-center">
                        <Avatar name="Rina Kartika" :src="rinaPhoto" size="xl" />
                        <figcaption class="text-xs text-muted">Termuat</figcaption>
                    </figure>
                    <figure class="flex flex-col items-center gap-2 text-center">
                        <Avatar name="Agus Prasetyo" :src="brokenPhoto" size="xl" />
                        <figcaption class="text-xs text-muted">Gagal dimuat</figcaption>
                    </figure>
                    <figure class="flex flex-col items-center gap-2 text-center">
                        <Avatar name="Budi Santoso" :src="simulateBrokenImage ? brokenPhoto : budiPhoto" alt="Budi Santoso" size="xl" />
                        <figcaption class="text-xs text-muted">{{ simulateBrokenImage ? 'URL rusak' : 'URL valid' }}</figcaption>
                    </figure>
                </div>

                <Button variant="secondary" size="sm" icon="image" @click="simulateBrokenImage = !simulateBrokenImage">
                    {{ simulateBrokenImage ? 'Pakai foto valid' : 'Simulasikan foto rusak' }}
                </Button>
            </div>
        </DocExample>

        <DocExample
            title="AvatarGroup"
            description="Tim penerima disposisi: avatar bertumpuk dengan sisa ditampilkan sebagai +N."
            :code="groupCode"
        >
            <div class="flex flex-col items-center gap-6">
                <AvatarGroup :people="team" :max="4" />
                <AvatarGroup :people="team" :max="3" size="sm" />
            </div>
        </DocExample>

        <DocProps
            title="Avatar"
            :rows="[
                { name: 'name', type: 'string', description: 'Nama lengkap; dipakai untuk inisial dan warna (wajib).' },
                { name: 'src', type: 'string', description: 'URL foto. Inisial tampil selama memuat atau jika gagal.' },
                { name: 'alt', type: 'string', description: 'Teks alternatif. Tanpa alt, avatar dianggap dekoratif (aria-hidden).' },
                { name: 'size', type: `'xs' | 'sm' | 'md' | 'lg' | 'xl'`, default: `'md'`, description: 'Ukuran avatar.' },
            ]"
        />

        <DocProps
            title="AvatarGroup"
            :rows="[
                { name: 'people', type: 'Array<{ name, src?, id? }>', description: 'Daftar orang yang ditampilkan (wajib).' },
                { name: 'max', type: 'number', default: '4', description: 'Jumlah avatar yang terlihat; sisanya jadi +N.' },
                { name: 'size', type: `'xs' | 'sm' | 'md' | 'lg' | 'xl'`, default: `'md'`, description: 'Ukuran semua avatar.' },
            ]"
        />
    </DocPage>
</template>
