<script setup lang="ts">
import { Deferred, Head, useForm } from '@inertiajs/vue3';
import {
    Globe,
    ImageIcon,
    Save,
} from 'lucide-vue-next';
import { ref, useTemplateRef } from 'vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import FileUploadField from '@/components/app/common/FileUploadField.vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Skeleton } from '@/components/ui/skeleton';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/pages/app/settings/partials/Layout.vue';
import { index as settingsIndex } from '@/routes/settings';
import { update as settingsUpdate } from '@/actions/App/Http/Controllers/Settings/SettingsController';
import type { BreadcrumbItem } from '@/types';

type DropdownItem = {
    id: string;
    name: string;
    code?: string;
    symbol?: string;
    rate?: number;
};

type Props = {
    settings: Record<string, string | null>;
    languages: DropdownItem[];
    currencies: DropdownItem[];
    countries: DropdownItem[];
    taxes: DropdownItem[];
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Ayarlar', href: '#' },
    { title: 'Genel', href: settingsIndex().url },
];

const form = useForm({
    _method: 'PUT',
    site_name: props.settings.site_name ?? '',
    site_slogan: props.settings.site_slogan ?? '',
    email: props.settings.email ?? '',
    phone: props.settings.phone ?? '',
    sender_name: props.settings.sender_name ?? '',
    mail_from_address: props.settings.mail_from_address ?? '',
    default_language: props.settings.default_language ?? '',
    default_currency: props.settings.default_currency ?? '',
    default_country: props.settings.default_country ?? '',
    default_tax: props.settings.default_tax ?? '',
    logo_light: null as File | null,
    logo_dark: null as File | null,
    favicon: null as File | null,
});

const logoLightRef = useTemplateRef<InstanceType<typeof FileUploadField>>('logoLightRef');
const logoDarkRef = useTemplateRef<InstanceType<typeof FileUploadField>>('logoDarkRef');
const faviconRef = useTemplateRef<InstanceType<typeof FileUploadField>>('faviconRef');

function handleFileSelect(field: 'logo_light' | 'logo_dark' | 'favicon', file: File) {
    form[field] = file;
}

const showConfirm = ref(false);
let currentDeleteKey: 'logo_light' | 'logo_dark' | 'favicon' = 'logo_light';

function handleDeleteFile(key: 'logo_light' | 'logo_dark' | 'favicon') {
    currentDeleteKey = key;
    showConfirm.value = true;
}

function onConfirmed() {
    const deleteForm = useForm({
        _method: 'PUT',
        [currentDeleteKey]: '',
    } as any);

    deleteForm.post(settingsUpdate.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form[currentDeleteKey] = null;
            const refs = { logo_light: logoLightRef, logo_dark: logoDarkRef, favicon: faviconRef };
            refs[currentDeleteKey].value?.clearPreview();
        },
    });

    showConfirm.value = false;
}

function submitForm() {
    form.post(settingsUpdate.url(), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.logo_light = null;
            form.logo_dark = null;
            form.favicon = null;
            logoLightRef.value?.clearPreview();
            logoDarkRef.value?.clearPreview();
            faviconRef.value?.clearPreview();
        },
    });
}
</script>

<template>

    <Head title="Genel Ayarlar" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <SettingsLayout>
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Site Info -->
                <div class="rounded-md border border-border bg-card p-6 shadow-none space-y-4">
                    <div class="flex items-center gap-2 mb-2 text-primary font-semibold text-sm uppercase tracking-wider">
                        <Globe class="h-5 w-5" />
                        <span>Site Bilgileri</span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="site_name">Site Adı</Label>
                            <Input id="site_name" v-model="form.site_name" class="shadow-none focus-visible:ring-1" />
                            <InputError :message="form.errors.site_name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Sistem E-posta (Genel)</Label>
                            <Input id="email" type="email" v-model="form.email" class="shadow-none focus-visible:ring-1" />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="site_slogan">Slogan / Açıklama</Label>
                        <Textarea id="site_slogan" v-model="form.site_slogan" rows="2" class="resize-none shadow-none focus-visible:ring-1" />
                        <InputError :message="form.errors.site_slogan" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="space-y-2">
                            <Label for="phone">Telefon</Label>
                            <Input id="phone" v-model="form.phone" class="shadow-none focus-visible:ring-1" />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div class="space-y-2">
                            <Label for="sender_name">Sistem Gönderici Adı</Label>
                            <Input id="sender_name" v-model="form.sender_name" class="shadow-none focus-visible:ring-1" />
                            <InputError :message="form.errors.sender_name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="mail_from_address">Gönderici E-posta</Label>
                            <Input id="mail_from_address" type="email" v-model="form.mail_from_address" class="shadow-none focus-visible:ring-1" />
                            <InputError :message="form.errors.mail_from_address" />
                        </div>
                    </div>
                </div>

                <!-- Logo & Favicon -->
                <div class="rounded-md border border-border bg-card p-6 shadow-none space-y-4">
                    <div class="flex items-center gap-2 mb-2 text-primary font-semibold text-sm uppercase tracking-wider">
                        <ImageIcon class="h-5 w-5" />
                        <span>Logo ve Favicon</span>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <FileUploadField
                            ref="logoLightRef"
                            label="Açık Tema Logo"
                            input-id="logo_light_input"
                            :current-url="settings.logo_light"
                            :error="form.errors.logo_light"
                            @change="handleFileSelect('logo_light', $event)"
                            @delete="handleDeleteFile('logo_light')"
                        />
                        <FileUploadField
                            ref="logoDarkRef"
                            label="Koyu Tema Logo"
                            input-id="logo_dark_input"
                            :current-url="settings.logo_dark"
                            :error="form.errors.logo_dark"
                            container-class="bg-zinc-900"
                            @change="handleFileSelect('logo_dark', $event)"
                            @delete="handleDeleteFile('logo_dark')"
                        />
                        <FileUploadField
                            ref="faviconRef"
                            label="Favicon"
                            input-id="favicon_input"
                            accept="image/png,image/x-icon,image/svg+xml"
                            :current-url="settings.favicon"
                            :error="form.errors.favicon"
                            preview-class="max-h-12 max-w-full object-contain"
                            @change="handleFileSelect('favicon', $event)"
                            @delete="handleDeleteFile('favicon')"
                        />
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Logo: PNG, JPG, SVG (maks 2MB) &middot; Favicon: PNG, ICO, SVG (maks 512KB)
                    </p>
                </div>

                <!-- Region Defaults -->
                <div class="rounded-md border border-border bg-card p-6 shadow-none space-y-4">
                    <div class="flex items-center gap-2 mb-2 text-primary font-semibold text-sm uppercase tracking-wider">
                        <Globe class="h-5 w-5" />
                        <span>Yerel Seçimler</span>
                    </div>

                    <Deferred :data="['languages', 'currencies', 'countries', 'taxes']">
                        <template #fallback>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-for="i in 4" :key="i" class="space-y-2">
                                    <Skeleton class="h-4 w-28" />
                                    <Skeleton class="h-10 w-full rounded-md" />
                                </div>
                            </div>
                        </template>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Varsayılan Dil</Label>
                                <Select v-model="form.default_language">
                                    <SelectTrigger class="shadow-none focus:ring-1">
                                        <SelectValue placeholder="Dil seçin" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="lang in languages" :key="lang.id" :value="lang.id">
                                            {{ lang.name }} ({{ lang.code }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.default_language" />
                            </div>

                            <div class="space-y-2">
                                <Label>Varsayılan Para Birimi</Label>
                                <Select v-model="form.default_currency">
                                    <SelectTrigger class="shadow-none focus:ring-1">
                                        <SelectValue placeholder="Para birimi seçin" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="currency in currencies" :key="currency.id" :value="currency.id">
                                            {{ currency.name }} ({{ currency.symbol }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.default_currency" />
                            </div>

                            <div class="space-y-2">
                                <Label>Varsayılan Ülke</Label>
                                <Select v-model="form.default_country">
                                    <SelectTrigger class="shadow-none focus:ring-1">
                                        <SelectValue placeholder="Ülke seçin" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="country in countries" :key="country.id" :value="country.id">
                                            {{ country.name }} ({{ country.code }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.default_country" />
                            </div>

                            <div class="space-y-2">
                                <Label>Varsayılan Vergi Dilimi</Label>
                                <Select v-model="form.default_tax">
                                    <SelectTrigger class="shadow-none focus:ring-1">
                                        <SelectValue placeholder="Vergi seçin" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="tax in taxes" :key="tax.id" :value="tax.id">
                                            {{ tax.name }} (%{{ tax.rate }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.default_tax" />
                            </div>
                        </div>
                    </Deferred>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-3 items-center">
                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                            Başarıyla güncellendi.
                        </p>
                    </Transition>
                    <Button type="submit" :disabled="form.processing">
                        <Save class="mr-1.5 h-4 w-4" />
                        Ayarları Kaydet
                    </Button>
                </div>
            </form>
        </SettingsLayout>
        <AlertDialog :open="showConfirm" @update:open="showConfirm = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Emin misiniz?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Bu dosyayı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel @click="showConfirm = false">İptal</AlertDialogCancel>
                    <AlertDialogAction class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                        @click="onConfirmed">
                        Evet, Sil
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
