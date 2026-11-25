<script setup>
import { ref } from 'vue';
import DocExample from '@/components/docs/DocExample.vue';
import DocPage from '@/components/docs/DocPage.vue';
import DocProps from '@/components/docs/DocProps.vue';
import Accordion from '@/components/ui/Accordion.vue';
import AccordionItem from '@/components/ui/AccordionItem.vue';

const faqs = [
    { value: 'item-1', title: 'Apa itu reka-ui?', answer: 'Library komponen Vue tanpa gaya bawaan (headless) yang sudah menangani aksesibilitas dan keyboard.' },
    { value: 'item-2', title: 'Kenapa dibungkus lagi?', answer: 'Supaya gaya, warna brand, dan dark mode konsisten di seluruh aplikasi tanpa menulis ulang class.' },
    { value: 'item-3', title: 'Apakah bisa dipakai di Inertia?', answer: 'Bisa. Komponen ini hanya Vue biasa, jadi bisa dipakai di page maupun layout Inertia.' },
];

const openPanels = ref(['item-1']);

const basicCode = `
<script setup>
import Accordion from '@/components/ui/Accordion.vue';
import AccordionItem from '@/components/ui/AccordionItem.vue';
<\/script>

<template>
    <Accordion default-value="item-1">
        <AccordionItem value="item-1" title="Apa itu reka-ui?">
            Library komponen Vue tanpa gaya bawaan (headless)...
        </AccordionItem>
        <AccordionItem value="item-2" title="Kenapa dibungkus lagi?">
            Supaya gaya, warna brand, dan dark mode konsisten...
        </AccordionItem>
    </Accordion>
</template>
`;

const multipleCode = `
<script setup>
import { ref } from 'vue';
import Accordion from '@/components/ui/Accordion.vue';
import AccordionItem from '@/components/ui/AccordionItem.vue';

const openPanels = ref(['item-1']);
<\/script>

<template>
    <Accordion v-model="openPanels" type="multiple">
        <AccordionItem value="item-1" title="Panel pertama">...</AccordionItem>
        <AccordionItem value="item-2" title="Panel kedua">...</AccordionItem>
    </Accordion>

    <p>Terbuka: {{ openPanels.join(', ') }}</p>
</template>
`;
</script>

<template>
    <DocPage>
        <DocExample title="Dasar" description="Satu panel terbuka dalam satu waktu; panel yang terbuka bisa ditutup lagi." :code="basicCode">
            <Accordion default-value="item-1" class="max-w-lg">
                <AccordionItem v-for="faq in faqs" :key="faq.value" :value="faq.value" :title="faq.title">
                    {{ faq.answer }}
                </AccordionItem>
            </Accordion>
        </DocExample>

        <DocExample
            title="Banyak panel terbuka"
            description="Dengan type=&quot;multiple&quot; dan v-model berupa array."
            :code="multipleCode"
        >
            <div class="flex w-full max-w-lg flex-col gap-3">
                <Accordion v-model="openPanels" type="multiple">
                    <AccordionItem v-for="faq in faqs" :key="faq.value" :value="faq.value" :title="faq.title">
                        {{ faq.answer }}
                    </AccordionItem>
                </Accordion>

                <p class="text-sm text-muted">Terbuka: {{ openPanels.join(', ') || '—' }}</p>
            </div>
        </DocExample>

        <DocProps
            title="Accordion"
            :rows="[
                { name: 'v-model', type: 'string | string[]', description: 'Panel yang terbuka (array untuk type multiple).' },
                { name: 'type', type: `'single' | 'multiple'`, default: `'single'`, description: 'Satu atau banyak panel boleh terbuka.' },
                { name: 'collapsible', type: 'boolean', default: 'true', description: 'Mode single: panel yang terbuka bisa ditutup.' },
                { name: 'default-value', type: 'string | string[]', description: 'Panel yang terbuka awalnya, tanpa v-model.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan semua panel.' },
            ]"
        />

        <DocProps
            title="AccordionItem"
            :rows="[
                { name: 'value', type: 'string', description: 'Identitas unik panel (wajib).' },
                { name: 'title', type: 'string', description: 'Judul panel; bisa diganti dengan slot #title.' },
                { name: 'disabled', type: 'boolean', default: 'false', description: 'Nonaktifkan panel ini.' },
            ]"
        />
    </DocPage>
</template>
