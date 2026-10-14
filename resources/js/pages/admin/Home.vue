<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/layouts/admin/AdminLayout.vue';

const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('vetpet.admin.title')">
        <h1 class="text-2xl font-semibold">{{ t('vetpet.admin.title') }}</h1>
        <p class="mt-3 text-sm text-slate-300">
            {{ t('vetpet.admin.description') }}
        </p>
        <a
            href="/clinics"
            class="mt-6 inline-block text-sm font-medium text-sky-400 underline underline-offset-4"
        >
            {{ t('vetpet.admin.clinics.manage_link') }}
        </a>
    </AdminLayout>
</template>
