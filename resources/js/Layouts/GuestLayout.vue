<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#111827] px-4 py-8 relative overflow-hidden">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, #E63B2E 1px, transparent 1px), radial-gradient(circle at 75% 75%, #E63B2E 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#111827] via-[#111827] to-[#1f2937] opacity-90"></div>

        <!-- Content -->
        <div class="relative z-10 w-full max-w-md">
            <!-- Logo -->
            <div class="flex items-center justify-center gap-2 mb-8">
                <Link href="/" class="flex items-center gap-2">
                    <svg class="w-10 h-10 text-[#E63B2E]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 23c-3.866 0-7-3.134-7-7 0-3.148 2.262-5.526 3.5-6.8.342-.352.893-.107.893.378 0 1.108.627 2.102 1.607 2.593 0-3.726 2.16-7.171 5-9.171.342-.241.814.053.737.46C16.18 6.407 18 8.98 18 12.5c0 .238-.012.473-.035.705A3.49 3.49 0 0019 10.5c.076-.407.548-.701.893-.378C20.738 10.974 23 13.352 23 16.5c0 3.866-3.134 7-7 7h-4z"/>
                    </svg>
                    <span class="text-3xl font-bold text-white">Clica<span class="text-[#E63B2E]">Food</span></span>
                </Link>
            </div>

            <!-- Card -->
            <div class="w-full rounded-xl bg-gray-800/80 backdrop-blur-sm px-6 py-8 shadow-xl border border-gray-700/50">
                <slot />
            </div>
        </div>
    </div>
</template>
