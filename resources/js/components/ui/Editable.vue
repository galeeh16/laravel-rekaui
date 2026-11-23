<script setup>
/**
 * Built on reka-ui Editable: text that turns into an input in place. Enter or clicking outside saves,
 * Escape cancels.
 * Sizes: md, lg (for titles)
 *
 * Usage: <Editable v-model="letter.subject" aria-label="Perihal" @submit="save" />
 *        <Editable v-model="folder.name" aria-label="Nama folder" activation-mode="dblclick" :controls="false" />
 *
 * `submit` fires with the new text after saving, only when it actually changed, so it's the place to send it to the server.
 * The `class` attribute is applied to the wrapper; every other attribute (aria-label, autocomplete…) goes to the <input>.
 */
import {
    EditableArea,
    EditableCancelTrigger,
    EditableEditTrigger,
    EditableInput,
    EditablePreview,
    EditableRoot,
    EditableSubmitTrigger,
} from 'reka-ui';
import { useAttrs } from 'vue';
import Button from './Button.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel({ type: String, default: '' });

defineProps({
    placeholder: { type: String, default: 'Klik untuk mengisi…' },
    /** How editing starts: `focus` (click or Tab), `dblclick`, or `none` (edit button only). */
    activationMode: { type: String, default: 'focus' },
    /** How editing is saved: `both` (Enter or click outside), `enter`, `blur`, or `none` (save button only). */
    submitMode: { type: String, default: 'both' },
    /** Show edit / save / cancel buttons next to the text. */
    controls: { type: Boolean, default: true },
    selectOnFocus: { type: Boolean, default: true },
    maxLength: { type: Number, default: undefined },
    size: { type: String, default: 'md' },
    name: { type: String, default: undefined },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
});

const emit = defineEmits(['submit']);

const attrs = useAttrs();

let valueBeforeEdit = model.value;

function onStateChange(state) {
    if (state === 'edit') {
        valueBeforeEdit = model.value;
    }
}

function onSubmit(value) {
    if (value !== valueBeforeEdit) {
        emit('submit', value);
    }
}

function inputAttrs() {
    const { class: _class, ...rest } = attrs;

    return rest;
}
</script>

<template>
    <EditableRoot
        v-model="model"
        :placeholder="placeholder"
        :activation-mode="disabled || readonly ? 'none' : activationMode"
        :submit-mode="submitMode"
        :select-on-focus="selectOnFocus"
        :max-length="maxLength"
        :name="name"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :class="['flex min-w-0 items-center gap-1', attrs.class]"
        @update:state="onStateChange"
        @submit="onSubmit"
    >
        <EditableArea
            :class="[
                'min-w-0 flex-1 data-disabled:opacity-60 data-empty:text-muted',
                size === 'lg' ? 'text-xl font-semibold tracking-tight' : 'text-sm',
            ]"
        >
            <EditablePreview
                :class="[
                    'block w-full truncate rounded-lg border border-transparent px-2.5 py-1.5 transition-colors',
                    'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand',
                    disabled || readonly ? 'cursor-default' : 'cursor-text hover:bg-ink/5',
                ]"
            />
            <EditableInput
                v-bind="inputAttrs()"
                class="w-full rounded-lg border border-brand bg-surface px-2.5 py-1.5 text-ink ring-3 ring-brand/15 outline-none placeholder:text-muted/80"
            />
        </EditableArea>

        <template v-if="controls && !disabled && !readonly">
            <EditableEditTrigger as-child>
                <Button variant="ghost" size="icon-sm" icon="pencil" aria-label="Ubah" />
            </EditableEditTrigger>
            <EditableSubmitTrigger as-child>
                <Button variant="soft" size="icon-sm" icon="check" aria-label="Simpan" />
            </EditableSubmitTrigger>
            <EditableCancelTrigger as-child>
                <Button variant="ghost" size="icon-sm" icon="x" aria-label="Batal" />
            </EditableCancelTrigger>
        </template>
    </EditableRoot>
</template>
