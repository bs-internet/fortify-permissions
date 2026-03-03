<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import SettingsController from '@/actions/App/Http/Controllers/Settings/SettingsController';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/pages/app/settings/partials/Layout.vue';
import { index as settingsIndex } from '@/routes/settings';
import { type BreadcrumbItem } from '@/types';
import { type GeneralSettings } from '@/types/settings';

type DropdownItem = {
    id: string;
    name: string;
    code?: string;
    symbol?: string;
    rate?: number;
};

type Props = {
    settings: GeneralSettings;
    languages: DropdownItem[];
    currencies: DropdownItem[];
    countries: DropdownItem[];
    taxes: DropdownItem[];
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Ayarlar', href: '#' },
    { title: 'Genel Ayarlar', href: settingsIndex().url },
];

const logoLightPreview = ref<string | null>(null);
const logoDarkPreview = ref<string | null>(null);
const faviconPreview = ref<string | null>(null);

const defaultLanguage = ref(props.settings.default_language ?? '');
const defaultCurrency = ref(props.settings.default_currency ?? '');
const defaultCountry = ref(props.settings.default_country ?? '');
const defaultTax = ref(props.settings.default_tax ?? '');

const handleFilePreview = (event: Event, setter: (value: string | null) => void) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            setter(e.target?.result as string);
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
                                    @change="handleFilePreview($event, (v) => (logoLightPreview = v))"
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
                                    @change="handleFilePreview($event, (v) => (logoDarkPreview = v))"
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
                                    @change="handleFilePreview($event, (v) => (faviconPreview = v))"
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

                    <Separator />

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>Varsayılan Dil</Label>
                            <input type="hidden" name="default_language" :value="defaultLanguage" />
                            <Select v-model="defaultLanguage">
                                <SelectTrigger>
                                    <SelectValue placeholder="Dil seçin" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="lang in languages" :key="lang.id" :value="lang.id">
                                        {{ lang.name }} ({{ lang.code }})
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.default_language" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Varsayılan Para Birimi</Label>
                            <input type="hidden" name="default_currency" :value="defaultCurrency" />
                            <Select v-model="defaultCurrency">
                                <SelectTrigger>
                                    <SelectValue placeholder="Para birimi seçin" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="currency in currencies" :key="currency.id" :value="currency.id">
                                        {{ currency.name }} ({{ currency.symbol }})
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.default_currency" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Varsayılan Ülke</Label>
                            <input type="hidden" name="default_country" :value="defaultCountry" />
                            <Select v-model="defaultCountry">
                                <SelectTrigger>
                                    <SelectValue placeholder="Ülke seçin" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="country in countries" :key="country.id" :value="country.id">
                                        {{ country.name }} ({{ country.code }})
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.default_country" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Varsayılan Vergi Dilimi</Label>
                            <input type="hidden" name="default_tax" :value="defaultTax" />
                            <Select v-model="defaultTax">
                                <SelectTrigger>
                                    <SelectValue placeholder="Vergi seçin" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="tax in taxes" :key="tax.id" :value="tax.id">
                                        {{ tax.name }} (%{{ tax.rate }})
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.default_tax" />
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
