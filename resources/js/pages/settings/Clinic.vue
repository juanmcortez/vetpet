<script setup lang="ts">
import { Form, Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type MemberRow = {
    id: number;
    role: string;
    user: { id: number; name: string; email: string };
};

const { t } = useI18n();
const page = usePage();

const props = defineProps<{
    clinic: {
        id: number;
        name: string;
        slug: string;
        timezone: string | null;
        currency: string | null;
    };
    members: MemberRow[];
    roles: { value: string; label: string }[];
}>();

const clinicForm = useForm({
    name: props.clinic.name,
    timezone: props.clinic.timezone ?? '',
    currency: props.clinic.currency ?? '',
});

const addMemberForm = useForm({
    email: '',
    role: 'staff',
});

const errors = computed(() => page.props.errors as Record<string, string>);

function submitClinic() {
    clinicForm.patch('/settings/clinic', { preserveScroll: true });
}

function submitMember() {
    addMemberForm.post('/settings/clinic/members', {
        preserveScroll: true,
        onSuccess: () => addMemberForm.reset('email'),
    });
}

function updateMemberRole(member: MemberRow, role: string) {
    router.patch(
        `/settings/clinic/members/${member.id}`,
        { role },
        { preserveScroll: true },
    );
}

function removeMember(member: MemberRow) {
    router.delete(`/settings/clinic/members/${member.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="t('vetpet.clinic.settings.title')" />

    <div class="flex flex-col space-y-10">
        <div class="space-y-6">
            <Heading
                variant="small"
                :title="t('vetpet.clinic.settings.title')"
                :description="t('vetpet.clinic.settings.description')"
            />

            <Form class="space-y-4" @submit.prevent="submitClinic">
                <div class="grid gap-2">
                    <Label for="clinic-name">{{
                        t('vetpet.admin.clinics.name')
                    }}</Label>
                    <Input
                        id="clinic-name"
                        v-model="clinicForm.name"
                        required
                    />
                    <InputError :message="clinicForm.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="clinic-timezone">{{
                        t('vetpet.clinic.settings.timezone')
                    }}</Label>
                    <Input id="clinic-timezone" v-model="clinicForm.timezone" />
                    <InputError :message="clinicForm.errors.timezone" />
                </div>
                <div class="grid gap-2">
                    <Label for="clinic-currency">{{
                        t('vetpet.clinic.settings.currency')
                    }}</Label>
                    <Input
                        id="clinic-currency"
                        v-model="clinicForm.currency"
                        maxlength="3"
                    />
                    <InputError :message="clinicForm.errors.currency" />
                </div>
                <p class="text-sm text-muted-foreground">
                    {{
                        t('vetpet.clinic.settings.slug_readonly', {
                            slug: clinic.slug,
                        })
                    }}
                </p>
                <Button type="submit" :disabled="clinicForm.processing">
                    {{ t('vetpet.clinic.settings.save') }}
                </Button>
            </Form>
        </div>

        <div class="space-y-6">
            <Heading
                variant="small"
                :title="t('vetpet.clinic.team.title')"
                :description="t('vetpet.clinic.team.description')"
            />

            <Form class="space-y-4" @submit.prevent="submitMember">
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="member-email">{{
                            t('vetpet.clinic.team.email')
                        }}</Label>
                        <Input
                            id="member-email"
                            v-model="addMemberForm.email"
                            type="email"
                            required
                        />
                        <InputError
                            :message="
                                addMemberForm.errors.email ?? errors.email
                            "
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="member-role">{{
                            t('vetpet.clinic.team.role')
                        }}</Label>
                        <select
                            id="member-role"
                            v-model="addMemberForm.role"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                        >
                            <option
                                v-for="role in roles"
                                :key="role.value"
                                :value="role.value"
                            >
                                {{
                                    t(`vetpet.clinic.team.roles.${role.value}`)
                                }}
                            </option>
                        </select>
                        <InputError :message="addMemberForm.errors.role" />
                    </div>
                </div>
                <Button type="submit" :disabled="addMemberForm.processing">
                    {{ t('vetpet.clinic.team.add_member') }}
                </Button>
            </Form>

            <InputError :message="errors.member" />

            <ul class="divide-y rounded-md border">
                <li
                    v-for="member in members"
                    :key="member.id"
                    class="flex flex-col gap-3 px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <p class="font-medium">{{ member.user.name }}</p>
                        <p class="text-muted-foreground">
                            {{ member.user.email }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <select
                            :value="member.role"
                            class="rounded-md border border-input bg-transparent px-2 py-1 text-sm"
                            @change="
                                updateMemberRole(
                                    member,
                                    ($event.target as HTMLSelectElement).value,
                                )
                            "
                        >
                            <option
                                v-for="role in roles"
                                :key="role.value"
                                :value="role.value"
                            >
                                {{
                                    t(`vetpet.clinic.team.roles.${role.value}`)
                                }}
                            </option>
                        </select>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="removeMember(member)"
                        >
                            {{ t('vetpet.clinic.team.remove') }}
                        </Button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
