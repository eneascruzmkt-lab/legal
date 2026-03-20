<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    dailyViews: {
        type: Array,
        default: () => [],
    },
    topItems: {
        type: Array,
        default: () => [],
    },
    topVideos: {
        type: Array,
        default: () => [],
    },
});

// Date range
const dateRange = ref(30);
const dateRangeOptions = [
    { label: '7 dias', value: 7 },
    { label: '30 dias', value: 30 },
    { label: '90 dias', value: 90 },
];

const setDateRange = (days) => {
    dateRange.value = days;
    router.get(route('analytics.index'), { days }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Filter daily views by selected range
const filteredViews = computed(() => {
    const cutoff = new Date();
    cutoff.setDate(cutoff.getDate() - dateRange.value);
    return props.dailyViews.filter((v) => new Date(v.date) >= cutoff);
});

// Chart max value
const maxViews = computed(() => {
    if (filteredViews.value.length === 0) return 1;
    return Math.max(...filteredViews.value.map((v) => v.count), 1);
});

// Stats
const totalViews = computed(() => {
    return filteredViews.value.reduce((sum, v) => sum + v.count, 0);
});

const totalItemViews = computed(() => {
    return props.topItems.reduce((sum, item) => sum + (item.views_count || 0), 0);
});

const totalVideoPlays = computed(() => {
    return props.topVideos.reduce((sum, item) => sum + (item.video_plays_count || 0), 0);
});

const formatDate = (dateStr) => {
    const date = new Date(dateStr + 'T00:00:00');
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
};
</script>

<template>
    <Head title="Metricas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-white">Metricas</h2>
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h1 class="text-2xl font-bold text-white">Metricas</h1>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400 mr-1">Periodo:</span>
                    <button
                        v-for="option in dateRangeOptions"
                        :key="option.value"
                        @click="setDateRange(option.value)"
                        :class="[
                            'px-3 py-1.5 text-xs font-medium rounded-lg transition-colors duration-150',
                            dateRange === option.value
                                ? 'bg-[#E63B2E] text-white'
                                : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700',
                        ]"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-gray-800 rounded-xl border border-gray-700/50 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2.5 rounded-lg bg-blue-400/10">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ totalViews.toLocaleString('pt-BR') }}</p>
                    <p class="text-sm text-gray-400 mt-1">Total de visualizacoes</p>
                </div>

                <div class="bg-gray-800 rounded-xl border border-gray-700/50 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2.5 rounded-lg bg-green-400/10">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ totalItemViews.toLocaleString('pt-BR') }}</p>
                    <p class="text-sm text-gray-400 mt-1">Total de cliques</p>
                </div>

                <div class="bg-gray-800 rounded-xl border border-gray-700/50 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2.5 rounded-lg bg-purple-400/10">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ totalVideoPlays.toLocaleString('pt-BR') }}</p>
                    <p class="text-sm text-gray-400 mt-1">Total de reproducoes</p>
                </div>
            </div>

            <!-- Daily Views Chart -->
            <div class="bg-gray-800 rounded-xl border border-gray-700/50 p-5 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">Visualizacoes diarias</h3>

                <div v-if="filteredViews.length === 0" class="text-center py-12">
                    <p class="text-gray-500">Nenhum dado disponivel para o periodo selecionado.</p>
                </div>

                <div v-else class="flex items-end gap-1 h-48 overflow-x-auto pb-2">
                    <div
                        v-for="view in filteredViews"
                        :key="view.date"
                        class="flex flex-col items-center gap-1 min-w-[28px] flex-1"
                    >
                        <!-- Bar -->
                        <span class="text-xs text-gray-400 mb-1">{{ view.count }}</span>
                        <div
                            class="w-full bg-[#E63B2E] rounded-t-sm transition-all duration-300 hover:bg-[#c93227] min-h-[4px]"
                            :style="{ height: Math.max((view.count / maxViews) * 140, 4) + 'px' }"
                            :title="`${formatDate(view.date)}: ${view.count} visualizacoes`"
                        ></div>
                        <!-- Date label -->
                        <span class="text-[10px] text-gray-500 -rotate-45 origin-top-left whitespace-nowrap mt-1">
                            {{ formatDate(view.date) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top 5 Items -->
                <div class="bg-gray-800 rounded-xl border border-gray-700/50 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-700/50">
                        <h3 class="text-lg font-semibold text-white">Top 5 itens mais vistos</h3>
                    </div>
                    <div v-if="topItems.length === 0" class="p-8 text-center">
                        <p class="text-gray-500 text-sm">Nenhum dado disponivel.</p>
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    <th class="px-5 py-3 w-8">#</th>
                                    <th class="px-5 py-3">Item</th>
                                    <th class="px-5 py-3 text-right">Visualizacoes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                <tr
                                    v-for="(item, index) in topItems.slice(0, 5)"
                                    :key="item.id"
                                    class="hover:bg-gray-700/30 transition-colors"
                                >
                                    <td class="px-5 py-3 text-sm text-gray-500 font-mono">{{ index + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg overflow-hidden bg-gray-700 shrink-0">
                                                <img
                                                    v-if="item.image_url"
                                                    :src="item.image_url"
                                                    :alt="item.name"
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                            <span class="text-sm text-white font-medium">{{ item.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-300 text-right font-mono">
                                        {{ (item.views_count || 0).toLocaleString('pt-BR') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top 5 Videos -->
                <div class="bg-gray-800 rounded-xl border border-gray-700/50 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-700/50">
                        <h3 class="text-lg font-semibold text-white">Top 5 videos mais assistidos</h3>
                    </div>
                    <div v-if="topVideos.length === 0" class="p-8 text-center">
                        <p class="text-gray-500 text-sm">Nenhum dado disponivel.</p>
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    <th class="px-5 py-3 w-8">#</th>
                                    <th class="px-5 py-3">Video</th>
                                    <th class="px-5 py-3 text-right">Reproducoes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                <tr
                                    v-for="(video, index) in topVideos.slice(0, 5)"
                                    :key="video.id"
                                    class="hover:bg-gray-700/30 transition-colors"
                                >
                                    <td class="px-5 py-3 text-sm text-gray-500 font-mono">{{ index + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg overflow-hidden bg-gray-700 shrink-0 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm text-white font-medium">{{ video.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-300 text-right font-mono">
                                        {{ (video.video_plays_count || 0).toLocaleString('pt-BR') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
