<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    restaurant: { type: Object, default: () => ({}) },
});

const form = useForm({
    name: props.restaurant?.name || '',
    description: props.restaurant?.description || '',
    primary_color: props.restaurant?.primary_color || '#E63B2E',
    secondary_color: props.restaurant?.secondary_color || '#1a1a1a',
    address: props.restaurant?.address || '',
    phone: props.restaurant?.phone || '',
    instagram: props.restaurant?.instagram || '',
    whatsapp: props.restaurant?.whatsapp || '',
    working_hours: props.restaurant?.working_hours ? JSON.stringify(props.restaurant.working_hours, null, 2) : '',
    logo: null,
});

const logoPreview = ref(props.restaurant?.logo ? `/storage/${props.restaurant.logo}` : null);
const saved = ref(false);

const onLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('settings.update'), {
        forceFormData: true,
        onSuccess: () => { saved.value = true; setTimeout(() => saved.value = false, 3000); },
    });
};
</script>

<template>
    <Head title="Configurações" />
    <AuthenticatedLayout>
        <div class="max-w-3xl mx-auto py-8 px-4">
            <h1 class="text-2xl font-bold text-white mb-8">Configurações do Restaurante</h1>

            <div v-if="saved" class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
                Configurações salvas com sucesso!
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-gray-800 rounded-xl p-6 space-y-6">
                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Logo do Restaurante</label>
                        <input type="file" accept="image/*" @change="onLogoChange"
                            class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand file:text-white file:cursor-pointer" />
                        <img v-if="logoPreview" :src="logoPreview" class="mt-3 w-24 h-24 object-cover rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nome do Restaurante</label>
                        <input v-model="form.name" type="text" required
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:border-brand focus:ring-brand" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Descrição / Bio</label>
                        <textarea v-model="form.description" rows="3"
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:border-brand focus:ring-brand"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Cor Primária</label>
                            <div class="flex items-center gap-3">
                                <input v-model="form.primary_color" type="color" class="w-12 h-10 rounded cursor-pointer bg-transparent" />
                                <input v-model="form.primary_color" type="text" class="flex-1 bg-gray-700 border-gray-600 rounded-lg text-white text-sm" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Cor Secundária</label>
                            <div class="flex items-center gap-3">
                                <input v-model="form.secondary_color" type="color" class="w-12 h-10 rounded cursor-pointer bg-transparent" />
                                <input v-model="form.secondary_color" type="text" class="flex-1 bg-gray-700 border-gray-600 rounded-lg text-white text-sm" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Endereço</label>
                        <input v-model="form.address" type="text"
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:border-brand focus:ring-brand" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Telefone</label>
                            <input v-model="form.phone" type="text"
                                class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:border-brand focus:ring-brand" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">WhatsApp</label>
                            <input v-model="form.whatsapp" type="text"
                                class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:border-brand focus:ring-brand" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Instagram</label>
                        <input v-model="form.instagram" type="text" placeholder="@seurestaurante"
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:border-brand focus:ring-brand" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Horário de Funcionamento</label>
                        <textarea v-model="form.working_hours" rows="4" placeholder="Seg-Sex: 11h-22h&#10;Sáb-Dom: 12h-23h"
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:border-brand focus:ring-brand"></textarea>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="bg-brand hover:bg-brand-dark px-8 py-3 rounded-lg font-semibold transition disabled:opacity-50">
                    {{ form.processing ? 'Salvando...' : 'Salvar Configurações' }}
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
