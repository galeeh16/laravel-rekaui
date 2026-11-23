<script setup>
/**
 * Built on reka-ui PinInput: one box per character, typing jumps to the next box, pasting a code fills
 * every box and Backspace goes back. The v-model is a plain string (e.g. '482915'), ready to send to Laravel.
 *
 * Usage: <PinInput v-model="form.code" label="Kode verifikasi" type="number" otp @complete="verify" />
 *        <PinInput v-model="form.pin" :length="4" type="number" mask />
 */
import { PinInputInput, PinInputRoot } from 'reka-ui';
import { computed, ref, useId, watch } from 'vue';
import Label from './Label.vue';

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    /** Number of boxes. */
    length: { type: Number, default: 6 },
    label: { type: String, default: null },
    id: { type: String, default: null },
    name: { type: String, default: undefined },
    /** `number` only accepts digits and opens the numeric keyboard on phones. */
    type: { type: String, default: 'text' },
    /** Fill the boxes in order and let the browser autofill one-time codes. */
    otp: { type: Boolean, default: false },
    /** Hide the characters like a password. */
    mask: { type: Boolean, default: false },
    placeholder: { type: String, default: '○' },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});

const emit = defineEmits(['complete']);

const generatedId = useId();
const pinId = computed(() => props.id ?? generatedId);

/**
 * reka-ui keeps one array entry per box. The array lives here (not derived from the string)
 * so an empty box in the middle doesn't shift the characters after it.
 */
const characters = ref(toCharacters(model.value));

function toCharacters(value) {
    const text = String(value ?? '');
    const allowed = props.type === 'number' ? text.replace(/\D/g, '') : text;

    return allowed
        .slice(0, props.length)
        .split('')
        .map((character) => (props.type === 'number' ? Number(character) : character));
}

function toText(values) {
    return values.map((character) => character ?? '').join('');
}

watch(model, (value) => {
    if ((value ?? '') !== toText(characters.value)) {
        characters.value = toCharacters(value);
    }
});

function updateCharacters(values) {
    characters.value = values;
    model.value = toText(values);
}
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label v-if="label" :for="pinId">{{ label }}</Label>

        <PinInputRoot
            :id="pinId"
            :model-value="characters"
            :name="name"
            :type="type"
            :otp="otp"
            :mask="mask"
            :placeholder="placeholder"
            :disabled="disabled"
            class="flex flex-wrap items-center gap-2"
            @update:model-value="updateCharacters"
            @complete="(values) => emit('complete', toText(values))"
        >
            <PinInputInput
                v-for="(_, index) in length"
                :key="index"
                :index="index"
                :aria-label="`Karakter ${index + 1} dari ${length}`"
                :aria-invalid="error ? 'true' : undefined"
                :class="[
                    'size-11 rounded-xl border bg-surface text-center text-base font-medium text-ink shadow-xs outline-none transition',
                    'placeholder:text-muted/50 focus:ring-3 disabled:cursor-not-allowed disabled:opacity-60',
                    error
                        ? 'border-danger focus:border-danger focus:ring-danger/15'
                        : 'border-line focus:border-brand focus:ring-brand/15',
                ]"
            />
        </PinInputRoot>

        <p v-if="error" class="text-sm text-danger">{{ error }}</p>
        <p v-else-if="hint" class="text-sm text-muted">{{ hint }}</p>
    </div>
</template>
