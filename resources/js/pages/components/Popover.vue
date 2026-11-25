<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Popover from '@/components/ui/Popover.vue';
import Switch from '@/components/ui/Switch.vue';

/* Example 1: share settings with reactive switches */
const shareSettings = ref({ allowComments: true, notifyRecipient: false });

/* Example 2: quick reminder form, closed through the slot's close() */
const isReminderOpen = ref(false);
const reminderDays = ref('3');
const reminder = ref(null);

function saveReminder(close) {
    reminder.value = `Pengingat dikirim ${reminderDays.value} hari sebelum tenggat.`;
    close();
}

const basicCode = `
<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Popover from '@/components/ui/Popover.vue';
import Switch from '@/components/ui/Switch.vue';

const shareSettings = ref({ allowComments: true, notifyRecipient: false });
<\/script>

<template>
    <Popover width="w-80">
        <template #trigger>
            <Button variant="secondary" icon="link">Bagikan</Button>
        </template>

        <h3>Bagikan surat</h3>
        <label>
            Izinkan komentar
            <Switch v-model="shareSettings.allowComments" />
        </label>
        <label>
            Beri tahu penerima
            <Switch v-model="shareSettings.notifyRecipient" />
        </label>
    </Popover>
</template>
`;

const formCode = `
<script setup>
import { ref } from 'vue';

const isOpen = ref(false);
const reminderDays = ref('3');
<\/script>

<template>
    <Popover v-model:open="isOpen" side="top" align="start">
        <template #trigger>
            <Button variant="soft" icon="bell">Atur pengingat</Button>
        </template>

        <template #default="{ close }">
            <form @submit.prevent="save(); close()">
                <Input v-model="reminderDays" type="number" min="1" label="Hari sebelum tenggat" />
                <Button type="submit" size="sm">Simpan</Button>
            </form>
        </template>
    </Popover>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample
            title="Dasar"
            description="Klik tombol untuk membuka; klik di luar atau Escape untuk menutup. Isi popover langsung mengubah state."
            :code="basicCode"
        >
            <div class="flex flex-col items-center gap-4">
                <Popover width="w-80">
                    <template #trigger>
                        <Button variant="secondary" icon="link">Bagikan</Button>
                    </template>

                    <div class="flex flex-col gap-4">
                        <div>
                            <h3 class="text-sm font-semibold">Bagikan surat</h3>
                            <p class="mt-0.5 text-xs text-muted">012/SK/XI/2026 · Pemberitahuan perubahan alamat</p>
                        </div>

                        <label class="flex items-center justify-between gap-4 text-sm">
                            Izinkan komentar
                            <Switch v-model="shareSettings.allowComments" />
                        </label>

                        <label class="flex items-center justify-between gap-4 text-sm">
                            Beri tahu penerima
                            <Switch v-model="shareSettings.notifyRecipient" />
                        </label>
                    </div>
                </Popover>

                <p class="text-sm text-muted">
                    Komentar {{ shareSettings.allowComments ? 'diizinkan' : 'dimatikan' }} · penerima
                    {{ shareSettings.notifyRecipient ? 'diberi tahu' : 'tidak diberi tahu' }}
                </p>
            </div>
        </DocExample>

        <DocExample
            title="Form + v-model:open"
            description="Slot default menerima close() untuk menutup popover setelah form disimpan. Posisi diatur dengan side dan align."
            :code="formCode"
        >
            <div class="flex flex-col items-center gap-4">
                <Popover v-model:open="isReminderOpen" side="top" align="start">
                    <template #trigger>
                        <Button variant="soft" icon="bell">Atur pengingat</Button>
                    </template>

                    <template #default="{ close }">
                        <form class="flex flex-col gap-3" @submit.prevent="saveReminder(close)">
                            <Input v-model="reminderDays" type="number" min="1" max="30" label="Hari sebelum tenggat" />
                            <div class="flex justify-end gap-2">
                                <Button variant="secondary" size="sm" @click="close">Batal</Button>
                                <Button type="submit" size="sm">Simpan</Button>
                            </div>
                        </form>
                    </template>
                </Popover>

                <p class="text-sm text-muted">
                    {{ reminder ?? 'Belum ada pengingat.' }} Popover {{ isReminderOpen ? 'terbuka' : 'tertutup' }}.
                </p>
            </div>
        </DocExample>

        <DocProps
            title="Popover"
            :rows="[
                { name: 'v-model:open', type: 'boolean', default: 'false', description: 'Status terbuka popover.' },
                { name: 'side', type: `'top' | 'right' | 'bottom' | 'left'`, default: `'bottom'`, description: 'Sisi trigger tempat popover muncul (otomatis pindah bila tidak muat).' },
                { name: 'align', type: `'start' | 'center' | 'end'`, default: `'center'`, description: 'Perataan terhadap trigger.' },
                { name: 'width', type: 'string', default: `'w-72'`, description: 'Class lebar konten.' },
                { name: 'modal', type: 'boolean', default: 'false', description: 'Kunci fokus di dalam popover dan nonaktifkan halaman di belakangnya.' },
                { name: '#trigger', type: 'slot', description: 'Elemen pembuka (satu elemen, biasanya Button).' },
                { name: '#default', type: 'slot { close }', description: 'Isi popover.' },
            ]"
        />
    </DocPage>
</template>
