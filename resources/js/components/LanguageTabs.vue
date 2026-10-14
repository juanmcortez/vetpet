<script setup lang="ts">
import { useLocale } from '@/composables/useLocale';

const { locale, locales, setLocale, labelFor } = useLocale();
</script>

<template>
    <div
        class="inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"
    >
        <button
            v-for="code in locales"
            :key="code"
            type="button"
            @click="setLocale(code)"
            :class="[
                'rounded-md px-3.5 py-1.5 text-sm transition-colors',
                locale === code
                    ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                    : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
            ]"
        >
            {{ labelFor(code) }}
        </button>
    </div>
</template>
