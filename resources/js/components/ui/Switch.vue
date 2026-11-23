<script setup>
/**
 * Built on reka-ui Switch.
 * Sizes: sm, md
 *
 * Usage: <Switch v-model="isEnabled" aria-label="Notifikasi email" />
 *        <Switch v-model="form.notify" hint="Dikirim setiap ada surat masuk">Notifikasi email</Switch>
 *        <Switch v-model="form.notify" label-position="start">Notifikasi email</Switch>   (text left, switch right, full width)
 *
 * The `class` attribute is applied to the wrapper <label>; every other attribute (name, disabled, aria-*)
 * goes to the switch itself.
 */
import { SwitchRoot, SwitchThumb } from 'reka-ui';
import { useAttrs } from 'vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: Boolean, default: false });

defineProps({
    size: { type: String, default: 'md' },
    /** Muted text under the label (default slot). */
    hint: { type: String, default: null },
    /** `end`: label after the switch. `start`: label first and the switch pushed to the right edge. */
    labelPosition: { type: String, default: 'end' },
});

const attrs = useAttrs();

function switchAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <label
        :class="[
            'cursor-pointer items-center gap-3 text-sm text-ink has-data-disabled:cursor-not-allowed has-data-disabled:opacity-60',
            labelPosition === 'start' ? 'flex flex-row-reverse justify-between' : 'inline-flex',
            attrs.class,
        ]"
    >
        <SwitchRoot
            v-model="model"
            v-bind="switchAttrs()"
            :class="[
                'inline-flex shrink-0 cursor-pointer items-center rounded-full bg-ink/15 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                'disabled:cursor-not-allowed data-[state=checked]:bg-brand',
                size === 'sm' ? 'h-5 w-8' : 'h-6 w-10',
            ]"
        >
            <SwitchThumb
                :class="[
                    'translate-x-0.5 rounded-full bg-white shadow-sm transition-transform',
                    size === 'sm' ? 'size-4 data-[state=checked]:translate-x-3.5' : 'size-5 data-[state=checked]:translate-x-4.5',
                ]"
            />
        </SwitchRoot>

        <span v-if="$slots.default || hint" class="flex flex-col gap-0.5">
            <span v-if="$slots.default"><slot /></span>
            <span v-if="hint" class="text-muted">{{ hint }}</span>
        </span>
    </label>
</template>
