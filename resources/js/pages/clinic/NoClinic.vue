<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ClinicLayout from '@/layouts/clinic/ClinicLayout.vue';

const { t } = useI18n();

defineOptions({
    layout: ClinicLayout,
});
</script>

<template>
    <Head :title="t('vetpet.clinic.missing_title')" />

    <div class="mx-auto max-w-lg py-10">
        <h1 class="text-2xl font-semibold">
            {{ t('vetpet.clinic.missing_title') }}
        </h1>
        <p class="mt-3 text-sm text-muted-foreground">
            {{ t('vetpet.clinic.missing_description') }}
        </p>
    </div>
</template>
