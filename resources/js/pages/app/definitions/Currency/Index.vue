<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2, X, Check } from 'lucide-vue-next';
import { ref } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Definitions/CurrencyController';
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
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as currencyRoute } from '@/routes/settings/definitions/currencies';
import { type BreadcrumbItem, type Currency } from '@/types';

defineProps<{ currencies: Currency[] }>();

const { can } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Para Birimleri', href: currencyRoute().url },
];

const isSheetOpen = ref(false);
const showDeleteDialog = ref(false);
const editingCurrency = ref<Currency | null>(null);

const form = useForm({
    code: '',
    name: '',
    symbol: '',
    decimal_places: 2,
    thousand_separator: '.',
    decimal_separator: ',',
    is_default: false,
    is_active: true,
    sort_order: 0,
});

useClearFormErrors(form);

function openCreateSheet() {
    editingCurrency.value = null;
    form.reset();
    form.clearErrors();
    isSheetOpen.value = true;
}

function openEditSheet(currency: Currency) {
    editingCurrency.value = currency;
    form.code = currency.code;
    form.name = currency.name;
    form.symbol = currency.symbol;
    form.decimal_places = currency.decimal_places;
    form.thousand_separator = currency.thousand_separator;
    form.decimal_separator = currency.decimal_separator;
    form.is_default = currency.is_default;
    form.is_active = currency.is_active;
    form.sort_order = currency.sort_order;
    form.clearErrors();
    isSheetOpen.value = true;
}

function submitForm() {
    if (editingCurrency.value) {
        form.put(update.url(editingCurrency.value.id), {
            onSuccess: () => { isSheetOpen.value = false; },
        });
    } else {
        form.post(store.url(), {
            onSuccess: () => { isSheetOpen.value = false; form.reset(); },
        });
    }
}

function openDeleteDialog() {
    showDeleteDialog.value = true;
}

function confirmDelete() {
    if (!editingCurrency.value) return;
    form.delete(destroy.url(editingCurrency.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            isSheetOpen.value = false;
            editingCurrency.value = null;
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Para Birimleri" />

        <DefinitionsLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading variant="small" title="Para Birimleri" description="Sistem üzerinde kullanılan para birimleri." />
                    <div v-if="can('currency.create')">
                        <Button size="sm" class="h-9" @click="openCreateSheet">
                            <Plus class="mr-2 h-4 w-4" />
                            Yeni Para Birimi Ekle
                        </Button>
                    </div>
                </div>

                <template v-if="currencies.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Kod</TableHead>
                                    <TableHead>Ad</TableHead>
                                    <TableHead>Sembol</TableHead>
                                    <TableHead class="text-center">Ondalık</TableHead>
                                    <TableHead class="text-center">Varsayılan</TableHead>
                                    <TableHead class="text-center">Durum</TableHead>
                                    <TableHead v-if="can('currency.update')" class="text-right w-[100px]">İşlemler</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="currency in currencies" :key="currency.id" class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">{{ currency.code }}</TableCell>
                                    <TableCell>{{ currency.name }}</TableCell>
                                    <TableCell>{{ currency.symbol }}</TableCell>
                                    <TableCell class="text-center">{{ currency.decimal_places }}</TableCell>
                                    <TableCell class="text-center">
                                        <Badge v-if="currency.is_default" variant="default">Varsayılan</Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="currency.is_active ? 'default' : 'secondary'">
                                            {{ currency.is_active ? 'Aktif' : 'Pasif' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell v-if="can('currency.update')" class="text-right">
                                        <Button variant="outline" size="sm" class="h-8" @click="openEditSheet(currency)">
                                            <Pencil class="mr-2 h-3.5 w-3.5" />
                                            Düzenle
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </template>

                <div v-else class="mx-2">
                    <EmptyState
                        title="Para Birimi Bulunamadı"
                        description="Sistemde henüz hiç para birimi oluşturulmamış."
                        :action-label="can('currency.create') ? 'Yeni Para Birimi Ekle' : undefined"
                        @action="can('currency.create') && openCreateSheet()"
                    />
                </div>
            </div>

            <Sheet v-model:open="isSheetOpen">
                <SheetContent side="right" class="sm:max-w-[480px] p-0 flex flex-col h-full">
                    <SheetHeader class="p-6 border-b shrink-0">
                        <SheetTitle class="flex items-center gap-2 text-xl">
                            <Pencil v-if="editingCurrency" class="h-5 w-5 text-primary" />
                            <Plus v-else class="h-5 w-5 text-primary" />
                            {{ editingCurrency ? 'Para Birimi Düzenle' : 'Yeni Para Birimi Ekle' }}
                        </SheetTitle>
                        <SheetDescription>
                            {{ editingCurrency ? 'Para birimi bilgilerini güncelleyin.' : 'Sisteme yeni bir para birimi ekleyin.' }}
                        </SheetDescription>
                    </SheetHeader>

                    <form id="currencyForm" class="flex-1 overflow-y-auto p-6 space-y-6" @submit.prevent="submitForm">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="code" class="text-sm font-bold">Kod</Label>
                                <Input id="code" v-model="form.code" placeholder="TRY" class="h-11" />
                                <InputError :message="form.errors.code" />
                            </div>
                            <div class="space-y-2">
                                <Label for="symbol" class="text-sm font-bold">Sembol</Label>
                                <Input id="symbol" v-model="form.symbol" placeholder="₺" class="h-11" />
                                <InputError :message="form.errors.symbol" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-bold">Ad</Label>
                            <Input id="name" v-model="form.name" placeholder="Türk Lirası" class="h-11" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="decimal_places" class="text-sm font-bold">Ondalık Basamak</Label>
                                <Input id="decimal_places" v-model.number="form.decimal_places" type="number" min="0" max="10" class="h-11" />
                                <InputError :message="form.errors.decimal_places" />
                            </div>
                            <div class="space-y-2">
                                <Label for="sort_order" class="text-sm font-bold">Sıralama</Label>
                                <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" class="h-11" />
                                <InputError :message="form.errors.sort_order" />
                            </div>
                        </div>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="thousand_separator" class="text-sm font-bold">Binlik Ayırıcı</Label>
                                <Input id="thousand_separator" v-model="form.thousand_separator" placeholder="." class="h-11" />
                                <InputError :message="form.errors.thousand_separator" />
                            </div>
                            <div class="space-y-2">
                                <Label for="decimal_separator" class="text-sm font-bold">Ondalık Ayırıcı</Label>
                                <Input id="decimal_separator" v-model="form.decimal_separator" placeholder="," class="h-11" />
                                <InputError :message="form.errors.decimal_separator" />
                            </div>
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
                            <Button type="submit" form="currencyForm" class="w-full h-11" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <Check v-else class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Kaydediliyor...' : (editingCurrency ? 'Değişiklikleri Kaydet' : 'Para Birimi Ekle') }}
                            </Button>
                            <Button type="button" variant="ghost" class="w-full h-11" @click="isSheetOpen = false">
                                <X class="mr-2 h-4 w-4" /> İptal
                            </Button>
                            <Button
                                v-if="editingCurrency && can('currency.delete')"
                                type="button"
                                variant="outline"
                                class="w-full h-11 text-destructive border-destructive/30 hover:bg-destructive/5"
                                @click="openDeleteDialog"
                            >
                                <Trash2 class="mr-2 h-4 w-4" />
                                Para Birimini Sil
                            </Button>
                        </div>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Para birimini silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ editingCurrency?.name }} ({{ editingCurrency?.code }})</strong> kalıcı olarak silinecektir. Bu işlem geri alınamaz.
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
