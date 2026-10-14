import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { syncI18nLocale } from '@/i18n';

const localeLabels: Record<string, string> = {
    en: 'English',
    es: 'Español',
};

export function useLocale() {
    const page = usePage();

    const locale = computed(() => String(page.props.locale ?? 'es'));
    const locales = computed(() => {
        const supported = page.props.locales;

        return Array.isArray(supported) ? supported.map(String) : ['en', 'es'];
    });

    const regional = computed(
        () =>
            page.props.regional as {
                timezone: string;
                currency: string;
                currency_locale: string;
            },
    );

    function setLocale(nextLocale: string): void {
        if (nextLocale === locale.value) {
            return;
        }

        router.post(
            '/locale',
            { locale: nextLocale },
            {
                preserveScroll: true,
                onSuccess: () => syncI18nLocale(nextLocale),
            },
        );
    }

    function labelFor(code: string): string {
        return localeLabels[code] ?? code;
    }

    return {
        locale,
        locales,
        regional,
        setLocale,
        labelFor,
    };
}
