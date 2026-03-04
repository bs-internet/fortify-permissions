<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    Globe,
    ImageIcon,
    Save,
    Trash2,
    Upload,
} from 'lucide-vue-next';
import { ref } from 'vue';
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
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { index as settingsIndex, update as settingsUpdate } from '@/routes/settings';
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

const breadcrumbs: BreadcrumbItem[] = [
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

const logoLightPreview = ref<string | null>(null);
const logoDarkPreview = ref<string | null>(null);
const faviconPreview = ref<string | null>(null);

function handleFileChange(field: 'logo_light' | 'logo_dark' | 'favicon', event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    form[field] = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        const result = e.target?.result as string;
        if (field === 'logo_light') logoLightPreview.value = result;
        else if (field === 'logo_dark') logoDarkPreview.value = result;
        else faviconPreview.value = result;
    };
    reader.readAsDataURL(file);
}

const showConfirm = ref(false);
let currentDeleteKey = '';

function requestConfirm(key: string) {
    currentDeleteKey = key;
    showConfirm.value = true;
}

function onConfirmed() {
    // To clear the file settings without deleting the others,
    // we send a small update payload specifically clearing this file field.
    const deleteForm = useForm({
        _method: 'PUT',
        [currentDeleteKey]: '',
    } as any);

    deleteForm.post(settingsUpdate().url, {
        preserveScroll: true,
        onSuccess: () => {
            if (currentDeleteKey === 'logo_light') {
                form.logo_light = null;
                logoLightPreview.value = null;
            } else if (currentDeleteKey === 'logo_dark') {
                form.logo_dark = null;
                logoDarkPreview.value = null;
            } else if (currentDeleteKey === 'favicon') {
                form.favicon = null;
                faviconPreview.value = null;
            }
        },
    });

    showConfirm.value = false;
}

function handleDeleteFile(key: string) {
    requestConfirm(key);
}

function getStorageUrl(path: string | null): string | null {
    if (!path) return null;
    return path; // The value from backend is already a full storage URL
}

function submitForm() {
    form.post(settingsUpdate().url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            logoLightPreview.value = null;
            logoDarkPreview.value = null;
            faviconPreview.value = null;
            form.logo_light = null;
            form.logo_dark = null;
            form.favicon = null;
        },
    });
}
</script>

<template>

    <Head title="Genel Ayarlar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <SettingsLayout>
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Site Info -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Globe class="h-4 w-4 text-muted-foreground" />
                            <CardTitle class="text-sm font-medium">Site Bilgileri</CardTitle>
                        </div>
                        <CardDescription>Temel site ayarlarını yapılandırın</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="site_name">Site Adı</Label>
                                <Input id="site_name" v-model="form.site_name" />
                                <InputError :message="form.errors.site_name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="email">Sistem E-posta (Genel)</Label>
                                <Input id="email" type="email" v-model="form.email" />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="site_slogan">Slogan / Açıklama</Label>
                            <Textarea id="site_slogan" v-model="form.site_slogan" rows="2" />
                            <InputError :message="form.errors.site_slogan" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="phone">Telefon</Label>
                                <Input id="phone" v-model="form.phone" />
                                <InputError :message="form.errors.phone" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="sender_name">Sistem Gönderici Adı</Label>
                                <Input id="sender_name" v-model="form.sender_name" />
                                <InputError :message="form.errors.sender_name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="mail_from_address">Gönderici E-posta</Label>
                                <Input id="mail_from_address" type="email" v-model="form.mail_from_address" />
                                <InputError :message="form.errors.mail_from_address" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Logo & Favicon -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <ImageIcon class="h-4 w-4 text-muted-foreground" />
                            <CardTitle class="text-sm font-medium">Logo ve Favicon</CardTitle>
                        </div>
                        <CardDescription>Site logo ve favicon dosyalarını yönetin</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-6 sm:grid-cols-3">
                            <!-- Logo Light -->
                            <div class="space-y-3">
                                <Label>Açık Tema Logo</Label>
                                <div
                                    class="flex h-24 items-center justify-center rounded-md border border-dashed bg-muted/30">
                                    <img v-if="logoLightPreview" :src="logoLightPreview" alt="Logo Light Preview"
                                        class="max-h-20 max-w-full object-contain" />
                                    <img v-else-if="settings.logo_light" :src="getStorageUrl(settings.logo_light)!"
                                        alt="Logo Light" class="max-h-20 max-w-full object-contain" />
                                    <ImageIcon v-else class="h-8 w-8 text-muted-foreground/30" />
                                </div>
                                <div class="flex gap-2">
                                    <Label for="logo_light_input"
                                        class="flex h-8 cursor-pointer items-center gap-1.5 rounded-md border px-3 text-xs font-medium hover:bg-accent">
                                        <Upload class="h-3.5 w-3.5" />
                                        Yükle
                                    </Label>
                                    <input id="logo_light_input" type="file" accept="image/png,image/jpeg,image/svg+xml"
                                        class="hidden" @change="handleFileChange('logo_light', $event)" />
                                    <Button v-if="settings.logo_light" variant="ghost" size="sm" type="button"
                                        class="h-8 px-2" @click="handleDeleteFile('logo_light')">
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                                <InputError :message="form.errors.logo_light" />
                            </div>

                            <!-- Logo Dark -->
                            <div class="space-y-3">
                                <Label>Koyu Tema Logo</Label>
                                <div
                                    class="flex h-24 items-center justify-center rounded-md border border-dashed bg-zinc-900">
                                    <img v-if="logoDarkPreview" :src="logoDarkPreview" alt="Logo Dark Preview"
                                        class="max-h-20 max-w-full object-contain" />
                                    <img v-else-if="settings.logo_dark" :src="getStorageUrl(settings.logo_dark)!"
                                        alt="Logo Dark" class="max-h-20 max-w-full object-contain" />
                                    <ImageIcon v-else class="h-8 w-8 text-zinc-600" />
                                </div>
                                <div class="flex gap-2">
                                    <Label for="logo_dark_input"
                                        class="flex h-8 cursor-pointer items-center gap-1.5 rounded-md border px-3 text-xs font-medium hover:bg-accent">
                                        <Upload class="h-3.5 w-3.5" />
                                        Yükle
                                    </Label>
                                    <input id="logo_dark_input" type="file" accept="image/png,image/jpeg,image/svg+xml"
                                        class="hidden" @change="handleFileChange('logo_dark', $event)" />
                                    <Button v-if="settings.logo_dark" variant="ghost" size="sm" type="button"
                                        class="h-8 px-2" @click="handleDeleteFile('logo_dark')">
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                                <InputError :message="form.errors.logo_dark" />
                            </div>

                            <!-- Favicon -->
                            <div class="space-y-3">
                                <Label>Favicon</Label>
                                <div
                                    class="flex h-24 items-center justify-center rounded-md border border-dashed bg-muted/30">
                                    <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon Preview"
                                        class="max-h-12 max-w-full object-contain" />
                                    <img v-else-if="settings.favicon" :src="getStorageUrl(settings.favicon)!"
                                        alt="Favicon" class="max-h-12 max-w-full object-contain" />
                                    <ImageIcon v-else class="h-8 w-8 text-muted-foreground/30" />
                                </div>
                                <div class="flex gap-2">
                                    <Label for="favicon_input"
                                        class="flex h-8 cursor-pointer items-center gap-1.5 rounded-md border px-3 text-xs font-medium hover:bg-accent">
                                        <Upload class="h-3.5 w-3.5" />
                                        Yükle
                                    </Label>
                                    <input id="favicon_input" type="file" accept="image/png,image/x-icon,image/svg+xml"
                                        class="hidden" @change="handleFileChange('favicon', $event)" />
                                    <Button v-if="settings.favicon" variant="ghost" size="sm" type="button"
                                        class="h-8 px-2" @click="handleDeleteFile('favicon')">
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                                <InputError :message="form.errors.favicon" />
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-muted-foreground">
                            Logo: PNG, JPG, SVG (maks 2MB) &middot; Favicon: PNG, ICO, SVG (maks 512KB)
                        </p>
                    </CardContent>
                </Card>

                <!-- Region Defaults -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Globe class="h-4 w-4 text-muted-foreground" />
                            <CardTitle class="text-sm font-medium">Yerel Seçimler</CardTitle>
                        </div>
                        <CardDescription>Sistemin varsayılan dil, para birimi ve bölge ayarları</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>Varsayılan Dil</Label>
                            <Select v-model="form.default_language">
                                <SelectTrigger>
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

                        <div class="grid gap-2">
                            <Label>Varsayılan Para Birimi</Label>
                            <Select v-model="form.default_currency">
                                <SelectTrigger>
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

                        <div class="grid gap-2">
                            <Label>Varsayılan Ülke</Label>
                            <Select v-model="form.default_country">
                                <SelectTrigger>
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

                        <div class="grid gap-2">
                            <Label>Varsayılan Vergi Dilimi</Label>
                            <Select v-model="form.default_tax">
                                <SelectTrigger>
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
                    </CardContent>
                </Card>

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
