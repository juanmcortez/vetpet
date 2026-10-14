<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import LanguageTabs from '@/components/LanguageTabs.vue';
import { useI18n } from 'vue-i18n';
import { edit } from '@/routes/appearance';

const { t } = useI18n();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Appearance settings" />

    <h1 class="sr-only">Appearance settings</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Appearance settings"
            description="Update the appearance settings for your account"
        />
        <AppearanceTabs />

        <div
            class="space-y-3 border-t border-neutral-200 pt-6 dark:border-neutral-800"
        >
            <Heading
                variant="small"
                :title="t('vetpet.settings.language')"
                :description="t('vetpet.settings.language_description')"
            />
            <LanguageTabs />
        </div>
    </div>
</template>
