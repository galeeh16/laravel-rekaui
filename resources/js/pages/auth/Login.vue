<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLogo from '@/components/AppLogo.vue';
import Alert from '@/components/ui/Alert.vue';
import Button from '@/components/ui/Button.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import Icon from '@/components/ui/Icon.vue';
import Input from '@/components/ui/Input.vue';

const page = usePage();

const currentYear = new Date().getFullYear();

const highlights = [
    { icon: 'send', title: 'Surat keluar', description: 'Buat, kirim, dan pantau status setiap surat.' },
    { icon: 'folder-open', title: 'Template dokumen', description: 'Isi template Word dan unduh dokumennya.' },
    { icon: 'users', title: 'Data penduduk', description: 'Kelola data penduduk dalam satu tempat.' },
];

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

    <main class="grid min-h-screen lg:grid-cols-2">
        <!-- Left: brand panel (large screens only) -->
        <aside class="relative hidden flex-col overflow-hidden bg-brand p-10 text-white lg:flex xl:p-14">
            <!-- Decorative background -->
            <div
                class="pointer-events-none absolute inset-0 opacity-15 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:24px_24px]"
                aria-hidden="true"
            />
            <div class="pointer-events-none absolute -top-32 -right-32 size-96 rounded-full bg-white/10 blur-3xl" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-40 -left-24 size-96 rounded-full bg-black/15 blur-3xl" aria-hidden="true" />

            <Link :href="route('home')" class="relative flex items-center gap-3">
                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/25">
                    <Icon name="mail" class="size-5" />
                </span>

                <span>
                    <span class="block text-lg leading-tight font-semibold">{{ page.props.name }}</span>
                    <span class="block text-xs text-white/70">Manajemen surat</span>
                </span>
            </Link>

            <div class="relative my-auto max-w-md py-12">
                <h2 class="text-4xl leading-tight font-medium tracking-tight xl:text-5xl">
                    Semua urusan surat dan dokumen dalam satu tempat.
                </h2>

                <ul class="mt-10 flex flex-col gap-5">
                    <li v-for="highlight in highlights" :key="highlight.title" class="flex items-start gap-4">
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/12 ring-1 ring-white/20">
                            <Icon :name="highlight.icon" class="size-5" />
                        </span>

                        <span>
                            <span class="block font-medium">{{ highlight.title }}</span>
                            <span class="mt-0.5 block text-sm text-white/75">{{ highlight.description }}</span>
                        </span>
                    </li>
                </ul>
            </div>

            <p class="relative text-sm text-white/60">© {{ currentYear }} {{ page.props.name }}</p>
        </aside>

        <!-- Right: login form -->
        <section class="flex flex-col bg-surface px-6 py-10 sm:px-10">
            <Link :href="route('home')" class="flex items-center gap-3 lg:hidden">
                <AppLogo />

                <span>
                    <span class="block text-lg leading-tight font-semibold">{{ page.props.name }}</span>
                    <span class="block text-xs text-muted">Manajemen surat</span>
                </span>
            </Link>

            <div class="mx-auto my-auto w-full max-w-sm py-12">
                <div class="flex flex-col gap-1">
                    <h1 class="text-3xl font-medium tracking-tight">Masuk ke akun Anda</h1>
                    <p class="text-sm text-muted">Masukkan email dan password Anda di bawah ini.</p>
                </div>

                <Alert v-if="page.flash.status" variant="success" class="mt-6">{{ page.flash.status }}</Alert>

                <form class="mt-8 flex flex-col gap-5" @submit.prevent="submit">
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
            </div>

            <p class="text-center text-xs text-muted lg:hidden">© {{ currentYear }} {{ page.props.name }}</p>
        </section>
    </main>
</template>
