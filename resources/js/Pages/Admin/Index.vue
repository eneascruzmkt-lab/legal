<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    restaurants: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
});

const search = ref('');
const filtered = computed(() => {
    if (!search.value) return props.restaurants;
    const q = search.value.toLowerCase();
    return props.restaurants.filter(r =>
        r.name.toLowerCase().includes(q) ||
        r.user?.name?.toLowerCase().includes(q) ||
        r.user?.email?.toLowerCase().includes(q)
    );
});
</script>

<template>
    <Head title="Painel Administrativo" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-8 px-4">
            <h1 class="text-2xl font-bold text-white mb-8">Painel Administrativo</h1>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-gray-800 rounded-xl p-5">
                    <p class="text-sm text-gray-400">Total Restaurantes</p>
                    <p class="text-3xl font-bold text-white">{{ stats.totalRestaurants || 0 }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5">
                    <p class="text-sm text-gray-400">Total Usuários</p>
                    <p class="text-3xl font-bold text-white">{{ stats.totalUsers || 0 }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5">
                    <p class="text-sm text-gray-400">Assinaturas Ativas</p>
                    <p class="text-3xl font-bold text-green-400">{{ stats.activeSubscriptions || 0 }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5">
                    <p class="text-sm text-gray-400">Views Totais</p>
                    <p class="text-3xl font-bold text-brand">{{ stats.totalViews || 0 }}</p>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-6">
                <input v-model="search" type="text" placeholder="Buscar restaurante, dono ou email..."
                    class="w-full md:w-96 bg-gray-800 border-gray-700 rounded-lg text-white focus:border-brand focus:ring-brand" />
            </div>

            <!-- Table -->
            <div class="bg-gray-800 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-900 text-gray-400">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Restaurante</th>
                                <th class="px-4 py-3 text-left">Dono</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-left">Plano</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Cadastro</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-300">
                            <tr v-for="r in filtered" :key="r.id" class="border-t border-gray-700 hover:bg-gray-700/50">
                                <td class="px-4 py-3">{{ r.id }}</td>
                                <td class="px-4 py-3 font-medium text-white">{{ r.name }}</td>
                                <td class="px-4 py-3">{{ r.user?.name }}</td>
                                <td class="px-4 py-3">{{ r.user?.email }}</td>
                                <td class="px-4 py-3">
                                    <span :class="['px-2 py-1 rounded text-xs font-medium',
                                        r.user?.subscription?.plan === 'pro' ? 'bg-brand/20 text-brand' : 'bg-gray-700 text-gray-400']">
                                        {{ r.user?.subscription?.plan || 'free' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="['w-2 h-2 rounded-full inline-block mr-2', r.active ? 'bg-green-400' : 'bg-red-400']"></span>
                                    {{ r.active ? 'Ativo' : 'Inativo' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ new Date(r.created_at).toLocaleDateString('pt-BR') }}</td>
                            </tr>
                            <tr v-if="filtered.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhum restaurante encontrado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
