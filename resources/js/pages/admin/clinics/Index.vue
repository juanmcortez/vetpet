<script setup lang="ts">
import { Form, Head, router, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/layouts/admin/AdminLayout.vue';

type MemberRow = {
    id: number;
    role: string;
    user: { id: number; name: string; email: string };
};

type ClinicRow = {
    id: number;
    name: string;
    slug: string;
    timezone: string | null;
    currency: string | null;
    is_active: boolean;
    memberships?: MemberRow[];
};

type Paginated<T> = {
    data: T[];
};

const { t } = useI18n();

const props = defineProps<{
    clinics: Paginated<ClinicRow>;
    roles: string[];
}>();

const createForm = useForm({
    name: '',
    slug: '',
    is_active: true,
});

const editForms = reactive<Record<number, ReturnType<typeof useForm>>>({});
const memberForms = reactive<Record<number, ReturnType<typeof useForm>>>({});

function ensureEditForm(clinic: ClinicRow) {
    if (!editForms[clinic.id]) {
        editForms[clinic.id] = useForm({
            name: clinic.name,
            slug: clinic.slug,
            timezone: clinic.timezone ?? '',
            currency: clinic.currency ?? '',
            is_active: clinic.is_active,
        });
    }

    return editForms[clinic.id];
}

function ensureMemberForm(clinicId: number) {
    if (!memberForms[clinicId]) {
        memberForms[clinicId] = useForm({
            email: '',
            role: 'staff',
        });
    }

    return memberForms[clinicId];
}

function submitCreate() {
    createForm.post('/clinics', {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
}

function submitEdit(clinic: ClinicRow) {
    ensureEditForm(clinic).patch(`/clinics/${clinic.id}`, {
        preserveScroll: true,
    });
}

function toggleActive(clinic: ClinicRow) {
    const form = ensureEditForm(clinic);
    form.is_active = !clinic.is_active;
    form.patch(`/clinics/${clinic.id}`, { preserveScroll: true });
}

function submitMember(clinic: ClinicRow) {
    const form = ensureMemberForm(clinic.id);
    form.post(`/clinics/${clinic.id}/members`, {
        preserveScroll: true,
        onSuccess: () => form.reset('email'),
    });
}

function updateMemberRole(clinic: ClinicRow, member: MemberRow, role: string) {
    router.patch(
        `/clinics/${clinic.id}/members/${member.id}`,
        { role },
        { preserveScroll: true },
    );
}

function removeMember(clinic: ClinicRow, member: MemberRow) {
    router.delete(`/clinics/${clinic.id}/members/${member.id}`, {
        preserveScroll: true,
    });
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

        <ul class="mt-10 space-y-4">
            <li
                v-for="clinic in props.clinics.data"
                :key="clinic.id"
                class="rounded-lg border border-slate-700 p-4 text-sm"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="font-medium text-slate-100">
                            {{ clinic.name }}
                        </p>
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
                </div>

                <Form
                    class="mt-4 grid gap-3"
                    @submit.prevent="submitEdit(clinic)"
                >
                    <h3
                        class="text-xs font-medium tracking-wide text-slate-400 uppercase"
                    >
                        {{ t('vetpet.admin.clinics.edit_heading') }}
                    </h3>
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="grid gap-1">
                            <span>{{ t('vetpet.admin.clinics.name') }}</span>
                            <input
                                v-model="ensureEditForm(clinic).name"
                                class="rounded border border-slate-600 bg-slate-900 px-3 py-2"
                                required
                            />
                        </label>
                        <label class="grid gap-1">
                            <span>{{ t('vetpet.admin.clinics.slug') }}</span>
                            <input
                                v-model="ensureEditForm(clinic).slug"
                                class="rounded border border-slate-600 bg-slate-900 px-3 py-2"
                                required
                            />
                        </label>
                        <label class="grid gap-1">
                            <span>{{
                                t('vetpet.admin.clinics.timezone')
                            }}</span>
                            <input
                                v-model="ensureEditForm(clinic).timezone"
                                class="rounded border border-slate-600 bg-slate-900 px-3 py-2"
                            />
                        </label>
                        <label class="grid gap-1">
                            <span>{{
                                t('vetpet.admin.clinics.currency')
                            }}</span>
                            <input
                                v-model="ensureEditForm(clinic).currency"
                                class="rounded border border-slate-600 bg-slate-900 px-3 py-2"
                                maxlength="3"
                            />
                        </label>
                    </div>
                    <button
                        type="submit"
                        class="w-fit rounded border border-slate-500 px-3 py-1.5 text-xs"
                        :disabled="ensureEditForm(clinic).processing"
                    >
                        {{ t('vetpet.admin.clinics.save') }}
                    </button>
                </Form>

                <div class="mt-6 border-t border-slate-700 pt-4">
                    <h3
                        class="text-xs font-medium tracking-wide text-slate-400 uppercase"
                    >
                        {{ t('vetpet.admin.clinics.members_heading') }}
                    </h3>
                    <Form
                        class="mt-3 grid gap-3 md:grid-cols-3"
                        @submit.prevent="submitMember(clinic)"
                    >
                        <input
                            v-model="ensureMemberForm(clinic.id).email"
                            type="email"
                            class="rounded border border-slate-600 bg-slate-900 px-3 py-2 md:col-span-2"
                            :placeholder="t('vetpet.clinic.team.email')"
                            required
                        />
                        <select
                            v-model="ensureMemberForm(clinic.id).role"
                            class="rounded border border-slate-600 bg-slate-900 px-3 py-2"
                        >
                            <option
                                v-for="role in roles"
                                :key="role"
                                :value="role"
                            >
                                {{ t(`vetpet.clinic.team.roles.${role}`) }}
                            </option>
                        </select>
                        <button
                            type="submit"
                            class="w-fit rounded bg-slate-700 px-3 py-1.5 text-xs md:col-span-3"
                            :disabled="ensureMemberForm(clinic.id).processing"
                        >
                            {{ t('vetpet.admin.clinics.add_member') }}
                        </button>
                    </Form>

                    <ul
                        v-if="clinic.memberships?.length"
                        class="mt-4 divide-y divide-slate-700"
                    >
                        <li
                            v-for="member in clinic.memberships"
                            :key="member.id"
                            class="flex flex-wrap items-center justify-between gap-2 py-2"
                        >
                            <div>
                                <span class="font-medium">{{
                                    member.user.name
                                }}</span>
                                <span class="text-slate-400">
                                    — {{ member.user.email }}</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <select
                                    :value="member.role"
                                    class="rounded border border-slate-600 bg-slate-900 px-2 py-1 text-xs"
                                    @change="
                                        updateMemberRole(
                                            clinic,
                                            member,
                                            ($event.target as HTMLSelectElement)
                                                .value,
                                        )
                                    "
                                >
                                    <option
                                        v-for="role in roles"
                                        :key="role"
                                        :value="role"
                                    >
                                        {{
                                            t(
                                                `vetpet.clinic.team.roles.${role}`,
                                            )
                                        }}
                                    </option>
                                </select>
                                <button
                                    type="button"
                                    class="text-xs text-red-300"
                                    @click="removeMember(clinic, member)"
                                >
                                    {{
                                        t('vetpet.admin.clinics.remove_member')
                                    }}
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </AdminLayout>
</template>
