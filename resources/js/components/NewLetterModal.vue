<script setup>
/**
 * "Surat baru" form, opened from the dashboard header button (or anywhere via `useNewLetterModal()`).
 * Gender and hobby options come from the `letterFormOptions` shared prop (PHP enums),
 * so the choices always match the server-side validation.
 */
import { useForm, usePage } from '@inertiajs/vue3';
import { getLocalTimeZone, today } from '@internationalized/date';
import { computed, watch } from 'vue';
import { route } from 'ziggy-js';
import Button from '@/components/ui/Button.vue';
import DatePicker from '@/components/ui/DatePicker.vue';
import Input from '@/components/ui/Input.vue';
import Modal from '@/components/ui/Modal.vue';
import RadioGroup from '@/components/ui/RadioGroup.vue';
import Select from '@/components/ui/Select.vue';
import { useNewLetterModal } from '@/composables/useNewLetterModal';

const { isOpen } = useNewLetterModal();
const page = usePage();

const options = computed(() => page.props.letterFormOptions ?? { genders: [], hobbies: [] });

/** Birth date must be before today (matches the `before:today` rule). */
const latestBirthDate = today(getLocalTimeZone()).subtract({ days: 1 }).toString();

const form = useForm({
    first_name: '',
    last_name: '',
    birth_date: '',
    gender: null,
    hobby: null,
});

function submit() {
    form.post(route('letters.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isOpen.value = false;
            form.reset();
        },
    });
}

watch(isOpen, (open) => {
    if (!open) {
        form.clearErrors();
    }
});

/** Hide a field's error as soon as the user changes that field. */
for (const field of Object.keys(form.data())) {
    watch(
        () => form[field],
        () => form.clearErrors(field),
    );
}
</script>

<template>
    <Modal v-model:open="isOpen" title="Surat baru" description="Lengkapi data berikut untuk membuat surat baru.">
        <form id="new-letter-form" class="flex flex-col gap-5" novalidate @submit.prevent="submit">
            <div class="grid gap-5 sm:grid-cols-2">
                <Input
                    v-model="form.first_name"
                    label="Nama depan"
                    name="first_name"
                    autocomplete="given-name"
                    :error="form.errors.first_name"
                />

                <Input
                    v-model="form.last_name"
                    label="Nama belakang"
                    name="last_name"
                    autocomplete="family-name"
                    hint="Opsional"
                    :error="form.errors.last_name"
                />
            </div>

            <DatePicker
                v-model="form.birth_date"
                label="Tanggal lahir"
                name="birth_date"
                min-date="1900-01-02"
                :max-date="latestBirthDate"
                :error="form.errors.birth_date"
            />

            <RadioGroup
                v-model="form.gender"
                label="Jenis kelamin"
                name="gender"
                variant="card"
                :options="options.genders"
                :error="form.errors.gender"
            />

            <Select
                v-model="form.hobby"
                label="Hobi"
                name="hobby"
                placeholder="Pilih hobi"
                :options="options.hobbies"
                :error="form.errors.hobby"
            />
        </form>

        <template #footer>
            <Button variant="secondary" @click="isOpen = false">Batal</Button>
            <Button type="submit" form="new-letter-form" :disabled="form.processing">
                {{ form.processing ? 'Menyimpan…' : 'Simpan' }}
            </Button>
        </template>
    </Modal>
</template>
