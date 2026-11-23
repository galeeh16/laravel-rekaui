<script setup>
import { reactive, ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Switch from '@/components/ui/Switch.vue';
import { componentLayout } from '@/lib/docs';

defineOptions({ layout: componentLayout });

const autoArchive = ref(true);

const settings = reactive({
    emailNotification: true,
    deadlineReminder: true,
    weeklySummary: false,
});

const isCompact = ref(false);

const basicCode = `
<script setup>
import { ref } from 'vue';
import Switch from '@/components/ui/Switch.vue';

const autoArchive = ref(true);
<\/script>

<template>
    <Switch v-model="autoArchive">Arsipkan otomatis setelah selesai</Switch>

    <p>Status: {{ autoArchive ? 'Aktif' : 'Nonaktif' }}</p>
</template>
`;

const settingsCode = `
<script setup>
import { reactive } from 'vue';
import Switch from '@/components/ui/Switch.vue';

const settings = reactive({
    emailNotification: true,
    deadlineReminder: true,
    weeklySummary: false,
});
<\/script>

<template>
    <div class="divide-y divide-line rounded-xl border border-line">
        <Switch v-model="settings.emailNotification" label-position="start" hint="Setiap ada surat masuk atau disposisi baru." class="px-4 py-3">
            Notifikasi email
        </Switch>
        <Switch v-model="settings.deadlineReminder" label-position="start" hint="Satu hari sebelum tenggat tindak lanjut." class="px-4 py-3">
            Pengingat tenggat disposisi
        </Switch>
        <Switch v-model="settings.weeklySummary" label-position="start" hint="Dikirim setiap Senin pagi." class="px-4 py-3">
            Ringkasan mingguan
        </Switch>
    </div>
</template>
`;

const variantsCode = `
<template>
    <Switch v-model="isCompact" size="sm">Tampilan ringkas</Switch>
    <Switch v-model="isCompact">Tampilan ringkas</Switch>
    <Switch :model-value="true" disabled>Enkripsi lampiran (wajib)</Switch>

    <!-- Without visible text, give it an aria-label -->
    <Switch v-model="isCompact" aria-label="Tampilan ringkas" />
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Dasar" description="v-model berupa boolean; teks di slot default menjadi label yang bisa diklik." :code="basicCode">
            <div class="flex flex-col items-center gap-4">
                <Switch v-model="autoArchive">Arsipkan otomatis setelah selesai</Switch>

                <p class="text-sm text-muted">Status: {{ autoArchive ? 'Aktif' : 'Nonaktif' }}</p>
            </div>
        </DocExample>

        <DocExample
            title="Daftar pengaturan"
            description="label-position=&quot;start&quot; menaruh teks di kiri dan switch di kanan selebar baris, ditambah hint."
            :code="settingsCode"
        >
            <div class="flex w-full max-w-md flex-col gap-3">
                <div class="divide-y divide-line rounded-xl border border-line">
                    <Switch
                        v-model="settings.emailNotification"
                        label-position="start"
                        hint="Setiap ada surat masuk atau disposisi baru."
                        class="px-4 py-3"
                    >
                        Notifikasi email
                    </Switch>
                    <Switch
                        v-model="settings.deadlineReminder"
                        label-position="start"
                        hint="Satu hari sebelum tenggat tindak lanjut."
                        class="px-4 py-3"
                    >
                        Pengingat tenggat disposisi
                    </Switch>
                    <Switch v-model="settings.weeklySummary" label-position="start" hint="Dikirim setiap Senin pagi." class="px-4 py-3">
                        Ringkasan mingguan
                    </Switch>
                </div>

                <pre class="rounded-xl bg-ink/5 p-3 font-mono text-xs text-muted">{{ settings }}</pre>
            </div>
        </DocExample>

        <DocExample title="Ukuran & status" description="Ukuran sm dan md, switch nonaktif, dan switch tanpa teks dengan aria-label." :code="variantsCode">
            <div class="flex flex-col gap-4">
                <Switch v-model="isCompact" size="sm">Tampilan ringkas</Switch>
                <Switch v-model="isCompact">Tampilan ringkas</Switch>
                <Switch :model-value="true" disabled>Enkripsi lampiran (wajib)</Switch>
                <Switch v-model="isCompact" aria-label="Tampilan ringkas" />
            </div>
        </DocExample>

        <DocProps
            title="Switch"
            :rows="[
                { name: 'v-model', type: 'boolean', default: 'false', description: 'Status nyala/mati.' },
                { name: 'size', type: `'sm' | 'md'`, default: `'md'`, description: 'Ukuran switch.' },
                { name: 'hint', type: 'string', description: 'Teks bantuan abu-abu di bawah label.' },
                { name: 'label-position', type: `'end' | 'start'`, default: `'end'`, description: 'end: teks setelah switch. start: teks di kiri, switch di ujung kanan.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan switch.' },
                { name: 'name', type: 'string', description: 'Nama field untuk submit form biasa.' },
                { name: 'aria-label', type: 'string', description: 'Wajib diisi bila tidak ada teks label.' },
                { name: 'slot default', type: '—', description: 'Teks label yang bisa diklik.' },
            ]"
        />
    </DocPage>
</template>
