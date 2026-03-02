<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2, ChevronsUpDown, Check, X } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Definitions/TaxController';
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
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import EmptyState from '@/components/ui/empty-state/EmptyState.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as taxRoute } from '@/routes/settings/definitions/taxes';
import { type BreadcrumbItem, type Country, type Tax } from '@/types';

const props = defineProps<{
    taxes: Tax[];
    countries: Country[];
}>();

const { can } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Vergiler', href: taxRoute().url },
];

const isSheetOpen = ref(false);
const showDeleteDialog = ref(false);
const editingTax = ref<Tax | null>(null);
const countryPopoverOpen = ref(false);

const form = useForm({
    name: '',
    rate: 0,
    countries: [] as string[],
    is_active: true,
    sort_order: 0,
});

useClearFormErrors(form);

const selectedCountryNames = computed(() => {
    if (form.countries.length === 0) return '';
    const names = props.countries
        .filter((c) => form.countries.includes(c.id))
        .map((c) => c.name);
    return names.join(', ');
});

function toggleCountry(countryId: string) {
    const index = form.countries.indexOf(countryId);
    if (index === -1) {
        form.countries.push(countryId);
    } else {
        form.countries.splice(index, 1);
    }
}

function openCreateSheet() {
    editingTax.value = null;
    form.reset();
    form.clearErrors();
    isSheetOpen.value = true;
}

function openEditSheet(tax: Tax) {
    editingTax.value = tax;
    form.name = tax.name;
    form.rate = tax.rate;
    form.countries = tax.countries.map((c) => c.id);
    form.is_active = tax.is_active;
    form.sort_order = tax.sort_order;
    form.clearErrors();
    isSheetOpen.value = true;
}

function submitForm() {
    if (editingTax.value) {
        form.put(update.url(editingTax.value.id), {
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
    if (!editingTax.value) return;
    form.delete(destroy.url(editingTax.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            isSheetOpen.value = false;
            editingTax.value = null;
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Vergiler" />

        <DefinitionsLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading variant="small" title="Vergiler" description="Sistem üzerinde kullanılan vergi oranları." />
                    <div v-if="can('tax.create')">
                        <Button size="sm" class="h-9" @click="openCreateSheet">
                            <Plus class="mr-2 h-4 w-4" />
                            Yeni Vergi Ekle
                        </Button>
                    </div>
                </div>

                <template v-if="taxes.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ad</TableHead>
                                    <TableHead class="text-center">Oran (%)</TableHead>
                                    <TableHead>Ülkeler</TableHead>
                                    <TableHead class="text-center">Durum</TableHead>
                                    <TableHead v-if="can('tax.update')" class="text-right w-[100px]">İşlemler</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="tax in taxes" :key="tax.id" class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">{{ tax.name }}</TableCell>
                                    <TableCell class="text-center">{{ tax.rate }}</TableCell>
                                    <TableCell>
                                        <div class="flex flex-wrap gap-1">
                                            <Badge v-for="country in tax.countries" :key="country.id" variant="outline" class="text-xs">
                                                {{ country.name }}
                                            </Badge>
                                            <span v-if="tax.countries.length === 0" class="text-muted-foreground text-sm">-</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="tax.is_active ? 'default' : 'secondary'">
                                            {{ tax.is_active ? 'Aktif' : 'Pasif' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell v-if="can('tax.update')" class="text-right">
                                        <Button variant="outline" size="sm" class="h-8" @click="openEditSheet(tax)">
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
                        title="Vergi Bulunamadı"
                        description="Sistemde henüz hiç vergi oranı oluşturulmamış."
                        :action-label="can('tax.create') ? 'Yeni Vergi Ekle' : undefined"
                        @action="can('tax.create') && openCreateSheet()"
                    />
                </div>
            </div>

            <Sheet v-model:open="isSheetOpen">
                <SheetContent side="right" class="sm:max-w-[480px] p-0 flex flex-col h-full">
                    <SheetHeader class="p-6 border-b shrink-0">
                        <SheetTitle class="flex items-center gap-2 text-xl">
                            <Pencil v-if="editingTax" class="h-5 w-5 text-primary" />
                            <Plus v-else class="h-5 w-5 text-primary" />
                            {{ editingTax ? 'Vergi Düzenle' : 'Yeni Vergi Ekle' }}
                        </SheetTitle>
                        <SheetDescription>
                            {{ editingTax ? 'Vergi bilgilerini güncelleyin.' : 'Sisteme yeni bir vergi oranı ekleyin.' }}
                        </SheetDescription>
                    </SheetHeader>

                    <form id="taxForm" class="flex-1 overflow-y-auto p-6 space-y-6" @submit.prevent="submitForm">
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-bold">Ad</Label>
                            <Input id="name" v-model="form.name" placeholder="KDV %20" class="h-11" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="rate" class="text-sm font-bold">Oran (%)</Label>
                            <Input id="rate" v-model.number="form.rate" type="number" min="0" max="100" step="0.01" placeholder="20" class="h-11" />
                            <InputError :message="form.errors.rate" />
                        </div>
                        <div class="space-y-2">
                            <Label class="text-sm font-bold">Ülkeler</Label>
                            <Popover v-model:open="countryPopoverOpen">
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        class="w-full justify-between h-11 font-normal"
                                        :class="{ 'text-muted-foreground': form.countries.length === 0 }"
                                    >
                                        <span class="truncate">
                                            {{ form.countries.length > 0 ? selectedCountryNames : 'Ülke seçin...' }}
                                        </span>
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-[--reka-popover-trigger-width] p-0" align="start">
                                    <Command>
                                        <CommandInput placeholder="Ülke ara..." />
                                        <CommandList>
                                            <CommandEmpty>Ülke bulunamadı.</CommandEmpty>
                                            <CommandGroup>
                                                <CommandItem
                                                    v-for="country in countries"
                                                    :key="country.id"
                                                    :value="country.name"
                                                    @select="toggleCountry(country.id)"
                                                >
                                                    <Check
                                                        class="mr-2 h-4 w-4"
                                                        :class="form.countries.includes(country.id) ? 'opacity-100' : 'opacity-0'"
                                                    />
                                                    {{ country.name }} ({{ country.code }})
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                            <div v-if="form.countries.length > 0" class="flex flex-wrap gap-1 mt-2">
                                <Badge
                                    v-for="countryId in form.countries"
                                    :key="countryId"
                                    variant="secondary"
                                    class="cursor-pointer"
                                    @click="toggleCountry(countryId)"
                                >
                                    {{ countries.find((c) => c.id === countryId)?.name }}
                                    <span class="ml-1">&times;</span>
                                </Badge>
                            </div>
                            <InputError :message="form.errors.countries" />
                        </div>
                        <div class="space-y-2">
                            <Label for="sort_order" class="text-sm font-bold">Sıralama</Label>
                            <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" class="h-11" />
                            <InputError :message="form.errors.sort_order" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Switch id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                            <Label for="is_active">Aktif</Label>
                        </div>
                    </form>

                    <SheetFooter class="p-6 border-t bg-muted/10 shrink-0">
                        <div class="flex w-full flex-col gap-3">
                            <Button type="submit" form="taxForm" class="w-full h-11" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <Check v-else class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Kaydediliyor...' : (editingTax ? 'Değişiklikleri Kaydet' : 'Vergi Ekle') }}
                            </Button>
                            <Button type="button" variant="ghost" class="w-full h-11" @click="isSheetOpen = false">
                                <X class="mr-2 h-4 w-4" /> İptal
                            </Button>
                            <Button
                                v-if="editingTax && can('tax.delete')"
                                type="button"
                                variant="outline"
                                class="w-full h-11 text-destructive border-destructive/30 hover:bg-destructive/5"
                                @click="openDeleteDialog"
                            >
                                <Trash2 class="mr-2 h-4 w-4" />
                                Vergiyi Sil
                            </Button>
                        </div>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Vergiyi silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ editingTax?.name }}</strong> kalıcı olarak silinecektir. Bu işlem geri alınamaz.
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
