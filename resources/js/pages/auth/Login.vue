<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLogo from '@/components/AppLogo.vue';
import Alert from '@/components/ui/Alert.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import Input from '@/components/ui/Input.vue';

const page = usePage();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Login" />

    <main class="flex min-h-screen flex-col items-center justify-center gap-8 p-6">
        <Link :href="route('home')" class="flex items-center gap-3">
            <AppLogo />

            <span>
                <span class="block text-lg leading-tight font-semibold">{{ page.props.name }}</span>
                <span class="block text-xs text-muted">Manajemen surat</span>
            </span>
        </Link>

        <Card class="w-full max-w-sm p-6 sm:p-8">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-medium tracking-tight">Masuk ke akun Anda</h1>
                <p class="text-sm text-muted">Masukkan email dan password Anda di bawah ini.</p>
            </div>

            <Alert v-if="page.flash.status" variant="success" class="mt-6">{{ page.flash.status }}</Alert>

            <form class="mt-6 flex flex-col gap-5" @submit.prevent="submit">
                <Input
                    v-model="form.email"
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="nama@contoh.com"
                    autocomplete="username"
                    :error="form.errors.email"
                    required
                    autofocus
                />

                <Input
                    v-model="form.password"
                    label="Password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    :error="form.errors.password"
                    required
                />

                <Checkbox v-model="form.remember" name="remember">Ingat saya</Checkbox>

                <Button type="submit" size="lg" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Memproses…' : 'Login' }}
                </Button>
            </form>
        </Card>
    </main>
</template>
