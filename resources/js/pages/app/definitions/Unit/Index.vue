<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2, X, Check, ChevronLeft, ChevronRight, ArrowRightLeft } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Definitions/UnitController';
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
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as unitRoute } from '@/routes/settings/definitions/units';
import { type BreadcrumbItem, type Unit, type UnitOption, type PaginationResponse } from '@/types';

const props = defineProps<{
    units: PaginationResponse<Unit>;
    allUnits: UnitOption[];
}>();

const { can } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Birimler', href: unitRoute().url },
];

const isSheetOpen = ref(false);
const showDeleteDialog = ref(false);
const editingUnit = ref<Unit | null>(null);

const form = useForm({
    name: '',
    abbreviation: '',
    is_active: true,
    sort_order: 0,
});

useClearFormErrors(form);

const conversionForm = useForm({
    to_unit_id: '',
    factor: null as number | null,
});

useClearFormErrors(conversionForm);

const availableUnitsForConversion = computed(() => {
    if (!editingUnit.value) return [];
    const existingIds = (editingUnit.value.conversions ?? []).map((c) => c.id);
    return props.allUnits.filter(
        (u) => u.id !== editingUnit.value!.id && !existingIds.includes(u.id),
    );
});

function openCreateSheet() {
    editingUnit.value = null;
    form.reset();
    form.clearErrors();
    isSheetOpen.value = true;
}

function openEditSheet(unit: Unit) {
    editingUnit.value = unit;
    form.name = unit.name;
    form.abbreviation = unit.abbreviation;
    form.is_active = unit.is_active;
    form.sort_order = unit.sort_order;
    form.clearErrors();
    conversionForm.reset();
    conversionForm.clearErrors();
    isSheetOpen.value = true;
}

function submitForm() {
    if (editingUnit.value) {
        form.put(update.url(editingUnit.value.id), {
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
    if (!editingUnit.value) return;
    form.delete(destroy.url(editingUnit.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            isSheetOpen.value = false;
            editingUnit.value = null;
        },
    });
}

function addConversion() {
    if (!editingUnit.value) return;
    const url = `/settings/definitions/units/${editingUnit.value.id}/conversions`;
    conversionForm.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            conversionForm.reset();
            const updatedUnit = props.units.data.find((u) => u.id === editingUnit.value?.id);
            if (updatedUnit) {
                editingUnit.value = updatedUnit;
            }
        },
    });
}

function removeConversion(toUnitId: string) {
    if (!editingUnit.value) return;
    const url = `/settings/definitions/units/${editingUnit.value.id}/conversions/${toUnitId}`;
    router.delete(url, {
        preserveScroll: true,
        onSuccess: () => {
            const updatedUnit = props.units.data.find((u) => u.id === editingUnit.value?.id);
            if (updatedUnit) {
                editingUnit.value = updatedUnit;
            }
        },
    });
}

function handlePageChange(page: number) {
    router.visit(unitRoute().url, {
        data: { page },
        preserveScroll: true,
        preserveState: true,
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Birimler" />

        <DefinitionsLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading variant="small" title="Birimler" description="Sistem üzerinde kullanılan birimler." />
                    <div v-if="can('unit.create')">
                        <Button size="sm" class="h-9" @click="openCreateSheet">
                            <Plus class="mr-2 h-4 w-4" />
                            Yeni Birim Ekle
                        </Button>
                    </div>
                </div>

                <template v-if="units.data.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ad</TableHead>
                                    <TableHead>Kısaltma</TableHead>
                                    <TableHead class="text-center">Dönüşüm</TableHead>
                                    <TableHead class="text-center">Durum</TableHead>
                                    <TableHead v-if="can('unit.update')" class="text-right w-[100px]">İşlemler
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="unit in units.data" :key="unit.id"
                                    class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">{{ unit.name }}</TableCell>
                                    <TableCell>{{ unit.abbreviation }}</TableCell>
                                    <TableCell class="text-center">
                                        <Badge v-if="unit.conversions && unit.conversions.length > 0" variant="outline">
                                            {{ unit.conversions.length }} dönüşüm
                                        </Badge>
                                        <span v-else class="text-sm text-muted-foreground">-</span>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="unit.is_active ? 'default' : 'secondary'">
                                            {{ unit.is_active ? 'Aktif' : 'Pasif' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell v-if="can('unit.update')" class="text-right">
                                        <Button variant="outline" size="sm" class="h-8" @click="openEditSheet(unit)">
                                            <Pencil class="mr-2 h-3.5 w-3.5" />
                                            Düzenle
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="units.last_page > 1"
                        class="mt-4 flex flex-col items-center justify-between gap-4 sm:flex-row px-2">
                        <div class="text-sm text-muted-foreground font-medium">
                            Toplam {{ units.total }} kayıttan {{ units.from }}-{{ units.to }} arası gösteriliyor
                        </div>
                        <Pagination :total="units.total" :items-per-page="units.per_page"
                            :default-page="units.current_page" @update:page="handlePageChange">
                            <PaginationContent>
                                <PaginationPrevious class="cursor-pointer">
                                    <ChevronLeft class="h-4 w-4" />
                                </PaginationPrevious>
                                <template v-for="(item, index) in units.links" :key="index">
                                    <PaginationItem v-if="item.url && !isNaN(Number(item.label))" :value="index">
                                        <Button size="icon" variant="ghost" class="h-9 w-9"
                                            :class="{ 'border': item.active }"
                                            @click="handlePageChange(Number(item.label))">
                                            {{ item.label }}
                                        </Button>
                                    </PaginationItem>
                                </template>
                                <PaginationNext class="cursor-pointer">
                                    <ChevronRight class="h-4 w-4" />
                                </PaginationNext>
                            </PaginationContent>
                        </Pagination>
                    </div>
                </template>

                <div v-else class="mx-2">
                    <EmptyState title="Birim Bulunamadı" description="Sistemde henüz hiç birim oluşturulmamış."
                        :action-label="can('unit.create') ? 'Yeni Birim Ekle' : undefined"
                        @action="can('unit.create') && openCreateSheet()" />
                </div>
            </div>

            <Sheet v-model:open="isSheetOpen">
                <SheetContent side="right" class="sm:max-w-[440px] p-0 flex flex-col h-full">
                    <SheetHeader class="p-6 border-b shrink-0">
                        <SheetTitle class="flex items-center gap-2 text-xl">
                            <Pencil v-if="editingUnit" class="h-5 w-5 text-primary" />
                            <Plus v-else class="h-5 w-5 text-primary" />
                            {{ editingUnit ? 'Birim Düzenle' : 'Yeni Birim Ekle' }}
                        </SheetTitle>
                        <SheetDescription>
                            {{ editingUnit ? 'Birim bilgilerini güncelleyin.' : 'Sisteme yeni bir birim ekleyin.' }}
                        </SheetDescription>
                    </SheetHeader>

                    <div class="flex-1 overflow-y-auto">
                        <form id="unitForm" class="p-6 space-y-6" @submit.prevent="submitForm">
                            <div class="space-y-2">
                                <Label for="name" class="text-sm font-bold">Ad</Label>
                                <Input id="name" v-model="form.name" placeholder="Kilogram" class="h-11" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="abbreviation" class="text-sm font-bold">Kısaltma</Label>
                                    <Input id="abbreviation" v-model="form.abbreviation" placeholder="kg" class="h-11" />
                                    <InputError :message="form.errors.abbreviation" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="sort_order" class="text-sm font-bold">Sıralama</Label>
                                    <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0"
                                        class="h-11" />
                                    <InputError :message="form.errors.sort_order" />
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Switch id="is_active" :checked="form.is_active"
                                    @update:checked="form.is_active = $event" />
                                <Label for="is_active">Aktif</Label>
                            </div>
                        </form>

                        <!-- Dönüşümler (sadece düzenlemede) -->
                        <template v-if="editingUnit">
                            <Separator />
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-2 text-sm font-bold">
                                    <ArrowRightLeft class="h-4 w-4 text-primary" />
                                    Dönüşümler
                                </div>

                                <!-- Mevcut dönüşümler -->
                                <div v-if="editingUnit.conversions && editingUnit.conversions.length > 0" class="space-y-2">
                                    <div
                                        v-for="conv in editingUnit.conversions"
                                        :key="conv.id"
                                        class="flex items-center justify-between rounded-md border border-border px-3 py-2"
                                    >
                                        <div class="text-sm">
                                            <span class="font-medium">1 {{ editingUnit.abbreviation }}</span>
                                            <span class="text-muted-foreground mx-1">=</span>
                                            <span class="font-medium">{{ conv.pivot.factor }} {{ conv.abbreviation }}</span>
                                        </div>
                                        <Button
                                            v-if="can('unit.update')"
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            class="h-7 w-7 text-muted-foreground hover:text-destructive"
                                            @click="removeConversion(conv.id)"
                                        >
                                            <X class="h-3.5 w-3.5" />
                                        </Button>
                                    </div>
                                </div>
                                <p v-else class="text-sm text-muted-foreground">
                                    Bu birim için henüz dönüşüm tanımlanmamış.
                                </p>

                                <!-- Yeni dönüşüm ekle -->
                                <div v-if="can('unit.update') && availableUnitsForConversion.length > 0" class="space-y-3 rounded-md border border-dashed border-border p-3">
                                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Yeni Dönüşüm Ekle</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="space-y-1">
                                            <Label class="text-xs">Hedef Birim</Label>
                                            <Select v-model="conversionForm.to_unit_id">
                                                <SelectTrigger class="h-9">
                                                    <SelectValue placeholder="Birim seçin" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem
                                                        v-for="u in availableUnitsForConversion"
                                                        :key="u.id"
                                                        :value="u.id"
                                                    >
                                                        {{ u.name }} ({{ u.abbreviation }})
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <InputError :message="conversionForm.errors.to_unit_id" />
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-xs">Katsayı</Label>
                                            <Input
                                                v-model.number="conversionForm.factor"
                                                type="number"
                                                step="any"
                                                min="0"
                                                placeholder="12"
                                                class="h-9"
                                            />
                                            <InputError :message="conversionForm.errors.factor" />
                                        </div>
                                    </div>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        class="w-full"
                                        :disabled="conversionForm.processing || !conversionForm.to_unit_id || !conversionForm.factor"
                                        @click="addConversion"
                                    >
                                        <Loader2 v-if="conversionForm.processing" class="mr-2 h-3.5 w-3.5 animate-spin" />
                                        <Plus v-else class="mr-2 h-3.5 w-3.5" />
                                        Dönüşüm Ekle
                                    </Button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <SheetFooter class="p-6 border-t bg-muted/10 shrink-0">
                        <div class="flex w-full flex-col gap-3">
                            <Button type="submit" form="unitForm" class="w-full h-11" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <Check v-else class="mr-2 h-4 w-4" />
{{ form.processing
    ? 'Kaydediliyor...'
    : (editingUnit
        ? 'Değişiklikleri Kaydet'
        : 'Birim Ekle') }}
                            </Button>
                            <Button type="button" variant="ghost" class="w-full h-11" @click="isSheetOpen = false">
                                <X class="mr-2 h-4 w-4" /> İptal
                            </Button>
                            <Button v-if="editingUnit && can('unit.delete')" type="button" variant="outline"
                                class="w-full h-11 text-destructive border-destructive/30 hover:bg-destructive/5"
                                @click="openDeleteDialog">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Birimi Sil
                            </Button>
                        </div>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Birimi silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ editingUnit?.name }} ({{ editingUnit?.abbreviation }})</strong> kalıcı olarak
                            silinecektir.
                            Bu işlem geri alınamaz.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>İptal</AlertDialogCancel>
                        <AlertDialogAction class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                            @click="confirmDelete">
                            Evet, Sil
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DefinitionsLayout>
    </AppLayout>
</template>
