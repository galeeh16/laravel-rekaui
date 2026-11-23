<script setup>
import { TabsContent } from 'reka-ui';

defineProps({
    value: { type: [String, Number], required: true },
});
</script>

<template>
    <TabsContent
        :value="value"
        class="flex-1 rounded-xl outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
    >
        <slot />
    </TabsContent>
</template>
