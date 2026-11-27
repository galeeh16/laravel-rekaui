<!--
    Usage:
    <Table>
        <template #head>
            <th>Nomor</th>
            <th class="text-right">Aksi</th>
        </template>

        <tr v-for="row in rows" :key="row.id">...</tr>

        <template #footer>Menampilkan 8 surat</template>
    </Table>
-->
<template>
    <div class="rounded-2xl border border-line bg-surface shadow-xs overflow-hidden">
        <div class="scrollbar-brand overflow-x-auto">
            <table
                class="w-full text-left text-sm [&_td]:px-5 [&_td]:py-4 [&_th]:px-5 [&_th]:py-3.5 [&_th]:font-normal [&_th]:whitespace-nowrap [&_th]:text-muted"
            >
                <thead v-if="$slots.head" class="border-b border-line">
                    <tr>
                        <slot name="head" />
                    </tr>
                </thead>

                <tbody class="divide-y divide-line [&>tr]:transition-colors [&>tr:hover]:bg-ink/2">
                    <slot />
                </tbody>
            </table>
        </div>

        <div v-if="$slots.footer" class="border-t border-line px-5 py-3.5 text-sm text-muted">
            <slot name="footer" />
        </div>
    </div>
</template>
