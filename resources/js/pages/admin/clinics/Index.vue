<script setup lang="ts">
import { Form, Head, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/layouts/admin/AdminLayout.vue';

type ClinicRow = {
    id: number;
    name: string;
    slug: string;
    timezone: string | null;
    currency: string | null;
    is_active: boolean;
};

type Paginated<T> = {
    data: T[];
};

const { t } = useI18n();

const props = defineProps<{
    clinics: Paginated<ClinicRow>;
}>();

const createForm = useForm({
    name: '',
    slug: '',
    is_active: true,
});

function submitCreate() {
    createForm.post('/clinics', {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
}

function toggleActive(clinic: ClinicRow) {
    router.patch(
        `/clinics/${clinic.id}`,
        {
            name: clinic.name,
            slug: clinic.slug,
            timezone: clinic.timezone,
            currency: clinic.currency,
            is_active: !clinic.is_active,
        },
        { preserveScroll: true },
    );
}
</script>

<template>
    <AdminLayout :title="t('vetpet.admin.clinics.title')">
        <Head :title="t('vetpet.admin.clinics.title')" />

        <h1 class="text-2xl font-semibold">
            {{ t('vetpet.admin.clinics.title') }}
        </h1>
        <p class="mt-2 text-sm text-slate-300">
            {{ t('vetpet.admin.clinics.description') }}
        </p>

        <Form
            class="mt-8 grid gap-4 rounded-lg border border-slate-700 p-4"
            @submit.prevent="submitCreate"
        >
            <h2 class="text-sm font-medium text-slate-200">
                {{ t('vetpet.admin.clinics.create_heading') }}
            </h2>
            <div class="grid gap-3 md:grid-cols-2">
                <label class="grid gap-1 text-sm">
                    <span>{{ t('vetpet.admin.clinics.name') }}</span>
                    <input
                        v-model="createForm.name"
                        class="rounded border border-slate-600 bg-slate-900 px-3 py-2"
                        required
                    />
                </label>
                <label class="grid gap-1 text-sm">
                    <span>{{ t('vetpet.admin.clinics.slug') }}</span>
                    <input
                        v-model="createForm.slug"
                        class="rounded border border-slate-600 bg-slate-900 px-3 py-2"
                        required
                    />
                </label>
            </div>
            <button
                type="submit"
                class="w-fit rounded bg-sky-600 px-4 py-2 text-sm font-medium"
                :disabled="createForm.processing"
            >
                {{ t('vetpet.admin.clinics.create_submit') }}
            </button>
        </Form>

        <ul
            class="mt-10 divide-y divide-slate-700 rounded-lg border border-slate-700"
        >
            <li
                v-for="clinic in props.clinics.data"
                :key="clinic.id"
                class="flex items-center justify-between gap-4 px-4 py-3 text-sm"
            >
                <div>
                    <p class="font-medium">{{ clinic.name }}</p>
                    <p class="text-slate-400">{{ clinic.slug }}</p>
                </div>
                <button
                    type="button"
                    class="rounded border border-slate-600 px-3 py-1 text-xs"
                    @click="toggleActive(clinic)"
                >
                    {{
                        clinic.is_active
                            ? t('vetpet.admin.clinics.deactivate')
                            : t('vetpet.admin.clinics.activate')
                    }}
                </button>
            </li>
        </ul>
    </AdminLayout>
</template>
