<script setup>
/**
 * Built on reka-ui Tabs (arrow-key navigation, automatic or manual activation).
 * For page-level tabs where each tab is its own URL, use `NavTabs` instead.
 *
 * Usage:
 * <Tabs default-value="account">
 *     <TabsList>
 *         <TabsTrigger value="account">Akun</TabsTrigger>
 *         <TabsTrigger value="password">Password</TabsTrigger>
 *     </TabsList>
 *     <TabsContent value="account">...</TabsContent>
 *     <TabsContent value="password">...</TabsContent>
 * </Tabs>
 */
import { TabsRoot } from 'reka-ui';

const model = defineModel({ type: [String, Number], default: undefined });

defineProps({
    defaultValue: { type: [String, Number], default: undefined },
    orientation: { type: String, default: 'horizontal' },
    /** `automatic` switches on focus, `manual` switches on Enter/Space/click. */
    activationMode: { type: String, default: 'automatic' },
});
</script>

<template>
    <TabsRoot
        v-model="model"
        :default-value="defaultValue"
        :orientation="orientation"
        :activation-mode="activationMode"
        class="flex flex-col gap-4 data-[orientation=vertical]:flex-row"
    >
        <slot />
    </TabsRoot>
</template>
