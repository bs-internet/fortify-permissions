<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff, ShieldCheck, LifeBuoy } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/two-factor/login';
import type { TwoFactorConfigContent } from '@/types';

const showRecoveryInput = ref<boolean>(false);
const showRecoveryCode = ref<boolean>(false); // Kurtarma kodu görünürlüğü

const authConfigContent = computed<TwoFactorConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Kurtarma Kodu',
            description:
                'Lütfen acil durum kurtarma kodlarınızdan birini girerek hesabınıza erişimi doğrulayın.',
            buttonText: 'Kimlik doğrulama kodu kullan',
        };
    }

    return {
        title: 'Doğrulama Kodu',
        description:
            'Lütfen kimlik doğrulama uygulamanız tarafından sağlanan 6 haneli kodu giriniz.',
        buttonText: 'Kurtarma kodu kullan',
    };
});

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = '';
};

const code = ref<string>('');
</script>

<template>
    <AuthLayout
        :title="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <Head :title="authConfigContent.title" />

        <div class="mt-4">
            <template v-if="!showRecoveryInput">
                <Form
                    v-bind="store.form()"
                    :transform="(data) => ({ ...data, code })"
                    class="space-y-6"
                    #default="{ errors, processing }"
                >
                    <div class="flex flex-col items-center justify-center gap-4">
                        <InputOTP
                            v-model="code"
                            :maxlength="6"
                            @complete="() => {}"
                            autofocus
                        >
                            <InputOTPGroup>
                                <InputOTPSlot :index="0" />
                                <InputOTPSlot :index="1" />
                                <InputOTPSlot :index="2" />
                                <InputOTPSlot :index="3" />
                                <InputOTPSlot :index="4" />
                                <InputOTPSlot :index="5" />
                            </InputOTPGroup>
                        </InputOTP>
                        <InputError :message="errors.code" />
                    </div>

                    <Button type="submit" class="w-full" :disabled="processing || code.length !== 6">
                        <ShieldCheck class="mr-2 h-4 w-4" />
                        Doğrula ve Giriş Yap
                    </Button>

                    <div class="text-center">
                        <button
                            type="button"
                            class="text-sm text-muted-foreground underline decoration-neutral-300 underline-offset-4 hover:text-foreground transition-colors"
                            @click="() => toggleRecoveryMode(() => {})"
                        >
                            {{ authConfigContent.buttonText }}
                        </button>
                    </div>
                </Form>
            </template>

            <template v-else>
                <Form
                    v-bind="store.form()"
                    class="space-y-4"
                    reset-on-error
                    #default="{ errors, processing, clearErrors }"
                >
                    <div class="relative">
                        <Input
                            name="recovery_code"
                            :type="showRecoveryCode ? 'text' : 'password'"
                            placeholder="Kurtarma kodunu giriniz"
                            :autofocus="showRecoveryInput"
                            class="pr-10"
                            required
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            @click="showRecoveryCode = !showRecoveryCode"
                        >
                            <Eye v-if="!showRecoveryCode" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                        </button>
                    </div>
                    
                    <InputError :message="errors.recovery_code" />
                    
                    <Button type="submit" class="w-full" :disabled="processing">
                        <LifeBuoy class="mr-2 h-4 w-4" />
                        Kurtarma Koduyla Giriş Yap
                    </Button>

                    <div class="text-center">
                        <button
                            type="button"
                            class="text-sm text-muted-foreground underline decoration-neutral-300 underline-offset-4 hover:text-foreground transition-colors"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.buttonText }}
                        </button>
                    </div>
                </Form>
            </template>
        </div>
    </AuthLayout>
</template>