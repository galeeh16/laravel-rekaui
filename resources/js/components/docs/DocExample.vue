<script setup>
/**
 * One documented example: a live preview and the source code that produces it.
 *
 * Usage:
 * <DocExample title="Dasar" description="..." :code="basicCode">
 *     ...live preview...
 * </DocExample>
 *
 * Inside a page's <script setup>, write the closing script tag in code strings as `<\/script>`
 * so the Vue compiler doesn't end the block early.
 */
import { onBeforeUnmount, ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Tabs from '@/components/ui/Tabs.vue';
import TabsContent from '@/components/ui/TabsContent.vue';
import TabsList from '@/components/ui/TabsList.vue';
import TabsTrigger from '@/components/ui/TabsTrigger.vue';

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: null },
    code: { type: String, required: true },
    /** Classes for the preview box, e.g. to change alignment or padding. */
    previewClass: { type: String, default: 'flex min-h-56 items-center justify-center p-6 sm:p-10' },
});

const copied = ref(false);
let resetTimeout = null;

async function copyCode() {
    try {
        await navigator.clipboard.writeText(props.code.trim());
        copied.value = true;
        clearTimeout(resetTimeout);
        resetTimeout = setTimeout(() => (copied.value = false), 2000);
    } catch {
        // Clipboard can be blocked (insecure context, permissions); nothing else to do.
    }
}

onBeforeUnmount(() => clearTimeout(resetTimeout));
</script>

<template>
    <section class="flex flex-col gap-3">
        <div>
            <h2 class="text-lg font-semibold tracking-tight">{{ title }}</h2>
            <p v-if="description" class="mt-1 text-sm text-muted">{{ description }}</p>
        </div>

        <Tabs default-value="preview">
            <div class="flex items-center justify-between gap-3">
                <TabsList>
                    <TabsTrigger value="preview" icon="eye">Preview</TabsTrigger>
                    <TabsTrigger value="code" icon="code">Code</TabsTrigger>
                </TabsList>

                <Button
                    variant="ghost"
                    size="sm"
                    :icon="copied ? 'check' : 'copy'"
                    :aria-label="copied ? 'Kode tersalin' : 'Salin kode'"
                    @click="copyCode"
                >
                    {{ copied ? 'Tersalin' : 'Salin' }}
                </Button>
            </div>

            <TabsContent value="preview">
                <div :class="['rounded-2xl border border-line bg-surface shadow-xs', previewClass]">
                    <slot />
                </div>
            </TabsContent>

            <TabsContent value="code">
                <pre
                    class="max-h-[32rem] overflow-auto rounded-2xl border border-line bg-zinc-950 p-5 font-mono text-[0.8125rem] leading-relaxed text-zinc-100"
                ><code>{{ code.trim() }}</code></pre>
            </TabsContent>
        </Tabs>
    </section>
</template>
