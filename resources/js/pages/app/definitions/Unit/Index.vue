<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2, X, Check } from 'lucide-vue-next';
import { ref } from 'vue';
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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as unitRoute } from '@/routes/settings/definitions/units';
import { type BreadcrumbItem, type Unit } from '@/types';

const props = defineProps<{
    units: Unit[];
    unitTypes: Record<string, string>;
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
    type: '',
    is_active: true,
    sort_order: 0,
});

useClearFormErrors(form);

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
    form.type = unit.type;
    form.is_active = unit.is_active;
    form.sort_order = unit.sort_order;
    form.clearErrors();
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

function getTypeLabel(type: string): string {
    return props.unitTypes[type] ?? type;
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

                <template v-if="units.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ad</TableHead>
                                    <TableHead>Kısaltma</TableHead>
                                    <TableHead>Tip</TableHead>
                                    <TableHead class="text-center">Durum</TableHead>
                                    <TableHead v-if="can('unit.update')" class="text-right w-[100px]">İşlemler</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="unit in units" :key="unit.id" class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">{{ unit.name }}</TableCell>
                                    <TableCell>{{ unit.abbreviation }}</TableCell>
                                    <TableCell>
                                        <Badge variant="outline">{{ getTypeLabel(unit.type) }}</Badge>
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
                </template>

                <div v-else class="mx-2">
                    <EmptyState
                        title="Birim Bulunamadı"
                        description="Sistemde henüz hiç birim oluşturulmamış."
                        :action-label="can('unit.create') ? 'Yeni Birim Ekle' : undefined"
                        @action="can('unit.create') && openCreateSheet()"
                    />
                </div>
            </div>

            <Sheet v-model:open="isSheetOpen">
                <SheetContent side="right" class="sm:max-w-[400px] p-0 flex flex-col h-full">
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

                    <form id="unitForm" class="flex-1 overflow-y-auto p-6 space-y-6" @submit.prevent="submitForm">
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-bold">Ad</Label>
                            <Input id="name" v-model="form.name" placeholder="Kilogram" class="h-11" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="abbreviation" class="text-sm font-bold">Kısaltma</Label>
                            <Input id="abbreviation" v-model="form.abbreviation" placeholder="kg" class="h-11" />
                            <InputError :message="form.errors.abbreviation" />
                        </div>
                        <div class="space-y-2">
                            <Label for="type" class="text-sm font-bold">Birim Tipi</Label>
                            <Select v-model="form.type">
                                <SelectTrigger class="h-11">
                                    <SelectValue placeholder="Tip seçin" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(label, value) in unitTypes" :key="value" :value="value">
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.type" />
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
                            <Button type="submit" form="unitForm" class="w-full h-11" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <Check v-else class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Kaydediliyor...' : (editingUnit ? 'Değişiklikleri Kaydet' : 'Birim Ekle') }}
                            </Button>
                            <Button type="button" variant="ghost" class="w-full h-11" @click="isSheetOpen = false">
                                <X class="mr-2 h-4 w-4" /> İptal
                            </Button>
                            <Button
                                v-if="editingUnit && can('unit.delete')"
                                type="button"
                                variant="outline"
                                class="w-full h-11 text-destructive border-destructive/30 hover:bg-destructive/5"
                                @click="openDeleteDialog"
                            >
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
                            <strong>{{ editingUnit?.name }} ({{ editingUnit?.abbreviation }})</strong> kalıcı olarak silinecektir. Bu işlem geri alınamaz.
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
