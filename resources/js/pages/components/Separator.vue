<script setup>
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Separator from '@/components/ui/Separator.vue';

const letterGroups = [
    {
        label: 'Hari ini',
        letters: [
            { subject: 'Undangan Rapat Koordinasi', sender: 'Dinas Pendidikan', time: '09.12' },
            { subject: 'Permohonan Data Pegawai', sender: 'Badan Kepegawaian Daerah', time: '08.40' },
        ],
    },
    {
        label: 'Kemarin',
        letters: [{ subject: 'Laporan Realisasi Anggaran', sender: 'Bagian Keuangan', time: '16.05' }],
    },
];

const horizontalCode = `
<script setup>
import Separator from '@/components/ui/Separator.vue';
<\/script>

<template>
    <div>
        <h3>Undangan Rapat Koordinasi Akhir Tahun</h3>
        <p>Dinas Pendidikan Provinsi</p>
    </div>

    <Separator class="my-4" />

    <p>Dengan hormat, kami mengundang Bapak/Ibu...</p>
</template>
`;

const verticalCode = `
<template>
    <!-- Separator vertikal mengikuti tinggi parent flex -->
    <div class="flex h-5 items-center gap-3 text-sm">
        <span>045/UM/XI/2026</span>
        <Separator orientation="vertical" decorative />
        <span>Rahasia</span>
        <Separator orientation="vertical" decorative />
        <span>3 lampiran</span>
    </div>
</template>
`;

const labelCode = `
<template>
    <Separator label="Hari ini" />
    <!-- ...daftar surat... -->

    <Separator label="Kemarin" />
    <!-- ...daftar surat... -->
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Horizontal" description="Memisahkan kepala surat dari isinya." :code="horizontalCode">
            <div class="w-full max-w-md rounded-2xl border border-line p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-ink">Undangan Rapat Koordinasi Akhir Tahun</h3>
                        <p class="mt-0.5 text-sm text-muted">Dinas Pendidikan Provinsi</p>
                    </div>
                    <Badge variant="info">Masuk</Badge>
                </div>

                <Separator class="my-4" />

                <p class="text-sm leading-relaxed text-ink/80">
                    Dengan hormat, kami mengundang Bapak/Ibu untuk menghadiri rapat koordinasi akhir tahun pada
                    Jumat, 28 November 2026 pukul 09.00 WIB di Aula Utama.
                </p>
            </div>
        </DocExample>

        <DocExample
            title="Vertikal"
            description="Di antara metadata atau tombol toolbar; tingginya mengikuti parent flex."
            :code="verticalCode"
        >
            <div class="flex flex-col items-center gap-6">
                <div class="flex h-5 items-center gap-3 text-sm text-muted">
                    <span class="font-medium text-ink">045/UM/XI/2026</span>
                    <Separator orientation="vertical" decorative />
                    <span>Rahasia</span>
                    <Separator orientation="vertical" decorative />
                    <span>3 lampiran</span>
                    <Separator orientation="vertical" decorative />
                    <span>20 Nov 2026</span>
                </div>

                <div class="flex h-10 items-center gap-1 rounded-xl border border-line px-1">
                    <Button variant="ghost" size="icon-sm" icon="bold" aria-label="Tebal" />
                    <Button variant="ghost" size="icon-sm" icon="italic" aria-label="Miring" />
                    <Button variant="ghost" size="icon-sm" icon="underline" aria-label="Garis bawah" />
                    <Separator orientation="vertical" class="mx-1 my-2" />
                    <Button variant="ghost" size="icon-sm" icon="align-left" aria-label="Rata kiri" />
                    <Button variant="ghost" size="icon-sm" icon="align-center" aria-label="Rata tengah" />
                    <Button variant="ghost" size="icon-sm" icon="align-right" aria-label="Rata kanan" />
                </div>
            </div>
        </DocExample>

        <DocExample title="Dengan label" description="Teks di tengah garis, mis. untuk mengelompokkan surat per hari." :code="labelCode">
            <div class="flex w-full max-w-md flex-col gap-3">
                <template v-for="group in letterGroups" :key="group.label">
                    <Separator :label="group.label" />
                    <ul class="flex flex-col gap-1">
                        <li
                            v-for="letter in group.letters"
                            :key="letter.subject"
                            class="flex items-center justify-between gap-3 rounded-xl px-3 py-2 text-sm hover:bg-ink/5"
                        >
                            <span class="flex min-w-0 flex-col">
                                <span class="truncate font-medium text-ink">{{ letter.subject }}</span>
                                <span class="truncate text-xs text-muted">{{ letter.sender }}</span>
                            </span>
                            <span class="text-xs text-muted tabular-nums">{{ letter.time }}</span>
                        </li>
                    </ul>
                </template>
            </div>
        </DocExample>

        <DocProps
            title="Separator"
            :rows="[
                { name: 'orientation', type: `'horizontal' | 'vertical'`, default: `'horizontal'`, description: 'Arah garis.' },
                { name: 'decorative', type: 'boolean', default: 'false', description: 'Hanya visual; disembunyikan dari pembaca layar.' },
                { name: 'label', type: 'string', description: 'Teks di tengah garis (atau slot default).' },
            ]"
        />
    </DocPage>
</template>
