<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff, RefreshCcw } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);

// Şifre görünürlük durumları
const showPassword = ref(false);
const showConfirmPassword = ref(false);
</script>

<template>
    <AuthLayout
        title="Şifreyi Sıfırla"
        description="Lütfen hesabınız için yeni bir şifre belirleyin."
    >
        <Head title="Şifreyi Sıfırla" />

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email: inputEmail })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">E-posta Adresi</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="inputEmail"
                        class="mt-1 block w-full bg-muted/50 cursor-not-allowed"
                        readonly
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Yeni Şifre</Label>
                    <div class="relative">
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            autocomplete="new-password"
                            class="mt-1 block w-full pr-10"
                            autofocus
                            placeholder="Yeni şifreniz"
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

                <div class="grid gap-2">
                    <Label for="password_confirmation">Yeni Şifre (Tekrar)</Label>
                    <div class="relative">
                        <Input
                            id="password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="mt-1 block w-full pr-10"
                            placeholder="Yeni şifrenizi doğrulayın"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            @click="showConfirmPassword = !showConfirmPassword"
                        >
                            <Eye v-if="!showConfirmPassword" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                        </button>
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :disabled="processing"
                    data-test="reset-password-button"
                >
                    <Spinner v-if="processing" />
                    <RefreshCcw v-else class="mr-2 h-4 w-4" />
                    Şifreyi Sıfırla ve Kaydet
                </Button>
            </div>
        </Form>
    </AuthLayout>
</template>