<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';
import Heading from '@/components/app/common/Heading.vue';
import TwoFactorRecoveryCodes from '@/components/app/common/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/app/common/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue';
import { show } from '@/routes/profile/twofactor';
import type { BreadcrumbItem } from '@/types';

type Props = {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'İki Faktörlü Doğrulama',
        href: show.url(),
    },
];

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => {
    clearTwoFactorAuthData();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="İki Faktörlü Doğrulama" />

        <h1 class="sr-only">İki Faktörlü Doğrulama Ayarları</h1>

        <ProfileLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="İki Faktörlü Doğrulama"
                    description="Hesabınızın güvenliğini artırmak için iki faktörlü doğrulamayı yönetin."
                />

                <div
                    v-if="!twoFactorEnabled"
                    class="flex flex-col items-start justify-start space-y-4"
                >
                    <Badge variant="destructive">Devre Dışı</Badge>

                    <p class="text-sm text-muted-foreground leading-relaxed">
                        İki faktörlü doğrulamayı etkinleştirdiğinizde, giriş sırasında sizden güvenli bir kod istenecektir. 
                        Bu kodu telefonunuzdaki Google Authenticator gibi TOTP destekli bir uygulama üzerinden alabilirsiniz.
                    </p>

                    <div>
                        <Button
                            v-if="hasSetupData"
                            @click="showSetupModal = true"
                        >
                            <ShieldCheck class="mr-2 h-4 w-4" />Kuruluma Devam Et
                        </Button>
                        <Form
                            v-else
                            v-bind="enable.form()"
                            @success="showSetupModal = true"
                            #default="{ processing }"
                        >
                            <Button type="submit" :disabled="processing">
                                <ShieldCheck class="mr-2 h-4 w-4" />2FA'yı Etkinleştir
                            </Button>
                        </Form>
                    </div>
                </div>

                <div
                    v-else
                    class="flex flex-col items-start justify-start space-y-4"
                >
                    <Badge variant="default" class="bg-green-600 hover:bg-green-700">Aktif</Badge>

                    <p class="text-sm text-muted-foreground leading-relaxed">
                        İki faktörlü doğrulama şu anda etkin. Giriş yaparken telefonunuzdaki kimlik doğrulama uygulaması tarafından üretilen rastgele kodu kullanmanız gerekmektedir.
                    </p>

                    <TwoFactorRecoveryCodes />

                    <div class="relative inline pt-2">
                        <Form v-bind="disable.form()" #default="{ processing }">
                            <Button
                                variant="destructive"
                                type="submit"
                                :disabled="processing"
                                class="bg-red-50 text-red-600 hover:bg-red-100 border-red-200"
                            >
                                <ShieldBan class="mr-2 h-4 w-4" />
                                2FA'yı Kapat
                            </Button>
                        </Form>
                    </div>
                </div>

                <TwoFactorSetupModal
                    v-model:isOpen="showSetupModal"
                    :requiresConfirmation="requiresConfirmation"
                    :twoFactorEnabled="twoFactorEnabled"
                />
            </div>
        </ProfileLayout>
    </AppLayout>
</template>