<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    restaurant: {
        type: Object,
        required: true,
    },
});

// --- Computed restaurant data ---
const restaurantName = computed(() => props.restaurant.name || 'Restaurante');
const restaurantDescription = computed(() => props.restaurant.description || '');
const restaurantLogo = computed(() => props.restaurant.logo_url || props.restaurant.logo || '');
const restaurantWhatsapp = computed(() => props.restaurant.whatsapp || '');
const restaurantInstagram = computed(() => props.restaurant.instagram || '');
const workingHours = computed(() => props.restaurant.working_hours || props.restaurant.workingHours || '');
const categories = computed(() => props.restaurant.categories || []);
const primaryColor = computed(() => props.restaurant.primary_color || '#E63B2E');

// --- Stories ---
const storiesItems = computed(() => {
    if (!props.restaurant.categories) return [];
    const items = [];
    props.restaurant.categories.forEach(cat => {
        (cat.menu_items || cat.menuItems || []).forEach(item => {
            if (item.video_url || item.video) {
                items.push({ ...item, categoryName: cat.name });
            }
        });
    });
    return items;
});

const showStoryModal = ref(false);
const currentStoryIndex = ref(0);
const storyVideoRef = ref(null);

const currentStory = computed(() => storiesItems.value[currentStoryIndex.value] || null);

function openStory(index) {
    currentStoryIndex.value = index;
    showStoryModal.value = true;
    document.body.style.overflow = 'hidden';
    nextTick(() => {
        if (storyVideoRef.value) {
            storyVideoRef.value.play().catch(() => {});
        }
    });
}

function closeStory() {
    showStoryModal.value = false;
    document.body.style.overflow = '';
    if (storyVideoRef.value) {
        storyVideoRef.value.pause();
    }
}

function nextStory() {
    if (currentStoryIndex.value < storiesItems.value.length - 1) {
        currentStoryIndex.value++;
        nextTick(() => {
            if (storyVideoRef.value) {
                storyVideoRef.value.load();
                storyVideoRef.value.play().catch(() => {});
            }
        });
    } else {
        closeStory();
    }
}

function prevStory() {
    if (currentStoryIndex.value > 0) {
        currentStoryIndex.value--;
        nextTick(() => {
            if (storyVideoRef.value) {
                storyVideoRef.value.load();
                storyVideoRef.value.play().catch(() => {});
            }
        });
    }
}

// Touch swipe for stories
let touchStartX = 0;
function onStoryTouchStart(e) {
    touchStartX = e.touches[0].clientX;
}
function onStoryTouchEnd(e) {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) {
        if (diff > 0) nextStory();
        else prevStory();
    }
}

// Keyboard navigation for stories
function onStoryKeydown(e) {
    if (!showStoryModal.value) return;
    if (e.key === 'Escape') closeStory();
    if (e.key === 'ArrowRight') nextStory();
    if (e.key === 'ArrowLeft') prevStory();
}

// --- Video Modal (for individual items) ---
const showVideoModal = ref(false);
const videoModalItem = ref(null);
const itemVideoRef = ref(null);

function openVideoModal(item) {
    videoModalItem.value = item;
    showVideoModal.value = true;
    document.body.style.overflow = 'hidden';
    nextTick(() => {
        if (itemVideoRef.value) {
            itemVideoRef.value.play().catch(() => {});
        }
    });
}

function closeVideoModal() {
    showVideoModal.value = false;
    document.body.style.overflow = '';
    if (itemVideoRef.value) {
        itemVideoRef.value.pause();
    }
    videoModalItem.value = null;
}

// --- Category tabs & scroll ---
const activeCategory = ref(null);
const categoryRefs = ref({});
const categoryTabsRef = ref(null);

function setCategoryRef(id, el) {
    if (el) categoryRefs.value[id] = el;
}

function scrollToCategory(categoryId) {
    activeCategory.value = categoryId;
    const el = categoryRefs.value[categoryId];
    if (el) {
        const offset = 120;
        const top = el.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
    }
}

// Intersection Observer for category highlighting
let observer = null;
onMounted(() => {
    if (props.restaurant.categories?.length) {
        activeCategory.value = props.restaurant.categories[0].id;
    }

    window.addEventListener('keydown', onStoryKeydown);

    nextTick(() => {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        activeCategory.value = Number(entry.target.dataset.categoryId);
                        const tabEl = document.querySelector(`[data-tab-id="${entry.target.dataset.categoryId}"]`);
                        if (tabEl && categoryTabsRef.value) {
                            tabEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                        }
                    }
                });
            },
            { rootMargin: '-120px 0px -60% 0px', threshold: 0 }
        );

        Object.values(categoryRefs.value).forEach(el => {
            if (el) observer.observe(el);
        });
    });
});

onUnmounted(() => {
    if (observer) observer.disconnect();
    window.removeEventListener('keydown', onStoryKeydown);
    document.body.style.overflow = '';
});

// --- Helpers ---
function formatPrice(price) {
    const num = Number(price);
    if (isNaN(num)) return 'R$ 0,00';
    return 'R$ ' + num.toFixed(2).replace('.', ',');
}

function getInitials(name) {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).filter(Boolean).slice(0, 2).join('').toUpperCase();
}

function getVideoUrl(item) {
    return item.video_url || item.video || '';
}

function getImageUrl(item) {
    return item.image_url || item.image || '';
}
</script>

<template>
    <Head>
        <title>{{ restaurantName }} - Cardapio Digital</title>
        <meta name="description" :content="`Cardapio digital de ${restaurantName}. Veja nosso menu completo com fotos e videos dos pratos.`" />
        <meta property="og:title" :content="`${restaurantName} - Cardapio Digital`" />
        <meta property="og:description" :content="restaurantDescription || `Confira o cardapio completo de ${restaurantName}`" />
        <meta property="og:image" :content="restaurantLogo" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="`${restaurantName} - Cardapio Digital`" />
        <meta name="twitter:description" :content="restaurantDescription" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    </Head>

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-[#111827] text-white">
            <div class="max-w-lg mx-auto px-4 py-6">
                <div class="flex items-center gap-4">
                    <!-- Logo -->
                    <div v-if="restaurantLogo" class="w-18 h-18 rounded-full overflow-hidden border-2 border-[#E63B2E] shrink-0" style="width: 72px; height: 72px;">
                        <img :src="restaurantLogo" :alt="restaurantName" class="w-full h-full object-cover" loading="lazy" />
                    </div>
                    <div v-else class="rounded-full bg-[#E63B2E] flex items-center justify-center text-white text-xl font-bold shrink-0" style="width: 72px; height: 72px;">
                        {{ getInitials(restaurantName) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-bold truncate">{{ restaurantName }}</h1>
                        <p v-if="restaurantDescription" class="text-sm text-gray-300 mt-1 line-clamp-2">{{ restaurantDescription }}</p>
                        <div class="flex items-center gap-3 mt-2">
                            <!-- Working hours -->
                            <span v-if="workingHours" class="inline-flex items-center gap-1.5 text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                {{ workingHours }}
                            </span>

                            <!-- Social links -->
                            <div class="flex items-center gap-2">
                                <a v-if="restaurantWhatsapp" :href="`https://wa.me/${restaurantWhatsapp.replace(/\\D/g, '')}`" target="_blank" rel="noopener" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-gray-300 hover:bg-green-500/20 hover:text-green-400 transition-all">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                                <a v-if="restaurantInstagram" :href="`https://instagram.com/${restaurantInstagram.replace('@', '')}`" target="_blank" rel="noopener" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-gray-300 hover:bg-pink-500/20 hover:text-pink-400 transition-all">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stories Section -->
        <section v-if="storiesItems.length" class="bg-white border-b border-gray-200">
            <div class="max-w-lg mx-auto px-4 py-4">
                <div class="flex gap-4 overflow-x-auto pb-2 stories-scroll" style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">
                    <button
                        v-for="(item, index) in storiesItems"
                        :key="item.id || index"
                        class="flex flex-col items-center gap-1.5 shrink-0 focus:outline-none group"
                        style="scroll-snap-align: start;"
                        @click="openStory(index)"
                    >
                        <div class="w-[68px] h-[68px] rounded-full p-[3px]" :style="{ background: `linear-gradient(135deg, ${primaryColor}, #f97316)` }">
                            <div class="w-full h-full rounded-full overflow-hidden border-2 border-white bg-gray-100">
                                <img
                                    v-if="getImageUrl(item)"
                                    :src="getImageUrl(item)"
                                    :alt="item.name"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                    loading="lazy"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                </div>
                            </div>
                        </div>
                        <span class="text-[11px] text-gray-700 font-medium truncate max-w-[72px] text-center leading-tight">{{ item.name }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Category Tabs -->
        <nav v-if="categories.length" class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-lg mx-auto">
                <div ref="categoryTabsRef" class="flex overflow-x-auto scrollbar-hide" style="-webkit-overflow-scrolling: touch;">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        :data-tab-id="category.id"
                        class="shrink-0 px-4 py-3 text-sm font-medium transition-colors whitespace-nowrap border-b-2"
                        :class="activeCategory === category.id
                            ? 'text-[#E63B2E] border-[#E63B2E]'
                            : 'text-gray-500 border-transparent hover:text-gray-700'"
                        @click="scrollToCategory(category.id)"
                    >
                        {{ category.name }}
                    </button>
                </div>
            </div>
        </nav>

        <!-- Menu Items by Category -->
        <main class="max-w-lg mx-auto px-4 py-6 space-y-8">
            <section
                v-for="category in categories"
                :key="category.id"
                :ref="(el) => setCategoryRef(category.id, el)"
                :data-category-id="category.id"
            >
                <!-- Category Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div v-if="category.image_url || category.image" class="w-12 h-12 rounded-xl overflow-hidden shrink-0 shadow-sm">
                        <img :src="category.image_url || category.image" :alt="category.name" class="w-full h-full object-cover" loading="lazy" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ category.name }}</h2>
                        <p v-if="category.description" class="text-xs text-gray-500">{{ category.description }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="space-y-3">
                    <div
                        v-for="item in (category.menu_items || category.menuItems || [])"
                        :key="item.id"
                        class="flex gap-3 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow active:scale-[0.99] duration-150"
                    >
                        <!-- Image -->
                        <div class="relative w-28 h-28 shrink-0 bg-gray-100">
                            <img
                                v-if="getImageUrl(item)"
                                :src="getImageUrl(item)"
                                :alt="item.name"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                            </div>
                            <!-- Play button overlay -->
                            <button
                                v-if="getVideoUrl(item)"
                                class="absolute inset-0 flex items-center justify-center bg-black/20 hover:bg-black/40 transition-colors"
                                @click.stop="openVideoModal(item)"
                            >
                                <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg backdrop-blur-sm">
                                    <svg class="w-5 h-5 text-[#E63B2E] ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                </div>
                            </button>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 py-3 pr-3 flex flex-col justify-between min-w-0">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ item.name }}</h3>
                                <p v-if="item.description" class="text-xs text-gray-500 mt-1 line-clamp-2">{{ item.description }}</p>
                            </div>
                            <p class="text-[#E63B2E] font-bold text-base mt-2">{{ formatPrice(item.price) }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Empty state -->
            <div v-if="!categories.length" class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                <p class="text-gray-500 text-sm">O cardapio esta sendo preparado.</p>
                <p class="text-gray-400 text-xs mt-1">Volte em breve!</p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-[#111827] text-white py-8">
            <div class="max-w-lg mx-auto px-4 text-center">
                <h3 class="font-bold text-lg mb-2">{{ restaurantName }}</h3>
                <p v-if="restaurantDescription" class="text-sm text-gray-400 mb-4">{{ restaurantDescription }}</p>
                <div class="flex items-center justify-center gap-3 mb-6">
                    <a v-if="restaurantWhatsapp" :href="`https://wa.me/${restaurantWhatsapp.replace(/\\D/g, '')}`" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-gray-400 hover:text-green-400 hover:bg-green-500/10 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <a v-if="restaurantInstagram" :href="`https://instagram.com/${restaurantInstagram.replace('@', '')}`" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-gray-400 hover:text-pink-400 hover:bg-pink-500/10 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
                <div class="border-t border-gray-700 pt-4">
                    <p class="text-xs text-gray-500">
                        Powered by
                        <span class="font-bold text-gray-400">Clica<span class="text-[#E63B2E]">Food</span></span>
                    </p>
                </div>
            </div>
        </footer>

        <!-- Story Modal (Fullscreen) -->
        <Teleport to="body">
            <div
                v-if="showStoryModal && currentStory"
                class="fixed inset-0 z-50 bg-black flex items-center justify-center"
                @touchstart="onStoryTouchStart"
                @touchend="onStoryTouchEnd"
            >
                <!-- Close -->
                <button
                    class="absolute top-4 right-4 z-20 w-10 h-10 flex items-center justify-center text-white hover:bg-white/10 rounded-full transition"
                    @click="closeStory"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <!-- Prev -->
                <button
                    v-if="currentStoryIndex > 0"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 flex items-center justify-center text-white hover:bg-white/10 rounded-full transition hidden sm:flex"
                    @click="prevStory"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>

                <!-- Next -->
                <button
                    v-if="currentStoryIndex < storiesItems.length - 1"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 flex items-center justify-center text-white hover:bg-white/10 rounded-full transition hidden sm:flex"
                    @click="nextStory"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>

                <!-- Progress bar -->
                <div class="absolute top-2 left-4 right-4 flex gap-1 z-20">
                    <div
                        v-for="(s, i) in storiesItems"
                        :key="s.id || i"
                        class="flex-1 h-0.5 rounded-full overflow-hidden transition-colors duration-300"
                        :class="i <= currentStoryIndex ? 'bg-white' : 'bg-white/30'"
                    ></div>
                </div>

                <!-- Video -->
                <video
                    ref="storyVideoRef"
                    :src="getVideoUrl(currentStory)"
                    class="max-w-full max-h-full object-contain"
                    playsinline
                    webkit-playsinline
                    @ended="nextStory"
                ></video>

                <!-- Bottom overlay -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6 pt-20">
                    <p class="text-white/60 text-xs uppercase tracking-wider mb-1">{{ currentStory.categoryName }}</p>
                    <h3 class="text-white font-bold text-xl">{{ currentStory.name }}</h3>
                    <p class="text-[#E63B2E] font-bold text-xl mt-1">{{ formatPrice(currentStory.price) }}</p>
                </div>
            </div>
        </Teleport>

        <!-- Video Modal (individual items) -->
        <Teleport to="body">
            <div
                v-if="showVideoModal && videoModalItem"
                class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center"
                @click.self="closeVideoModal"
            >
                <!-- Close -->
                <button
                    class="absolute top-4 right-4 z-20 w-10 h-10 flex items-center justify-center text-white hover:bg-white/10 rounded-full transition"
                    @click="closeVideoModal"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <!-- Video -->
                <video
                    ref="itemVideoRef"
                    :src="getVideoUrl(videoModalItem)"
                    class="max-w-full max-h-[80vh] object-contain rounded-lg"
                    controls
                    playsinline
                    webkit-playsinline
                ></video>

                <!-- Bottom details -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6 pt-20 pointer-events-none">
                    <h3 class="text-white font-bold text-lg">{{ videoModalItem.name }}</h3>
                    <p v-if="videoModalItem.description" class="text-gray-300 text-sm mt-1 line-clamp-2">{{ videoModalItem.description }}</p>
                    <p class="text-[#E63B2E] font-bold text-lg mt-1">{{ formatPrice(videoModalItem.price) }}</p>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.scrollbar-hide,
.stories-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar,
.stories-scroll::-webkit-scrollbar {
    display: none;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
