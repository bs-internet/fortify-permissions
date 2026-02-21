<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref, type Ref } from 'vue';
import SettingsController from '@/actions/App/Http/Controllers/Settings/SettingsController';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/pages/app/settings/partials/Layout.vue';
import { index as settingsIndex } from '@/routes/settings';
import { type BreadcrumbItem } from '@/types';
import { type GeneralSettings } from '@/types/settings';

type Props = {
    settings: GeneralSettings;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Ayarlar', href: '#' },
    { title: 'Genel Ayarlar', href: settingsIndex().url },
];

const logoLightPreview = ref<string | null>(null);
const logoDarkPreview = ref<string | null>(null);
const faviconPreview = ref<string | null>(null);

const handleFilePreview = (event: Event, preview: Ref<string | null>) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Genel Ayarlar" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Panel Kimliği ve İletişim"
                    description="Sistem genelinde kullanılan marka varlıklarını ve iletişim bilgilerini yönetin."
                />

                <Form
                    v-bind="SettingsController.update.form()"
                    v-slot="{ errors, processing, recentlySuccessful }"
                    class="space-y-8"
                >
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-2">
                            <Label>Logo (Açık Tema)</Label>
                            <div class="flex flex-col items-center gap-4 rounded-lg border border-dashed p-4">
                                <img
                                    v-if="logoLightPreview || settings.logo_light"
                                    :src="logoLightPreview || settings.logo_light"
                                    class="h-12 object-contain"
                                    alt="Logo (Açık Tema)"
                                />
                                <Input
                                    type="file"
                                    name="logo_light"
                                    accept="image/*"
                                    class="text-xs"
                                    @change="handleFilePreview($event, logoLightPreview)"
                                />
                            </div>
                            <InputError :message="errors.logo_light" />
                        </div>

                        <div class="space-y-2">
                            <Label>Logo (Koyu Tema)</Label>
                            <div class="flex flex-col items-center gap-4 rounded-lg border border-dashed bg-slate-950 p-4">
                                <img
                                    v-if="logoDarkPreview || settings.logo_dark"
                                    :src="logoDarkPreview || settings.logo_dark"
                                    class="h-12 object-contain"
                                    alt="Logo (Koyu Tema)"
                                />
                                <Input
                                    type="file"
                                    name="logo_dark"
                                    accept="image/*"
                                    class="text-xs text-white"
                                    @change="handleFilePreview($event, logoDarkPreview)"
                                />
                            </div>
                            <InputError :message="errors.logo_dark" />
                        </div>

                        <div class="space-y-2">
                            <Label>Favicon</Label>
                            <div class="flex flex-col items-center gap-4 rounded-lg border border-dashed p-4">
                                <img
                                    v-if="faviconPreview || settings.favicon"
                                    :src="faviconPreview || settings.favicon"
                                    class="h-8 w-8 object-contain"
                                    alt="Favicon"
                                />
                                <Input
                                    type="file"
                                    name="favicon"
                                    accept="image/*"
                                    class="text-xs"
                                    @change="handleFilePreview($event, faviconPreview)"
                                />
                            </div>
                            <InputError :message="errors.favicon" />
                        </div>
                    </div>

                    <Separator />

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="site_name">Site Adı</Label>
                            <Input
                                id="site_name"
                                name="site_name"
                                :default-value="settings.site_name"
                            />
                            <InputError :message="errors.site_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="site_slogan">Slogan / Alt Başlık</Label>
                            <Input
                                id="site_slogan"
                                name="site_slogan"
                                :default-value="settings.site_slogan ?? ''"
                            />
                            <InputError :message="errors.site_slogan" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Sistem E-posta Adresi</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                :default-value="settings.email"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="sender_name">E-posta Gönderen Adı</Label>
                            <Input
                                id="sender_name"
                                name="sender_name"
                                :default-value="settings.sender_name"
                            />
                            <InputError :message="errors.sender_name" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing">Ayarları Kaydet</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="recentlySuccessful" class="text-sm text-green-600">
                                Başarıyla güncellendi.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
