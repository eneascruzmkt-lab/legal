<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    item: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const isEditing = computed(() => !!props.item);

const form = useForm({
    name: props.item?.name ?? '',
    description: props.item?.description ?? '',
    price: props.item?.price ?? '',
    category_id: props.item?.category_id ?? '',
    image: null,
    video_url: null,
    featured: props.item?.featured ?? false,
    available: props.item?.available ?? true,
});

// Image preview
const imagePreview = ref(props.item?.image_url ?? null);
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

// Video preview
const videoPreview = ref(props.item?.video_url ? `/storage/${props.item.video_url}` : null);
const handleVideoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.video_url = file;
        videoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    if (isEditing.value) {
        form.post(route('menu-items.update', props.item.id), {
            forceFormData: true,
            preserveScroll: true,
        });
    } else {
        form.post(route('menu-items.store'), {
            forceFormData: true,
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Item' : 'Novo Item'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-white">{{ isEditing ? 'Editar Item' : 'Novo Item' }}</h2>
        </template>

        <div class="max-w-3xl mx-auto">
            <!-- Back link -->
            <div class="mb-6">
                <Link
                    :href="route('menu-items.index')"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar para o cardapio
                </Link>
            </div>

            <div class="bg-gray-800 rounded-xl border border-gray-700/50 p-6">
                <h1 class="text-xl font-bold text-white mb-6">
                    {{ isEditing ? 'Editar Item' : 'Novo Item' }}
                </h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nome</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:border-[#E63B2E] focus:ring-1 focus:ring-[#E63B2E] transition-colors"
                            placeholder="Ex: X-Burger Especial"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Descricao</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:border-[#E63B2E] focus:ring-1 focus:ring-[#E63B2E] transition-colors resize-none"
                            placeholder="Descreva o item do cardapio..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-400">{{ form.errors.description }}</p>
                    </div>

                    <!-- Price and Category -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Preco (R$)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">R$</span>
                                <input
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full bg-gray-900 border border-gray-600 rounded-lg pl-10 pr-3 py-2.5 text-white text-sm placeholder-gray-500 focus:border-[#E63B2E] focus:ring-1 focus:ring-[#E63B2E] transition-colors"
                                    placeholder="0,00"
                                    required
                                />
                            </div>
                            <p v-if="form.errors.price" class="mt-1 text-sm text-red-400">{{ form.errors.price }}</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Categoria</label>
                            <select
                                v-model="form.category_id"
                                class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-white text-sm focus:border-[#E63B2E] focus:ring-1 focus:ring-[#E63B2E] transition-colors"
                                required
                            >
                                <option value="" disabled>Selecione uma categoria</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-400">{{ form.errors.category_id }}</p>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Imagem</label>
                        <div class="flex items-start gap-4">
                            <div v-if="imagePreview" class="w-24 h-24 rounded-lg overflow-hidden bg-gray-700 shrink-0">
                                <img :src="imagePreview" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Escolher imagem
                                    <input
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleImageChange"
                                    />
                                </label>
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG ate 2MB</p>
                            </div>
                        </div>
                        <p v-if="form.errors.image" class="mt-1 text-sm text-red-400">{{ form.errors.image }}</p>
                    </div>

                    <!-- Video Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Video</label>
                        <div class="flex flex-col gap-4">
                            <div v-if="videoPreview" class="w-full max-w-sm rounded-lg overflow-hidden bg-gray-700">
                                <video :src="videoPreview" controls class="w-full" />
                            </div>
                            <div>
                                <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Escolher video
                                    <input
                                        type="file"
                                        accept="video/*"
                                        class="hidden"
                                        @change="handleVideoChange"
                                    />
                                </label>
                                <p class="text-xs text-gray-500 mt-2">MP4, WebM ate 20MB</p>
                            </div>
                        </div>
                        <p v-if="form.errors.video_url" class="mt-1 text-sm text-red-400">{{ form.errors.video_url }}</p>
                    </div>

                    <!-- Toggles -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Featured -->
                        <div class="flex items-center justify-between bg-gray-900 rounded-lg px-4 py-3">
                            <div>
                                <label class="text-sm font-medium text-gray-300">Destaque</label>
                                <p class="text-xs text-gray-500">Exibir como item em destaque</p>
                            </div>
                            <button
                                type="button"
                                @click="form.featured = !form.featured"
                                :class="[
                                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                    form.featured ? 'bg-[#E63B2E]' : 'bg-gray-600',
                                ]"
                            >
                                <span
                                    :class="[
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                        form.featured ? 'translate-x-5' : 'translate-x-0',
                                    ]"
                                />
                            </button>
                        </div>

                        <!-- Available -->
                        <div class="flex items-center justify-between bg-gray-900 rounded-lg px-4 py-3">
                            <div>
                                <label class="text-sm font-medium text-gray-300">Disponivel</label>
                                <p class="text-xs text-gray-500">Item disponivel para pedidos</p>
                            </div>
                            <button
                                type="button"
                                @click="form.available = !form.available"
                                :class="[
                                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                    form.available ? 'bg-[#E63B2E]' : 'bg-gray-600',
                                ]"
                            >
                                <span
                                    :class="[
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                        form.available ? 'translate-x-5' : 'translate-x-0',
                                    ]"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-700/50">
                        <Link
                            :href="route('menu-items.index')"
                            class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            {{ isEditing ? 'Salvar Alteracoes' : 'Criar Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
