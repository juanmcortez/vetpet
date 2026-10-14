<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const { t } = useI18n();
const page = usePage();

const clinicContext = computed(() => page.props.clinic ?? null);

const currentId = computed(() => {
    const current = clinicContext.value?.current;
    return current ? String(current.id) : undefined;
});

function onChange(value: unknown) {
    const clinicId = Number(value);

    if (!clinicId || clinicId === clinicContext.value?.current?.id) {
        return;
    }

    router.post(
        '/clinic/switch',
        { clinic_id: clinicId },
        { preserveScroll: true },
    );
}
</script>

<template>
    <div
        v-if="clinicContext && clinicContext.available.length > 1"
        class="min-w-[12rem]"
    >
        <Select :model-value="currentId" @update:model-value="onChange">
            <SelectTrigger class="h-9 w-full">
                <SelectValue
                    :placeholder="t('vetpet.clinic.switcher_placeholder')"
                />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="clinic in clinicContext.available"
                    :key="clinic.id"
                    :value="String(clinic.id)"
                >
                    {{ clinic.name }}
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
