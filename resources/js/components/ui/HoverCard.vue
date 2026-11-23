<script setup>
/**
 * Built on reka-ui HoverCard (preview card that opens after hovering or focusing the trigger,
 * stays open while the pointer is on the card, rendered in a portal). Mouse/keyboard only:
 * touch screens never see it, so don't put anything essential in it.
 *
 * Usage:
 * <HoverCard>
 *     <template #trigger>
 *         <a href="/contacts/1" class="font-medium text-brand hover:underline">Rina Kartika</a>
 *     </template>
 *
 *     ...preview content...
 * </HoverCard>
 */
import { HoverCardContent, HoverCardPortal, HoverCardRoot, HoverCardTrigger } from 'reka-ui';

const open = defineModel('open', { type: Boolean, default: false });

defineProps({
    /** Milliseconds of hovering before the card opens. */
    openDelay: { type: Number, default: 500 },
    /** Milliseconds before the card closes after the pointer leaves. */
    closeDelay: { type: Number, default: 200 },
    side: { type: String, default: 'bottom' },
    align: { type: String, default: 'center' },
    width: { type: String, default: 'w-80' },
});
</script>

<template>
    <HoverCardRoot v-model:open="open" :open-delay="openDelay" :close-delay="closeDelay">
        <HoverCardTrigger as-child>
            <slot name="trigger" />
        </HoverCardTrigger>

        <HoverCardPortal>
            <HoverCardContent
                :side="side"
                :align="align"
                :side-offset="8"
                :collision-padding="8"
                :class="[
                    'z-50 origin-(--reka-hover-card-content-transform-origin) rounded-xl border border-line bg-surface p-4 text-ink shadow-lg shadow-black/8 outline-none',
                    'data-[state=closed]:animate-pop-out data-[state=open]:animate-pop-in',
                    width,
                ]"
            >
                <slot />
            </HoverCardContent>
        </HoverCardPortal>
    </HoverCardRoot>
</template>
