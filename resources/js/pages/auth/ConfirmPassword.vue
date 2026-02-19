<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';

// Şifre görünürlük durumu
const showPassword = ref(false);
</script>

<template>
    <AuthLayout
        title="Şifrenizi doğrulayın"
        description="Burası uygulamanın güvenli bir alanıdır. Lütfen devam etmeden önce şifrenizi doğrulayın."
    >
        <Head title="Şifreyi Doğrula" />

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">Şifre</Label>
                    <div class="relative">
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            class="mt-1 block w-full pr-10"
                            required
                            autocomplete="current-password"
                            autofocus
                            placeholder="Şifrenizi giriniz"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            @click="showPassword = !showPassword"
                        >
                            <Eye v-if="!showPassword" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                        </button>
                    </div>

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" />
                        Şifreyi Doğrula
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>