<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    qrCodes: {
        type: Array,
        default: () => [],
    },
    restaurantSlug: {
        type: String,
        default: '',
    },
});

// Create modal
const showCreateModal = ref(false);
const showDeleteModal = ref(false);
const deletingQrCode = ref(null);

const form = useForm({
    label: '',
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    showCreateModal.value = true;
};

const submitCreate = () => {
    form.post(route('qr-codes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};

// Delete
const confirmDelete = (qrCode) => {
    deletingQrCode.value = qrCode;
    showDeleteModal.value = true;
};

const deleteQrCode = () => {
    router.delete(route('qr-codes.destroy', deletingQrCode.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingQrCode.value = null;
        },
    });
};

// QR Code SVG generator (simple pattern)
const generateQrPattern = (url) => {
    // Simple visual representation using the URL hash for visual variety
    let hash = 0;
    for (let i = 0; i < url.length; i++) {
        hash = ((hash << 5) - hash) + url.charCodeAt(i);
        hash |= 0;
    }

    const cells = [];
    const size = 21;
    for (let row = 0; row < size; row++) {
        for (let col = 0; col < size; col++) {
            // Fixed finder patterns (top-left, top-right, bottom-left)
            const inFinderTL = row < 7 && col < 7;
            const inFinderTR = row < 7 && col >= size - 7;
            const inFinderBL = row >= size - 7 && col < 7;

            if (inFinderTL || inFinderTR || inFinderBL) {
                // Finder pattern borders and center
                const localR = inFinderTL ? row : inFinderTR ? row : row - (size - 7);
                const localC = inFinderTL ? col : inFinderTR ? col - (size - 7) : col;
                const isBorder = localR === 0 || localR === 6 || localC === 0 || localC === 6;
                const isCenter = localR >= 2 && localR <= 4 && localC >= 2 && localC <= 4;
                if (isBorder || isCenter) {
                    cells.push({ row, col });
                }
            } else {
                // Pseudo-random data based on hash
                const seed = (hash + row * 31 + col * 17) & 0xFFFF;
                if (seed % 3 !== 0) {
                    cells.push({ row, col });
                }
            }
        }
    }
    return { cells, size };
};

const copyUrl = (url) => {
    navigator.clipboard.writeText(url);
};
</script>

<template>
    <Head title="QR Codes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-white">QR Codes</h2>
        </template>

        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">QR Codes</h1>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Gerar QR Code
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="qrCodes.length === 0" class="bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h4v4H3V4zm14 0h4v4h-4V4zM3 16h4v4H3v-4zm11 0h1v1h-1v-1zm3 0h1v1h-1v-1zm3 0h1v1h-1v-1zm-3 3h1v1h-1v-1zm3 0h1v1h-1v-1zm-6-6h1v1h-1v-1zm3 0h1v1h-1v-1zm3 0h1v1h-1v-1zm-6 3h1v1h-1v-1z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-300 mb-2">Nenhum QR Code gerado</h3>
                <p class="text-gray-500 mb-6">Gere QR Codes para mesas, balcao ou delivery.</p>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E63B2E] hover:bg-[#c93227] text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Gerar QR Code
                </button>
            </div>

            <!-- QR Codes Grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="qrCode in qrCodes"
                    :key="qrCode.id"
                    class="bg-gray-800 rounded-xl border border-gray-700/50 p-5 hover:border-gray-600 transition-colors"
                >
                    <!-- QR Code Preview -->
                    <div class="flex justify-center mb-4">
                        <div class="bg-white rounded-lg p-3">
                            <svg
                                :viewBox="`0 0 ${generateQrPattern(qrCode.url).size} ${generateQrPattern(qrCode.url).size}`"
                                class="w-32 h-32"
                            >
                                <rect
                                    v-for="(cell, idx) in generateQrPattern(qrCode.url).cells"
                                    :key="idx"
                                    :x="cell.col"
                                    :y="cell.row"
                                    width="1"
                                    height="1"
                                    fill="#111827"
                                />
                            </svg>
                        </div>
                    </div>

                    <!-- Label -->
                    <h3 class="text-base font-semibold text-white text-center mb-2">{{ qrCode.label }}</h3>

                    <!-- URL -->
                    <div class="flex items-center gap-2 bg-gray-900 rounded-lg px-3 py-2 mb-4">
                        <p class="text-xs text-gray-400 truncate flex-1">{{ qrCode.url }}</p>
                        <button
                            @click="copyUrl(qrCode.url)"
                            class="text-gray-400 hover:text-white transition-colors shrink-0"
                            title="Copiar URL"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <a
                            :href="route('qr-codes.download', qrCode.id)"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs font-medium rounded-lg transition-colors"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                        <button
                            @click="confirmDelete(qrCode)"
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

        <!-- Create Modal -->
        <Modal :show="showCreateModal" max-width="md" @close="showCreateModal = false">
            <div class="bg-gray-800 p-6">
                <h3 class="text-lg font-semibold text-white mb-6">Gerar QR Code</h3>

                <form @submit.prevent="submitCreate" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Rotulo</label>
                        <input
                            v-model="form.label"
                            type="text"
                            class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:border-[#E63B2E] focus:ring-1 focus:ring-[#E63B2E] transition-colors"
                            placeholder="Ex: Mesa 1, Balcao, Delivery..."
                            required
                        />
                        <p v-if="form.errors.label" class="mt-1 text-sm text-red-400">{{ form.errors.label }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="showCreateModal = false"
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
                            Gerar
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
                    <h3 class="text-lg font-semibold text-white">Excluir QR Code</h3>
                </div>
                <p class="text-sm text-gray-400 mb-6">
                    Tem certeza que deseja excluir o QR Code <strong class="text-white">{{ deletingQrCode?.label }}</strong>? Esta acao nao pode ser desfeita.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="deleteQrCode"
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors duration-150"
                    >
                        Excluir
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
