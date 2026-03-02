<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2 } from 'lucide-vue-next';
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
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import EmptyState from '@/components/ui/empty-state/EmptyState.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { index as currencyRoute } from '@/routes/settings/definitions/currencies';
import { type BreadcrumbItem, type Currency } from '@/types';

type Props = {
    currencies: Currency[];
};

defineProps<Props>();

const { can, canAny } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Para Birimleri', href: currencyRoute().url },
];

const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingCurrency = ref<Currency | null>(null);
const deletingCurrency = ref<Currency | null>(null);

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

function openCreateDialog() {
    editingCurrency.value = null;
    form.reset();
    form.clearErrors();
    showFormDialog.value = true;
}

function openEditDialog(currency: Currency) {
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
    showFormDialog.value = true;
}

function openDeleteDialog(currency: Currency) {
    deletingCurrency.value = currency;
    showDeleteDialog.value = true;
}

function submitForm() {
    if (editingCurrency.value) {
        form.put(update.url(editingCurrency.value.id), {
            onSuccess: () => {
                showFormDialog.value = false;
            },
        });
    } else {
        form.post(store.url(), {
            onSuccess: () => {
                showFormDialog.value = false;
                form.reset();
            },
        });
    }
}

function confirmDelete() {
    if (!deletingCurrency.value) return;

    form.delete(destroy.url(deletingCurrency.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingCurrency.value = null;
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
                        <Button size="sm" class="h-9" @click="openCreateDialog">
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
                                    <TableHead v-if="canAny(['currency.update', 'currency.delete'])" class="text-right w-[160px]">İşlemler</TableHead>
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
                                    <TableCell v-if="canAny(['currency.update', 'currency.delete'])" class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button v-if="can('currency.update')" variant="outline" size="sm" class="h-8" @click="openEditDialog(currency)">
                                                <Pencil class="mr-2 h-3.5 w-3.5" />
                                                Düzenle
                                            </Button>
                                            <Button v-if="can('currency.delete')" variant="outline" size="sm" class="h-8 text-destructive border-destructive/30 hover:bg-destructive/5" @click="openDeleteDialog(currency)">
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
                        title="Para Birimi Bulunamadı"
                        description="Sistemde henüz hiç para birimi oluşturulmamış."
                        :action-label="can('currency.create') ? 'Yeni Para Birimi Ekle' : undefined"
                        @action="can('currency.create') && openCreateDialog()"
                    />
                </div>
            </div>

            <!-- Create/Edit Dialog -->
            <Dialog v-model:open="showFormDialog">
                <DialogContent class="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>{{ editingCurrency ? 'Para Birimi Düzenle' : 'Yeni Para Birimi Ekle' }}</DialogTitle>
                        <DialogDescription>
                            {{ editingCurrency ? 'Para birimi bilgilerini güncelleyin.' : 'Sisteme yeni bir para birimi ekleyin.' }}
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="code">Kod</Label>
                                <Input id="code" v-model="form.code" placeholder="TRY" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.code" />
                            </div>

                            <div class="space-y-2">
                                <Label for="name">Ad</Label>
                                <Input id="name" v-model="form.name" placeholder="Türk Lirası" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="symbol">Sembol</Label>
                                <Input id="symbol" v-model="form.symbol" placeholder="₺" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.symbol" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="decimal_places">Ondalık Basamak</Label>
                                <Input id="decimal_places" v-model.number="form.decimal_places" type="number" min="0" max="10" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.decimal_places" />
                            </div>

                            <div class="space-y-2">
                                <Label for="thousand_separator">Binlik Ayırıcı</Label>
                                <Input id="thousand_separator" v-model="form.thousand_separator" placeholder="." class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.thousand_separator" />
                            </div>

                            <div class="space-y-2">
                                <Label for="decimal_separator">Ondalık Ayırıcı</Label>
                                <Input id="decimal_separator" v-model="form.decimal_separator" placeholder="," class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.decimal_separator" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="sort_order">Sıralama</Label>
                                <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.sort_order" />
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

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="showFormDialog = false">İptal</Button>
                            <Button type="submit" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                {{ form.processing ? 'Kaydediliyor...' : 'Kaydet' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation -->
            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Para birimini silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingCurrency?.name }} ({{ deletingCurrency?.code }})</strong> kalıcı olarak silinecektir. Bu işlem geri alınamaz.
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
