<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Witaj" />

    <main class="flex min-h-screen flex-col items-center justify-center gap-6 bg-gray-50 p-8 text-gray-800">
        <h1 class="text-4xl font-semibold">Bear Marketplace</h1>
        <p class="text-lg text-gray-500">Stack Laravel + Inertia + Vue 3 + Vite (SSR) działa poprawnie.</p>
        <p class="text-sm text-gray-400">
            Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}
        </p>
    </main>
</template>
