<script setup>
/**
 * NIK input that searches the penduduk data while typing (from three digits) and suggests matching
 * residents with their names. Emits `select` with the resident once the NIK matches one exactly,
 * whether it was picked from the list or typed in full.
 *
 * Usage: <NikLookupInput v-model="values.nik" label="NIK" @select="fillFromResident" />
 *
 * The resident is `{ id, nik, nama, fields }`, where `fields` holds the values formatted for a document,
 * keyed by penduduk field (see `PendudukController::search`).
 */
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import Autocomplete from '@/components/ui/Autocomplete.vue';

const nik = defineModel({ type: String, default: '' });

const props = defineProps({
    label: { type: String, default: 'NIK' },
    hint: { type: String, default: null },
    error: { type: String, default: null },
});

const emit = defineEmits(['select']);

const MIN_SEARCH_DIGITS = 3;
const NIK_LENGTH = 16;
const SEARCH_DELAY_MS = 250;

const results = ref([]);
const searchedDigits = ref('');
const isSearching = ref(false);

let searchTimeout = null;
let activeRequest = null;
let selectedNik = null;

const digits = computed(() => (nik.value ?? '').replace(/\D/g, ''));

const suggestions = computed(() => results.value.map((resident) => ({ value: resident.nik, hint: resident.nama })));

const isNotFound = computed(
    () => !isSearching.value && digits.value.length === NIK_LENGTH && searchedDigits.value === digits.value && results.value.length === 0,
);

const hintText = computed(() => (isNotFound.value ? 'NIK tidak ditemukan di data penduduk. Isi kolom lain secara manual.' : props.hint));

/** Emit `select` once for a resident whose NIK is exactly what was typed. */
function selectExactMatch() {
    const resident = results.value.find((candidate) => candidate.nik === digits.value);

    if (!resident) {
        selectedNik = null;

        return;
    }

    if (selectedNik !== resident.nik) {
        selectedNik = resident.nik;
        emit('select', resident);
    }
}

async function search(searchDigits) {
    activeRequest?.abort();

    const request = new AbortController();
    activeRequest = request;
    isSearching.value = true;

    try {
        const response = await fetch(route('penduduk.search', { nik: searchDigits }), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: request.signal,
        });

        if (!response.ok) {
            throw new Error(`Unexpected status ${response.status}`);
        }

        results.value = (await response.json()).data;
        searchedDigits.value = searchDigits;
        selectExactMatch();
    } catch (error) {
        if (error.name !== 'AbortError') {
            results.value = [];
        }
    } finally {
        if (activeRequest === request) {
            isSearching.value = false;
        }
    }
}

watch(digits, (value) => {
    clearTimeout(searchTimeout);

    if (value.length < MIN_SEARCH_DIGITS) {
        activeRequest?.abort();
        results.value = [];
        searchedDigits.value = '';
        isSearching.value = false;
        selectedNik = null;

        return;
    }

    /* A suggestion that was just picked is already in the results: fill in without waiting. */
    selectExactMatch();
    searchTimeout = setTimeout(() => search(value), SEARCH_DELAY_MS);
});

onBeforeUnmount(() => {
    clearTimeout(searchTimeout);
    activeRequest?.abort();
});
</script>

<template>
    <Autocomplete
        v-model="nik"
        :label="label"
        :hint="hintText"
        :error="error"
        :suggestions="suggestions"
        :loading="isSearching"
        ignore-filter
        icon="search"
        inputmode="numeric"
        maxlength="16"
        placeholder="Ketik NIK untuk mencari penduduk"
        autocomplete="off"
    />
</template>
