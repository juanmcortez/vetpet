import { createI18n } from 'vue-i18n';
import en from '@/locales/en.json';
import es from '@/locales/es.json';

export const i18n = createI18n({
    legacy: false,
    locale: 'es',
    fallbackLocale: 'en',
    messages: {
        en,
        es,
    },
});

export function syncI18nLocale(locale: string): void {
    if (locale === i18n.global.locale.value) {
        return;
    }

    if (locale === 'en' || locale === 'es') {
        i18n.global.locale.value = locale;
    }
}
