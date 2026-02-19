<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff, Lock } from 'lucide-vue-next';
import { ref } from 'vue';
import PasswordController from '@/actions/App/Http/Controllers/Profile/PasswordController';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue';
import { edit } from '@/routes/profile/password';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Şifre Ayarları',
        href: edit().url,
    },
];

// Şifre görünürlük durumları
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Şifre Ayarları" />

        <h1 class="sr-only">Şifre Ayarları</h1>

        <ProfileLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Şifreyi Güncelle"
                    description="Hesabınızın güvenliğini sağlamak için uzun ve rastgele bir şifre kullandığınızdan emin olun."
                />

                <Form
                    v-bind="PasswordController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="[
                        'password',
                        'password_confirmation',
                        'current_password',
                    ]"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="current_password">Mevcut Şifre</Label>
                        <div class="relative">
                            <Input
                                id="current_password"
                                name="current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                class="mt-1 block w-full pr-10"
                                autocomplete="current-password"
                                placeholder="Mevcut şifreniz"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                @click="showCurrentPassword = !showCurrentPassword"
                            >
                                <Eye v-if="!showCurrentPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Yeni Şifre</Label>
                        <div class="relative">
                            <Input
                                id="password"
                                name="password"
                                :type="showNewPassword ? 'text' : 'password'"
                                class="mt-1 block w-full pr-10"
                                autocomplete="new-password"
                                placeholder="Yeni şifreniz"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                @click="showNewPassword = !showNewPassword"
                            >
                                <Eye v-if="!showNewPassword" class="h-4 w-4" />
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
                                name="password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                class="mt-1 block w-full pr-10"
                                autocomplete="new-password"
                                placeholder="Yeni şifrenizi onaylayın"
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

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-password-button"
                        >
                            <Lock class="mr-2 h-4 w-4" />
                            Şifreyi Kaydet
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-green-600"
                            >
                                Değişiklikler kaydedildi.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </ProfileLayout>
    </AppLayout>
</template>