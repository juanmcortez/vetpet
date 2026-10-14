<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import OwnerLayout from '@/layouts/owner/OwnerLayout.vue';

const { t } = useI18n();
</script>

<template>
    <OwnerLayout :title="t('vetpet.owner.title')">
        <h1 class="text-2xl font-semibold">{{ t('vetpet.owner.title') }}</h1>
        <p class="mt-3 text-sm opacity-80">
            {{ t('vetpet.owner.description') }}
        </p>
    </OwnerLayout>
</template>
