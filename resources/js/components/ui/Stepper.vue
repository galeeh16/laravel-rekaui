<script setup>
/**
 * Built on reka-ui Stepper (arrow-key navigation, linear mode, aria-current on the active step).
 * Steps are data-driven; v-model is the active step number, starting at 1.
 *
 * Usage:
 * <Stepper v-model="step" :steps="[{ title: 'Data surat' }, { title: 'Lampiran' }, { title: 'Kirim' }]" />
 * <Button @click="step++">Lanjut</Button>
 *
 * In linear mode (default) the user can only click back, or to the next step;
 * set :linear="false" to allow jumping to any step.
 */
import {
    StepperDescription,
    StepperIndicator,
    StepperItem,
    StepperRoot,
    StepperSeparator,
    StepperTitle,
    StepperTrigger,
} from 'reka-ui';
import { computed } from 'vue';
import Icon from './Icon.vue';

const model = defineModel({ type: Number, default: undefined });

const props = defineProps({
    /** @type {Array<{ title: string, description?: string, icon?: string, disabled?: boolean, completed?: boolean }>} */
    steps: { type: Array, required: true },
    defaultValue: { type: Number, default: 1 },
    orientation: { type: String, default: 'horizontal' },
    linear: { type: Boolean, default: true },
});

const isVertical = computed(() => props.orientation === 'vertical');

const indicatorClasses = {
    completed: 'bg-brand text-white',
    active: 'bg-surface text-brand ring-2 ring-brand ring-inset',
    inactive: 'bg-surface text-muted ring-1 ring-line ring-inset',
};
</script>

<template>
    <StepperRoot
        v-model="model"
        :default-value="defaultValue"
        :orientation="orientation"
        :linear="linear"
        :class="isVertical ? 'relative flex flex-col' : 'relative flex w-full items-start'"
    >
        <StepperItem
            v-for="(step, index) in steps"
            :key="index"
            v-slot="{ state }"
            :step="index + 1"
            :disabled="step.disabled ?? false"
            :completed="step.completed ?? false"
            :class="isVertical ? 'relative' : 'relative flex flex-1 flex-col items-center'"
        >
            <StepperTrigger
                :class="[
                    'flex cursor-pointer rounded-xl p-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                    'disabled:cursor-not-allowed',
                    isVertical ? 'items-start gap-4 text-left' : 'flex-col items-center gap-2 text-center',
                ]"
            >
                <StepperIndicator
                    :class="[
                        'flex size-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold transition-colors',
                        indicatorClasses[state],
                    ]"
                >
                    <Icon v-if="state === 'completed'" name="check" class="size-4.5" />
                    <Icon v-else-if="step.icon" :name="step.icon" class="size-4.5" />
                    <template v-else>{{ index + 1 }}</template>
                </StepperIndicator>

                <span
                    :class="[
                        'flex flex-col gap-0.5',
                        isVertical ? 'pt-1.5' : 'px-2',
                        isVertical && index < steps.length - 1 ? 'pb-8' : '',
                    ]"
                >
                    <StepperTitle as="span" :class="['text-sm font-medium', state === 'inactive' ? 'text-muted' : 'text-ink']">
                        {{ step.title }}
                    </StepperTitle>
                    <StepperDescription v-if="step.description" as="span" class="text-xs text-muted">
                        {{ step.description }}
                    </StepperDescription>
                </span>
            </StepperTrigger>

            <StepperSeparator
                v-if="index < steps.length - 1"
                :class="[
                    'absolute rounded-full transition-colors',
                    state === 'completed' ? 'bg-brand' : 'bg-line',
                    isVertical ? 'top-11.5 bottom-1.5 left-5.25 w-0.5' : 'top-5.25 right-[calc(-50%+1.75rem)] left-[calc(50%+1.75rem)] h-0.5',
                ]"
            />
        </StepperItem>
    </StepperRoot>
</template>
