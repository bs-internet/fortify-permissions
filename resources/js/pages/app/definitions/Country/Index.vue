<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2, X, Check } from 'lucide-vue-next';
import { ref } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Definitions/CountryController';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import EmptyState from '@/components/ui/empty-state/EmptyState.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as countryRoute } from '@/routes/settings/definitions/countries';
import { type BreadcrumbItem, type Country } from '@/types';

type Props = {
    countries: Country[];
};

defineProps<Props>();

const { can, canAny } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Ülkeler', href: countryRoute().url },
];

const isSheetOpen = ref(false);
const showDeleteDialog = ref(false);
const editingCountry = ref<Country | null>(null);
const deletingCountry = ref<Country | null>(null);

const form = useForm({
    code: '',
    name: '',
    is_default: false,
    is_active: true,
    sort_order: 0,
});

useClearFormErrors(form);

function openCreateSheet() {
    editingCountry.value = null;
    form.reset();
    form.clearErrors();
    isSheetOpen.value = true;
}

function openEditSheet(country: Country) {
    editingCountry.value = country;
    form.code = country.code;
    form.name = country.name;
    form.is_default = country.is_default;
    form.is_active = country.is_active;
    form.sort_order = country.sort_order;
    form.clearErrors();
    isSheetOpen.value = true;
}

function openDeleteDialog(country: Country) {
    deletingCountry.value = country;
    showDeleteDialog.value = true;
}

function submitForm() {
    if (editingCountry.value) {
        form.put(update.url(editingCountry.value.id), {
            onSuccess: () => {
                isSheetOpen.value = false;
            },
        });
    } else {
        form.post(store.url(), {
            onSuccess: () => {
                isSheetOpen.value = false;
                form.reset();
            },
        });
    }
}

function confirmDelete() {
    if (!deletingCountry.value) return;

    form.delete(destroy.url(deletingCountry.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingCountry.value = null;
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Ülkeler" />

        <DefinitionsLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading variant="small" title="Ülkeler" description="Sistem üzerinde kullanılan ülke tanımlamaları." />

                    <div v-if="can('country.create')">
                        <Button size="sm" class="h-9" @click="openCreateSheet">
                            <Plus class="mr-2 h-4 w-4" />
                            Yeni Ülke Ekle
                        </Button>
                    </div>
                </div>

                <template v-if="countries.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Kod</TableHead>
                                    <TableHead>Ad</TableHead>
                                    <TableHead class="text-center">Varsayılan</TableHead>
                                    <TableHead class="text-center">Durum</TableHead>
                                    <TableHead v-if="canAny(['country.update', 'country.delete'])" class="text-right w-[160px]">İşlemler</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="country in countries" :key="country.id" class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">{{ country.code }}</TableCell>
                                    <TableCell>{{ country.name }}</TableCell>
                                    <TableCell class="text-center">
                                        <Badge v-if="country.is_default" variant="default">Varsayılan</Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="country.is_active ? 'default' : 'secondary'">
                                            {{ country.is_active ? 'Aktif' : 'Pasif' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell v-if="canAny(['country.update', 'country.delete'])" class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button v-if="can('country.update')" variant="outline" size="sm" class="h-8" @click="openEditSheet(country)">
                                                <Pencil class="mr-2 h-3.5 w-3.5" />
                                                Düzenle
                                            </Button>
                                            <Button v-if="can('country.delete')" variant="outline" size="sm" class="h-8 text-destructive border-destructive/30 hover:bg-destructive/5" @click="openDeleteDialog(country)">
                                                <Trash2 class="mr-2 h-3.5 w-3.5" />
                                                Sil
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </template>

                <div v-else class="mx-2">
                    <EmptyState
                        title="Ülke Bulunamadı"
                        description="Sistemde henüz hiç ülke oluşturulmamış."
                        :action-label="can('country.create') ? 'Yeni Ülke Ekle' : undefined"
                        @action="can('country.create') && openCreateSheet()"
                    />
                </div>
            </div>

            <!-- Create/Edit Sheet -->
            <Sheet v-model:open="isSheetOpen">
                <SheetContent side="right" class="sm:max-w-[400px] p-0 flex flex-col h-full">
                    <SheetHeader class="p-6 border-b shrink-0">
                        <SheetTitle class="flex items-center gap-2 text-xl">
                            <Pencil v-if="editingCountry" class="h-5 w-5 text-primary" />
                            <Plus v-else class="h-5 w-5 text-primary" />
                            {{ editingCountry ? 'Ülke Düzenle' : 'Yeni Ülke Ekle' }}
                        </SheetTitle>
                        <SheetDescription>
                            {{ editingCountry ? 'Ülke bilgilerini güncelleyin.' : 'Sisteme yeni bir ülke ekleyin.' }}
                        </SheetDescription>
                    </SheetHeader>

                    <form id="countryForm" @submit.prevent="submitForm" class="flex-1 overflow-y-auto p-6 space-y-6">
                        <div class="space-y-2">
                            <Label for="code" class="text-sm font-bold">Kod</Label>
                            <Input id="code" v-model="form.code" placeholder="TR" class="h-11" />
                            <InputError :message="form.errors.code" />
                        </div>

                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-bold">Ad</Label>
                            <Input id="name" v-model="form.name" placeholder="Türkiye" class="h-11" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="sort_order" class="text-sm font-bold">Sıralama</Label>
                            <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" class="h-11" />
                            <InputError :message="form.errors.sort_order" />
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-2">
                                <Switch id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                <Label for="is_active">Aktif</Label>
                            </div>

                            <div class="flex items-center gap-2">
                                <Switch id="is_default" :checked="form.is_default" @update:checked="form.is_default = $event" />
                                <Label for="is_default">Varsayılan</Label>
                            </div>
                        </div>
                    </form>

                    <SheetFooter class="p-6 border-t bg-muted/10 shrink-0">
                        <div class="flex w-full flex-col gap-3">
                            <Button type="submit" form="countryForm" class="w-full h-11" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <Check v-else class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Kaydediliyor...' : (editingCountry ? 'Değişiklikleri Kaydet' : 'Ülke Ekle') }}
                            </Button>
                            <Button type="button" variant="ghost" class="w-full h-11" @click="isSheetOpen = false">
                                <X class="mr-2 h-4 w-4" /> İptal
                            </Button>
                        </div>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

            <!-- Delete Confirmation -->
            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Ülkeyi silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingCountry?.name }} ({{ deletingCountry?.code }})</strong> kalıcı olarak silinecektir. Bu işlem geri alınamaz.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>İptal</AlertDialogCancel>
                        <AlertDialogAction class="bg-destructive text-destructive-foreground hover:bg-destructive/90" @click="confirmDelete">
                            Evet, Sil
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DefinitionsLayout>
    </AppLayout>
</template>
