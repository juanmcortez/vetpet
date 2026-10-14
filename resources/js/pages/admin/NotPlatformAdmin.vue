<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/layouts/admin/AdminLayout.vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps<{
    clinicSettingsUrl: string;
}>();

const clinicSettingsUrl = computed(
    () =>
        props.clinicSettingsUrl ??
        page.props.vetpet?.links?.clinic_settings ??
        '/',
);
</script>

<template>
    <AdminLayout :title="t('vetpet.admin.access.denied_title')">
        <Head :title="t('vetpet.admin.access.denied_title')" />

        <h1 class="text-2xl font-semibold">
            {{ t('vetpet.admin.access.denied_title') }}
        </h1>
        <p class="mt-3 max-w-xl text-sm text-slate-300">
            {{ t('vetpet.admin.access.denied_description') }}
        </p>
        <a
            :href="clinicSettingsUrl"
            class="mt-6 inline-block text-sm font-medium text-sky-400 underline underline-offset-4"
        >
            {{ t('vetpet.admin.access.clinic_settings_link') }}
        </a>
    </AdminLayout>
</template>
