<script setup>
/**
 * Built on reka-ui Tooltip (opens on hover and keyboard focus, closes on Escape or click, rendered
 * in a portal). Each Tooltip brings its own TooltipProvider, so no app-level setup is needed.
 * Keep tooltips short and never put the only copy of important information in them.
 *
 * Usage:
 * <Tooltip content="Unduh PDF">
 *     <Button variant="ghost" size="icon-sm" icon="download" aria-label="Unduh PDF" />
 * </Tooltip>
 *
 * <Tooltip content="Cari surat" kbd="⌘K" side="bottom">...</Tooltip>
 * For richer content, use the #content slot instead of the `content` prop.
 */
import { TooltipArrow, TooltipContent, TooltipPortal, TooltipProvider, TooltipRoot, TooltipTrigger } from 'reka-ui';

const open = defineModel('open', { type: Boolean, default: undefined });

defineProps({
    content: { type: String, default: null },
    /** Keyboard shortcut shown after the text, e.g. "⌘K". */
    kbd: { type: String, default: null },
    side: { type: String, default: 'top' },
    align: { type: String, default: 'center' },
    /** Milliseconds of hovering before the tooltip opens. */
    delayDuration: { type: Number, default: 300 },
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <TooltipProvider :delay-duration="delayDuration">
        <TooltipRoot v-model:open="open" :disabled="disabled">
            <TooltipTrigger as-child>
                <slot />
            </TooltipTrigger>

            <TooltipPortal>
                <TooltipContent
                    :side="side"
                    :align="align"
                    :side-offset="6"
                    :collision-padding="8"
                    class="z-50 flex max-w-xs origin-(--reka-tooltip-content-transform-origin) items-center gap-2 rounded-lg bg-ink px-2.5 py-1.5 text-xs font-medium text-canvas shadow-lg shadow-black/8 select-none data-[state=closed]:animate-pop-out data-[state=delayed-open]:animate-pop-in data-[state=instant-open]:animate-pop-in"
                >
                    <slot name="content">{{ content }}</slot>

                    <kbd v-if="kbd" class="rounded bg-canvas/15 px-1.5 py-0.5 font-sans text-[0.6875rem] text-canvas/75">{{ kbd }}</kbd>

                    <TooltipArrow :width="10" :height="5" class="fill-ink" />
                </TooltipContent>
            </TooltipPortal>
        </TooltipRoot>
    </TooltipProvider>
</template>
