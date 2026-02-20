<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import ProfileController from '@/actions/App/Http/Controllers/Profile/ProfileController';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue';
import { edit } from '@/routes/profile';
import { type BreadcrumbItem } from '@/types';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profil Ayarları',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profil Ayarları" />

        <h1 class="sr-only">Profil Ayarları</h1>

        <ProfileLayout>
            <div class="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Profil Bilgileri"
                    description="Hesabınızdaki temel bilgileri buradan güncelleyebilirsiniz."
                />

                <Form
                    v-bind="ProfileController.update.form()"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Ad Soyad</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Tam adınız"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="title">Görev</Label>
                        <Input
                            id="title"
                            class="mt-1 block w-full"
                            name="title"
                            :default-value="user.title ?? ''"
                            autocomplete="name"
                            placeholder="Göreviniz"
                        />
                        <InputError class="mt-2" :message="errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">E-posta Adresi</Label>
                        <div class="relative">
                            <Input
                                id="email"
                                type="email"
                                class="mt-1 block w-full bg-muted/50 cursor-not-allowed opacity-80"
                                name="email"
                                :default-value="user.email"
                                disabled
                                placeholder="E-posta adresi"
                            />
                            <p class="mt-1 text-[11px] text-muted-foreground italic">
                                * E-posta adresi değiştirilemez.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-profile-button"
                        >
                            Bilgileri Güncelle
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
