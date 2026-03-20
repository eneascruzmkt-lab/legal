<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Entrar" />

        <h2 class="text-xl font-bold text-white text-center mb-6">Entrar</h2>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-400 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="seu@email.com"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <!-- Senha -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Senha</label>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="Sua senha"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <!-- Lembrar-me + Esqueceu a senha -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="rounded border-gray-600 bg-gray-700/50 text-[#E63B2E] shadow-sm focus:ring-[#E63B2E]"
                    />
                    <span class="ms-2 text-sm text-gray-400">Lembrar-me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-400 hover:text-[#E63B2E] transition-colors duration-150"
                >
                    Esqueceu a senha?
                </Link>
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="{ 'opacity-50': form.processing }"
                    class="w-full rounded-lg bg-[#E63B2E] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#d1342a] focus:outline-none focus:ring-2 focus:ring-[#E63B2E] focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors duration-150"
                >
                    Entrar
                </button>
            </div>

            <!-- Register link -->
            <div class="text-center">
                <Link
                    :href="route('register')"
                    class="text-sm text-gray-400 hover:text-[#E63B2E] transition-colors duration-150"
                >
                    Não tem uma conta? <span class="underline">Criar Conta</span>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
