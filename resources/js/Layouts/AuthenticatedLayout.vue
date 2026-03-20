<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showSidebar = ref(false);
const page = usePage();

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: 'home' },
    { name: 'Cardápio', route: 'dashboard', icon: 'utensils' },
    { name: 'Categorias', route: 'dashboard', icon: 'grid' },
    { name: 'QR Code', route: 'dashboard', icon: 'qr' },
    { name: 'Métricas', route: 'dashboard', icon: 'chart' },
    { name: 'Configurações', route: 'dashboard', icon: 'gear' },
    { name: 'Minha Conta', route: 'dashboard', icon: 'user' },
];

const isActive = (routeName) => {
    try {
        return route().current(routeName);
    } catch {
        return false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-950 flex">
        <!-- Mobile sidebar overlay -->
        <div
            v-if="showSidebar"
            class="fixed inset-0 z-40 bg-black/60 lg:hidden"
            @click="showSidebar = false"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 border-r border-gray-800 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static lg:z-auto',
                showSidebar ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center gap-2 px-5 py-5 border-b border-gray-800">
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <!-- Flame icon -->
                    <svg class="w-8 h-8 text-[#E63B2E]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 23c-3.866 0-7-3.134-7-7 0-3.148 2.262-5.526 3.5-6.8.342-.352.893-.107.893.378 0 1.108.627 2.102 1.607 2.593 0-3.726 2.16-7.171 5-9.171.342-.241.814.053.737.46C16.18 6.407 18 8.98 18 12.5c0 .238-.012.473-.035.705A3.49 3.49 0 0019 10.5c.076-.407.548-.701.893-.378C20.738 10.974 23 13.352 23 16.5c0 3.866-3.134 7-7 7h-4z"/>
                    </svg>
                    <span class="text-xl font-bold text-white">Clica<span class="text-[#E63B2E]">Food</span></span>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150',
                        isActive(item.route)
                            ? 'bg-[#E63B2E]/10 text-[#E63B2E]'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800',
                    ]"
                >
                    <!-- Home icon -->
                    <svg v-if="item.icon === 'home'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                    </svg>
                    <!-- Utensils icon -->
                    <svg v-else-if="item.icon === 'utensils'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <!-- Grid icon -->
                    <svg v-else-if="item.icon === 'grid'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <!-- QR icon -->
                    <svg v-else-if="item.icon === 'qr'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h4v4H3V4zm14 0h4v4h-4V4zM3 16h4v4H3v-4zm11 0h1v1h-1v-1zm3 0h1v1h-1v-1zm3 0h1v1h-1v-1zm-3 3h1v1h-1v-1zm3 0h1v1h-1v-1zm-6-6h1v1h-1v-1zm3 0h1v1h-1v-1zm3 0h1v1h-1v-1zm-6 3h1v1h-1v-1z"/>
                    </svg>
                    <!-- Chart icon -->
                    <svg v-else-if="item.icon === 'chart'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2v8H3v-8zm6-4h2v12H9V9zm6-5h2v17h-2V4zm6 8h2v9h-2v-9z"/>
                    </svg>
                    <!-- Gear icon -->
                    <svg v-else-if="item.icon === 'gear'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <!-- User icon -->
                    <svg v-else-if="item.icon === 'user'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 7a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>{{ item.name }}</span>
                </Link>
            </nav>

            <!-- Logout -->
            <div class="px-3 py-4 border-t border-gray-800">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition-colors duration-150"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sair</span>
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top bar -->
            <header class="sticky top-0 z-30 bg-gray-900 border-b border-gray-800">
                <div class="flex items-center justify-between px-4 sm:px-6 h-16">
                    <!-- Hamburger (mobile) -->
                    <button
                        @click="showSidebar = !showSidebar"
                        class="lg:hidden inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:text-white hover:bg-gray-800 transition"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Page heading slot -->
                    <div class="hidden lg:block">
                        <slot name="header" />
                    </div>

                    <!-- User info -->
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-white">{{ $page.props.auth.user.name }}</p>
                            <p class="text-xs text-gray-400">{{ $page.props.auth.user.email }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-[#E63B2E] flex items-center justify-center text-white text-sm font-bold">
                            {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
