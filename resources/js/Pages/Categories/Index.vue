<script setup>
import { ref, nextTick } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

// Modal state
const showFormModal = ref(false);
const showDeleteModal = ref(false);
const editingCategory = ref(null);
const deletingCategory = ref(null);

// Form
const form = useForm({
    name: '',
    image: null,
    is_active: true,
});

// Image preview
const imagePreview = ref(null);
const imageInput = ref(null);

const openCreateModal = () => {
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
    imagePreview.value = null;
    showFormModal.value = true;
};

const openEditModal = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.is_active = category.is_active;
    form.image = null;
    imagePreview.value = category.image_url || null;
    form.clearErrors();
    showFormModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    editingCategory.value = null;
    form.reset();
    imagePreview.value = null;
};

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (ev) => {
            imagePreview.value = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submitForm = () => {
    if (editingCategory.value) {
        form.post(route('categories.update', editingCategory.value.id), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => closeFormModal(),
        });
    } else {
        form.post(route('categories.store'), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => closeFormModal(),
        });
    }
};

// Delete
const confirmDelete = (category) => {
    deletingCategory.value = category;
    showDeleteModal.value = true;
};

const deleteCategory = () => {
    router.delete(route('categories.destroy', deletingCategory.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingCategory.value = null;
        },
    });
};

// Toggle active
const toggleActive = (category) => {
    router.put(route('categories.update', category.id), {
        ...category,
        is_active: !category.is_active,
    }, {
        preserveScroll: true,
    });
};

// Drag and drop reorder
const dragIndex = ref(null);

const onDragStart = (index) => {
    dragIndex.value = index;
};

const onDragOver = (e) => {
    e.preventDefault();
};

const onDrop = (targetIndex) => {
    if (dragIndex.value === null || dragIndex.value === targetIndex) return;

    const ids = props.categories.map((c) => c.id);
    const [moved] = ids.splice(dragIndex.value, 1);
    ids.splice(targetIndex, 0, moved);

    router.post(route('categories.reorder'), { order: ids }, {
        preserveScroll: true,
    });

    dragIndex.value = null;
};

const onDragEnd = () => {
    dragIndex.value = null;
};
</script>

<template>
    <Head title="Categorias" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-white">Categorias</h2>
        </template>

        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Categorias</h1>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Categoria
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="categories.length === 0" class="bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-300 mb-2">Nenhuma categoria cadastrada</h3>
                <p class="text-gray-500 mb-6">Crie sua primeira categoria para organizar o cardápio.</p>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Categoria
                </button>
            </div>

            <!-- Categories table -->
            <div v-else class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-10"></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-16">Imagem</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nome</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider w-20">Ordem</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider w-20">Ativo</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider w-28">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(category, index) in categories"
                            :key="category.id"
                            class="border-b border-gray-700/50 last:border-b-0 hover:bg-gray-750 transition-colors cursor-grab active:cursor-grabbing"
                            draggable="true"
                            @dragstart="onDragStart(index)"
                            @dragover="onDragOver"
                            @drop="onDrop(index)"
                            @dragend="onDragEnd"
                            :class="{ 'opacity-50': dragIndex === index }"
                        >
                            <!-- Drag handle -->
                            <td class="px-4 py-3">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 6h2v2H8V6zm6 0h2v2h-2V6zM8 11h2v2H8v-2zm6 0h2v2h-2v-2zm-6 5h2v2H8v-2zm6 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <!-- Image -->
                            <td class="px-4 py-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-700 flex items-center justify-center">
                                    <img
                                        v-if="category.image_url"
                                        :src="category.image_url"
                                        :alt="category.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <svg v-else class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </td>
                            <!-- Name -->
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-white">{{ category.name }}</span>
                            </td>
                            <!-- Order -->
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm text-gray-400">{{ category.sort_order ?? index + 1 }}</span>
                            </td>
                            <!-- Active toggle -->
                            <td class="px-4 py-3 text-center">
                                <button
                                    @click="toggleActive(category)"
                                    :class="[
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                        category.is_active ? 'bg-[#E63B2E]' : 'bg-gray-600',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                            category.is_active ? 'translate-x-5' : 'translate-x-0',
                                        ]"
                                    />
                                </button>
                            </td>
                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        @click="openEditModal(category)"
                                        class="p-1.5 text-gray-400 hover:text-white rounded-lg hover:bg-gray-700 transition-colors"
                                        title="Editar"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="confirmDelete(category)"
                                        class="p-1.5 text-gray-400 hover:text-red-400 rounded-lg hover:bg-gray-700 transition-colors"
                                        title="Excluir"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showFormModal" max-width="md" @close="closeFormModal">
            <div class="bg-gray-800 p-6">
                <h3 class="text-lg font-semibold text-white mb-6">
                    {{ editingCategory ? 'Editar Categoria' : 'Nova Categoria' }}
                </h3>

                <form @submit.prevent="submitForm" class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nome</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:border-[#E63B2E] focus:ring-1 focus:ring-[#E63B2E] transition-colors"
                            placeholder="Ex: Pizzas, Bebidas..."
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Imagem</label>
                        <div class="flex items-center gap-4">
                            <div v-if="imagePreview" class="w-16 h-16 rounded-lg overflow-hidden bg-gray-700">
                                <img :src="imagePreview" class="w-full h-full object-cover" />
                            </div>
                            <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Escolher imagem
                                <input
                                    ref="imageInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImageChange"
                                />
                            </label>
                        </div>
                        <p v-if="form.errors.image" class="mt-1 text-sm text-red-400">{{ form.errors.image }}</p>
                    </div>

                    <!-- Active toggle -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Ativa</label>
                        <button
                            type="button"
                            @click="form.is_active = !form.is_active"
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                form.is_active ? 'bg-[#E63B2E]' : 'bg-gray-600',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    form.is_active ? 'translate-x-5' : 'translate-x-0',
                                ]"
                            />
                        </button>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="closeFormModal"
                            class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            {{ editingCategory ? 'Salvar' : 'Criar' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" max-width="sm" @close="showDeleteModal = false">
            <div class="bg-gray-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Excluir Categoria</h3>
                </div>
                <p class="text-sm text-gray-400 mb-6">
                    Tem certeza que deseja excluir a categoria <strong class="text-white">{{ deletingCategory?.name }}</strong>? Esta ação não pode ser desfeita.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="deleteCategory"
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                    >
                        Excluir
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
