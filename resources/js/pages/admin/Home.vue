<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/layouts/admin/AdminLayout.vue';

const { t } = useI18n();
const page = usePage();

const user = computed(
    () => page.props.auth?.user as { is_platform_admin?: boolean } | null,
);
const isPlatformAdmin = computed(() => Boolean(user.value?.is_platform_admin));
const clinicSettingsUrl = computed(
    () =>
        (page.props.vetpet as { links?: { clinic_settings?: string } })?.links
            ?.clinic_settings ?? '/',
);
</script>

<template>
    <AdminLayout :title="t('vetpet.admin.title')">
        <h1 class="text-2xl font-semibold">{{ t('vetpet.admin.title') }}</h1>
        <p class="mt-3 text-sm text-slate-300">
            {{ t('vetpet.admin.description') }}
        </p>

        <template v-if="isPlatformAdmin">
            <a
                href="/clinics"
                class="mt-6 inline-block text-sm font-medium text-sky-400 underline underline-offset-4"
            >
                {{ t('vetpet.admin.clinics.manage_link') }}
            </a>
        </template>
        <template v-else-if="user">
            <p class="mt-6 max-w-xl text-sm text-slate-300">
                {{ t('vetpet.admin.access.use_clinic_surface') }}
            </p>
            <a
                :href="clinicSettingsUrl"
                class="mt-3 inline-block text-sm font-medium text-sky-400 underline underline-offset-4"
            >
                {{ t('vetpet.admin.access.clinic_settings_link') }}
            </a>
        </template>
        <template v-else>
            <p class="mt-6 text-sm text-slate-300">
                {{ t('vetpet.admin.access.sign_in_clinic') }}
            </p>
            <a
                :href="
                    (page.props.vetpet as { links?: { clinic_login?: string } })
                        ?.links?.clinic_login
                "
                class="mt-3 inline-block text-sm font-medium text-sky-400 underline underline-offset-4"
            >
                {{ t('vetpet.admin.access.clinic_login_link') }}
            </a>
        </template>
    </AdminLayout>
</template>
