import type { InertiaForm } from '@inertiajs/vue3';
import { watch } from 'vue';

/**
 * Form alanları değiştiğinde ilgili hata mesajını otomatik temizler.
 */
export function useClearFormErrors<T extends Record<string, unknown>>(form: InertiaForm<T>): void {
    const fields = Object.keys(form.data()) as Array<keyof T & string>;

    fields.forEach((field) => {
        watch(
            () => form[field as keyof typeof form],
            () => {
                if (form.errors[field as keyof typeof form.errors]) {
                    form.clearErrors(field as any);
                }
            },
        );
    });
}
