<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <Head :title="title ?? 'Owner portal'" />
    <div
        class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]"
    >
        <header
            class="border-b border-[#19140035] px-6 py-4 dark:border-[#3E3E3A]"
        >
            <p class="text-sm font-medium tracking-wide uppercase opacity-70">
                VETPET Owner
            </p>
        </header>
        <main class="mx-auto max-w-3xl px-6 py-10">
            <slot />
        </main>
    </div>
</template>
