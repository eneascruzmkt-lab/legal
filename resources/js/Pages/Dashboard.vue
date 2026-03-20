<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();

const stats = [
    {
        label: 'Total de Visualizações',
        value: '1.248',
        icon: 'eye',
        color: 'text-blue-400',
        bg: 'bg-blue-400/10',
    },
    {
        label: 'Itens no Cardápio',
        value: '32',
        icon: 'menu',
        color: 'text-green-400',
        bg: 'bg-green-400/10',
    },
    {
        label: 'Vídeos Cadastrados',
        value: '8',
        icon: 'video',
        color: 'text-purple-400',
        bg: 'bg-purple-400/10',
    },
    {
        label: 'Visualizações do Mês',
        value: '324',
        icon: 'chart',
        color: 'text-[#E63B2E]',
        bg: 'bg-[#E63B2E]/10',
    },
];

const topItems = [
    { name: 'X-Burger Especial', views: 187, category: 'Hambúrgueres' },
    { name: 'Pizza Margherita', views: 143, category: 'Pizzas' },
    { name: 'Açaí 500ml', views: 98, category: 'Sobremesas' },
    { name: 'Coca-Cola 600ml', views: 76, category: 'Bebidas' },
    { name: 'Batata Frita Grande', views: 65, category: 'Acompanhamentos' },
];

const topVideos = [
    { name: 'Preparo do X-Burger', views: 312, duration: '0:45' },
    { name: 'Pizza saindo do forno', views: 198, duration: '0:30' },
    { name: 'Montagem do Açaí', views: 156, duration: '1:02' },
    { name: 'Drinks especiais', views: 89, duration: '0:38' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-white">Dashboard</h2>
        </template>

        <!-- Welcome -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">
                Bem-vindo, {{ $page.props.auth.user.name }}!
            </h1>
            <p class="text-gray-400 mt-1">Aqui está o resumo do seu restaurante.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div
                v-for="stat in stats"
                :key="stat.label"
                class="bg-gray-800 rounded-xl p-5 border border-gray-700/50"
            >
                <div class="flex items-center justify-between mb-3">
                    <div :class="[stat.bg, 'p-2.5 rounded-lg']">
                        <!-- Eye icon -->
                        <svg v-if="stat.icon === 'eye'" :class="['w-5 h-5', stat.color]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <!-- Menu icon -->
                        <svg v-else-if="stat.icon === 'menu'" :class="['w-5 h-5', stat.color]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <!-- Video icon -->
                        <svg v-else-if="stat.icon === 'video'" :class="['w-5 h-5', stat.color]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <!-- Chart icon -->
                        <svg v-else-if="stat.icon === 'chart'" :class="['w-5 h-5', stat.color]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white">{{ stat.value }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ stat.label }}</p>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Items -->
            <div class="bg-gray-800 rounded-xl border border-gray-700/50 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700/50">
                    <h3 class="text-lg font-semibold text-white">Itens Mais Visualizados</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3">Item</th>
                                <th class="px-5 py-3">Categoria</th>
                                <th class="px-5 py-3 text-right">Visualizações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            <tr v-for="item in topItems" :key="item.name" class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3 text-sm text-white font-medium">{{ item.name }}</td>
                                <td class="px-5 py-3 text-sm text-gray-400">{{ item.category }}</td>
                                <td class="px-5 py-3 text-sm text-gray-300 text-right">{{ item.views }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Videos -->
            <div class="bg-gray-800 rounded-xl border border-gray-700/50 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700/50">
                    <h3 class="text-lg font-semibold text-white">Vídeos Mais Assistidos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3">Vídeo</th>
                                <th class="px-5 py-3">Duração</th>
                                <th class="px-5 py-3 text-right">Visualizações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            <tr v-for="video in topVideos" :key="video.name" class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3 text-sm text-white font-medium">{{ video.name }}</td>
                                <td class="px-5 py-3 text-sm text-gray-400">{{ video.duration }}</td>
                                <td class="px-5 py-3 text-sm text-gray-300 text-right">{{ video.views }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
