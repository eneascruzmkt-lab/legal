<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    menuItems: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

// Filter by category
const selectedCategory = ref(null);

const filteredItems = computed(() => {
    if (!selectedCategory.value) return props.menuItems;
    return props.menuItems.filter((item) => item.category_id === selectedCategory.value);
});

// Delete
const showDeleteModal = ref(false);
const deletingItem = ref(null);

const confirmDelete = (item) => {
    deletingItem.value = item;
    showDeleteModal.value = true;
};

const deleteItem = () => {
    router.delete(route('menu-items.destroy', deletingItem.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingItem.value = null;
        },
    });
};

const formatPrice = (price) => {
    return Number(price).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    });
};
</script>

<template>
    <Head title="Cardapio" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-white">Cardapio</h2>
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h1 class="text-2xl font-bold text-white">Cardapio</h1>
                <Link
                    :href="route('menu-items.index') + '?action=create'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Item
                </Link>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap gap-2 mb-6" v-if="categories.length > 0">
                <button
                    @click="selectedCategory = null"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150',
                        !selectedCategory
                            ? 'bg-[#E63B2E] text-white'
                            : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700',
                    ]"
                >
                    Todos
                </button>
                <button
                    v-for="category in categories"
                    :key="category.id"
                    @click="selectedCategory = category.id"
                    :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150',
                        selectedCategory === category.id
                            ? 'bg-[#E63B2E] text-white'
                            : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700',
                    ]"
                >
                    {{ category.name }}
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="menuItems.length === 0" class="bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-300 mb-2">Nenhum item cadastrado</h3>
                <p class="text-gray-500 mb-6">Crie seu primeiro item no cardapio.</p>
                <Link
                    :href="route('menu-items.index') + '?action=create'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Item
                </Link>
            </div>

            <!-- No results for filter -->
            <div v-else-if="filteredItems.length === 0" class="bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <h3 class="text-lg font-medium text-gray-300 mb-2">Nenhum item nesta categoria</h3>
                <p class="text-gray-500">Selecione outra categoria ou adicione novos itens.</p>
            </div>

            <!-- Items Grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div
                    v-for="item in filteredItems"
                    :key="item.id"
                    class="bg-gray-800 rounded-xl border border-gray-700/50 overflow-hidden hover:border-gray-600 transition-colors group"
                >
                    <!-- Image -->
                    <div class="relative aspect-video bg-gray-700 overflow-hidden">
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            :alt="item.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <!-- Badges -->
                        <div class="absolute top-2 left-2 flex gap-1.5">
                            <!-- Featured star -->
                            <span v-if="item.featured" class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-500/90 text-yellow-900 text-xs font-bold rounded-md">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                                Destaque
                            </span>
                            <!-- Video icon -->
                            <span v-if="item.video_url" class="inline-flex items-center gap-1 px-2 py-1 bg-purple-500/90 text-white text-xs font-bold rounded-md">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                                Video
                            </span>
                        </div>

                        <!-- Available badge -->
                        <div class="absolute top-2 right-2">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-1 text-xs font-bold rounded-md',
                                    item.available
                                        ? 'bg-green-500/90 text-white'
                                        : 'bg-red-500/90 text-white',
                                ]"
                            >
                                {{ item.available ? 'Disponivel' : 'Indisponivel' }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="text-sm font-semibold text-white leading-tight">{{ item.name }}</h3>
                            <span class="text-sm font-bold text-[#E63B2E] whitespace-nowrap">{{ formatPrice(item.price) }}</span>
                        </div>

                        <p v-if="item.category" class="text-xs text-gray-400 mb-3">
                            {{ item.category.name }}
                        </p>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 pt-3 border-t border-gray-700/50">
                            <Link
                                :href="route('menu-items.index') + '?action=edit&id=' + item.id"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs font-medium rounded-lg transition-colors"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </Link>
                            <button
                                @click="confirmDelete(item)"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-700 hover:bg-red-600/20 text-gray-400 hover:text-red-400 text-xs font-medium rounded-lg transition-colors"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" max-width="sm" @close="showDeleteModal = false">
            <div class="bg-gray-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Excluir Item</h3>
                </div>
                <p class="text-sm text-gray-400 mb-6">
                    Tem certeza que deseja excluir <strong class="text-white">{{ deletingItem?.name }}</strong>? Esta acao nao pode ser desfeita.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="deleteItem"
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                    >
                        Excluir
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
