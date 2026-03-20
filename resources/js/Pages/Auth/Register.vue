<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    cpf_cnpj: '',
    telefone: '',
    password: '',
    password_confirmation: '',
    restaurant_name: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Criar Conta" />

        <h2 class="text-xl font-bold text-white text-center mb-6">Criar Conta</h2>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Nome -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nome</label>
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="Seu nome completo"
                />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="seu@email.com"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <!-- CPF/CNPJ -->
            <div>
                <label for="cpf_cnpj" class="block text-sm font-medium text-gray-300 mb-1">CPF/CNPJ</label>
                <input
                    id="cpf_cnpj"
                    type="text"
                    v-model="form.cpf_cnpj"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="000.000.000-00 ou 00.000.000/0000-00"
                />
                <InputError class="mt-1" :message="form.errors.cpf_cnpj" />
            </div>

            <!-- Telefone -->
            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-300 mb-1">Telefone</label>
                <input
                    id="telefone"
                    type="text"
                    v-model="form.telefone"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="(00) 00000-0000"
                />
                <InputError class="mt-1" :message="form.errors.telefone" />
            </div>

            <!-- Nome do Restaurante -->
            <div>
                <label for="restaurant_name" class="block text-sm font-medium text-gray-300 mb-1">Nome do Restaurante</label>
                <input
                    id="restaurant_name"
                    type="text"
                    v-model="form.restaurant_name"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="Nome do seu restaurante"
                />
                <InputError class="mt-1" :message="form.errors.restaurant_name" />
            </div>

            <!-- Senha -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Senha</label>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="Mínimo 8 caracteres"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <!-- Confirmar Senha -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirmar Senha</label>
                <input
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border-gray-600 bg-gray-700/50 text-white placeholder-gray-400 focus:border-[#E63B2E] focus:ring-[#E63B2E] text-sm"
                    placeholder="Repita a senha"
                />
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="{ 'opacity-50': form.processing }"
                    class="w-full rounded-lg bg-[#E63B2E] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#d1342a] focus:outline-none focus:ring-2 focus:ring-[#E63B2E] focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors duration-150"
                >
                    Criar Conta
                </button>
            </div>

            <!-- Login link -->
            <div class="text-center">
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-400 hover:text-[#E63B2E] transition-colors duration-150"
                >
                    Já tem uma conta? <span class="underline">Entrar</span>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
