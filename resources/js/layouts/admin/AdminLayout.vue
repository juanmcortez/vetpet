<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <Head :title="title ?? 'Admin console'" />
    <div class="min-h-screen bg-[#0f172a] text-slate-100">
        <header class="border-b border-slate-700 px-6 py-4">
            <p
                class="text-sm font-medium tracking-wide text-slate-400 uppercase"
            >
                VETPET Admin
            </p>
        </header>
        <main class="mx-auto max-w-4xl px-6 py-10">
            <slot />
        </main>
    </div>
</template>
