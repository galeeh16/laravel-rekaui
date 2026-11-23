<script setup>
/**
 * API table for a component's props (and v-model / slots / events if useful).
 *
 * Usage:
 * <DocProps title="Accordion" :rows="[
 *     { name: 'type', type: `'single' | 'multiple'`, default: `'single'`, description: 'Satu atau banyak panel terbuka.' },
 * ]" />
 */
import Table from '@/components/ui/Table.vue';

defineProps({
    title: { type: String, required: true },
    /** @type {Array<{ name: string, type: string, default?: string, description: string }>} */
    rows: { type: Array, required: true },
});
</script>

<template>
    <section class="flex flex-col gap-3">
        <h2 class="text-lg font-semibold tracking-tight">
            API <code class="rounded-md bg-ink/6 px-1.5 py-0.5 font-mono text-base">{{ title }}</code>
        </h2>

        <Table>
            <template #head>
                <th>Prop</th>
                <th>Tipe</th>
                <th>Default</th>
                <th>Keterangan</th>
            </template>

            <tr v-for="row in rows" :key="row.name">
                <td class="align-top font-mono text-[0.8125rem] whitespace-nowrap text-brand-ink">{{ row.name }}</td>
                <td class="align-top font-mono text-[0.8125rem] text-ink/80">{{ row.type }}</td>
                <td class="align-top font-mono text-[0.8125rem] whitespace-nowrap text-muted">{{ row.default ?? '—' }}</td>
                <td class="align-top text-muted">{{ row.description }}</td>
            </tr>
        </Table>
    </section>
</template>
