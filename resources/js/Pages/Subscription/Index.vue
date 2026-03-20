<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    subscription: {
        type: Object,
        default: null,
    },
});

const currentPlan = computed(() => {
    if (!props.subscription || props.subscription.status === 'cancelled') return 'free';
    return props.subscription.plan || 'free';
});

const isProcessing = ref(false);
const showUpgradeModal = ref(false);
const showCancelModal = ref(false);

const upgrade = () => {
    isProcessing.value = true;
    router.post(route('subscription.upgrade'), {}, {
        preserveScroll: true,
        onFinish: () => {
            isProcessing.value = false;
            showUpgradeModal.value = false;
        },
    });
};

const cancel = () => {
    isProcessing.value = true;
    router.post(route('subscription.cancel'), {}, {
        preserveScroll: true,
        onFinish: () => {
            isProcessing.value = false;
            showCancelModal.value = false;
        },
    });
};

const plans = [
    {
        id: 'free',
        name: 'Free',
        price: 'Gratis',
        priceDetail: 'para sempre',
        features: [
            { text: 'Ate 10 itens no cardapio', included: true },
            { text: 'Upload de video', included: false },
            { text: 'QR Code basico', included: true },
            { text: 'Metricas avancadas', included: false },
            { text: 'QR Codes ilimitados', included: false },
            { text: 'Suporte prioritario', included: false },
        ],
    },
    {
        id: 'pro',
        name: 'Pro',
        price: 'R$ 97',
        priceDetail: '/mes',
        features: [
            { text: 'Itens ilimitados', included: true },
            { text: 'Upload de video', included: true },
            { text: 'QR Codes ilimitados', included: true },
            { text: 'Metricas avancadas', included: true },
            { text: 'Personalizacao completa', included: true },
            { text: 'Suporte prioritario', included: true },
        ],
    },
];

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Assinatura" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-white">Assinatura</h2>
        </template>

        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-white mb-2">Assinatura</h1>
            <p class="text-gray-400 mb-8">Gerencie seu plano e assinatura.</p>

            <!-- Current Plan Info -->
            <div v-if="subscription && currentPlan === 'pro'" class="bg-gray-800 rounded-xl border border-gray-700/50 p-5 mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-lg font-semibold text-white">Plano Pro</h3>
                            <span class="inline-flex items-center px-2 py-0.5 bg-green-500/10 text-green-400 text-xs font-semibold rounded-full">
                                Ativo
                            </span>
                        </div>
                        <p class="text-sm text-gray-400">
                            <span v-if="subscription.ends_at">Valido ate {{ formatDate(subscription.ends_at) }}</span>
                            <span v-else>Assinatura ativa</span>
                        </p>
                    </div>
                    <button
                        @click="showCancelModal = true"
                        class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-red-400 bg-gray-700 hover:bg-gray-700/80 rounded-lg transition-colors"
                    >
                        Cancelar assinatura
                    </button>
                </div>
            </div>

            <!-- Plan Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    :class="[
                        'relative bg-gray-800 rounded-xl border-2 p-6 transition-colors',
                        currentPlan === plan.id
                            ? 'border-[#E63B2E]'
                            : 'border-gray-700/50 hover:border-gray-600',
                    ]"
                >
                    <!-- Current plan badge -->
                    <div v-if="currentPlan === plan.id" class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="inline-flex items-center px-3 py-1 bg-[#E63B2E] text-white text-xs font-bold rounded-full">
                            Plano Atual
                        </span>
                    </div>

                    <!-- Recommended badge -->
                    <div v-if="plan.id === 'pro' && currentPlan !== 'pro'" class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="inline-flex items-center px-3 py-1 bg-yellow-500 text-yellow-900 text-xs font-bold rounded-full">
                            Recomendado
                        </span>
                    </div>

                    <div class="text-center mb-6 pt-2">
                        <h3 class="text-xl font-bold text-white mb-2">{{ plan.name }}</h3>
                        <div class="flex items-baseline justify-center gap-1">
                            <span class="text-3xl font-bold text-white">{{ plan.price }}</span>
                            <span class="text-sm text-gray-400">{{ plan.priceDetail }}</span>
                        </div>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-3 mb-8">
                        <li
                            v-for="(feature, idx) in plan.features"
                            :key="idx"
                            class="flex items-center gap-3"
                        >
                            <svg
                                v-if="feature.included"
                                class="w-5 h-5 text-green-400 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg
                                v-else
                                class="w-5 h-5 text-gray-600 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span :class="feature.included ? 'text-sm text-gray-300' : 'text-sm text-gray-500'">
                                {{ feature.text }}
                            </span>
                        </li>
                    </ul>

                    <!-- Action Button -->
                    <div>
                        <button
                            v-if="plan.id === 'pro' && currentPlan !== 'pro'"
                            @click="showUpgradeModal = true"
                            class="w-full py-3 bg-[#E63B2E] hover:bg-[#c93227] text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                        >
                            Assinar Pro
                        </button>
                        <button
                            v-else-if="plan.id === 'free' && currentPlan === 'pro'"
                            @click="showCancelModal = true"
                            class="w-full py-3 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-semibold rounded-lg transition-colors duration-150"
                        >
                            Fazer downgrade
                        </button>
                        <div
                            v-else
                            class="w-full py-3 bg-gray-700/50 text-gray-500 text-sm font-semibold rounded-lg text-center cursor-default"
                        >
                            Plano atual
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upgrade Modal -->
        <Modal :show="showUpgradeModal" max-width="sm" @close="showUpgradeModal = false">
            <div class="bg-gray-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-[#E63B2E]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#E63B2E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Assinar Plano Pro</h3>
                </div>
                <p class="text-sm text-gray-400 mb-2">
                    Voce esta prestes a assinar o plano Pro por <strong class="text-white">R$ 97/mes</strong>.
                </p>
                <p class="text-sm text-gray-400 mb-6">
                    Tenha acesso a itens ilimitados, upload de videos, metricas avancadas e muito mais.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button
                        @click="showUpgradeModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="upgrade"
                        :disabled="isProcessing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                    >
                        <svg v-if="isProcessing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Confirmar Assinatura
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Cancel Modal -->
        <Modal :show="showCancelModal" max-width="sm" @close="showCancelModal = false">
            <div class="bg-gray-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Cancelar Assinatura</h3>
                </div>
                <p class="text-sm text-gray-400 mb-6">
                    Tem certeza que deseja cancelar sua assinatura Pro? Voce perdera acesso a funcionalidades exclusivas como upload de videos, metricas avancadas e itens ilimitados.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button
                        @click="showCancelModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                    >
                        Manter Plano
                    </button>
                    <button
                        @click="cancel"
                        :disabled="isProcessing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                    >
                        <svg v-if="isProcessing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Cancelar Assinatura
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
