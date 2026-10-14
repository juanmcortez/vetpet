export function formatMoney(
    amount: number,
    currencyLocale: string,
    currency: string,
): string {
    return new Intl.NumberFormat(currencyLocale, {
        style: 'currency',
        currency,
    }).format(amount);
}
