<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/app/common/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="E-posta Adresinizi Doğrulayın"
        description="Lütfen size gönderdiğimiz bağlantıya tıklayarak e-posta adresinizi doğrulayın. Eğer e-posta ulaşmadıysa, size yenisini gönderebiliriz."
    >
        <Head title="E-posta Doğrulama" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-100"
        >
            Kayıt sırasında belirttiğiniz adrese yeni bir doğrulama bağlantısı gönderildi.
        </div>

        <Form
            v-bind="send.form()"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary" class="w-full">
                <Spinner v-if="processing" />
                Doğrulama E-postasını Tekrar Gönder
            </Button>

            <div class="flex flex-col gap-2">
                <p class="text-xs text-muted-foreground">Yanlış bir hesapla mı giriş yaptınız?</p>
                <TextLink
                    :href="logout()"
                    as="button"
                    class="mx-auto block text-sm font-medium"
                >
                    Güvenli Çıkış Yap
                </TextLink>
            </div>
        </Form>
    </AuthLayout>
</template>