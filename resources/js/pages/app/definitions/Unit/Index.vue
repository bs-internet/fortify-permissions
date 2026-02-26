<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
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
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as unitRoute } from '@/routes/settings/definitions/units';
import { type BreadcrumbItem, type Unit } from '@/types';

type Props = {
    units: Unit[];
    unitTypes: Record<string, string>;
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Birimler', href: unitRoute().url },
];

const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingUnit = ref<Unit | null>(null);
const deletingUnit = ref<Unit | null>(null);

const form = useForm({
    name: '',
    abbreviation: '',
    type: '',
    is_active: true,
    sort_order: 0,
});

function openCreateDialog() {
    editingUnit.value = null;
    form.reset();
    form.clearErrors();
    showFormDialog.value = true;
}

function openEditDialog(unit: Unit) {
    editingUnit.value = unit;
    form.name = unit.name;
    form.abbreviation = unit.abbreviation;
    form.type = unit.type;
    form.is_active = unit.is_active;
    form.sort_order = unit.sort_order;
    form.clearErrors();
    showFormDialog.value = true;
}

function openDeleteDialog(unit: Unit) {
    deletingUnit.value = unit;
    showDeleteDialog.value = true;
}

function submitForm() {
    if (editingUnit.value) {
        form.put(update.url(editingUnit.value.id), {
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
    if (!deletingUnit.value) return;

    form.delete(destroy.url(deletingUnit.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingUnit.value = null;
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
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <Heading variant="small" title="Birimler" description="Sistem üzerinde kullanılan birimler." />

                    <Button @click="openCreateDialog">Yeni Birim Ekle</Button>
                </div>

                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Ad</TableHead>
                                <TableHead>Kısaltma</TableHead>
                                <TableHead>Tip</TableHead>
                                <TableHead class="text-center">Durum</TableHead>
                                <TableHead class="text-center">Sıra</TableHead>
                                <TableHead class="text-right">İşlemler</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="units.length === 0">
                                <TableCell :colspan="6" class="text-center text-muted-foreground"> Henüz birim eklenmemiş. </TableCell>
                            </TableRow>
                            <TableRow v-for="unit in units" :key="unit.id">
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
                                <TableCell class="text-center">{{ unit.sort_order }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="sm" @click="openEditDialog(unit)"> Düzenle </Button>
                                        <Button variant="ghost" size="sm" class="text-destructive" @click="openDeleteDialog(unit)">
                                            Sil
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <!-- Create/Edit Dialog -->
            <Dialog v-model:open="showFormDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{{ editingUnit ? 'Birim Düzenle' : 'Yeni Birim Ekle' }}</DialogTitle>
                        <DialogDescription>
                            {{ editingUnit ? 'Birim bilgilerini güncelleyin.' : 'Sisteme yeni bir birim ekleyin.' }}
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Ad</Label>
                                <Input id="name" v-model="form.name" placeholder="Kilogram" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="abbreviation">Kısaltma</Label>
                                <Input id="abbreviation" v-model="form.abbreviation" placeholder="kg" />
                                <InputError :message="form.errors.abbreviation" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="type">Birim Tipi</Label>
                                <Select v-model="form.type">
                                    <SelectTrigger>
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
                                <Label for="sort_order">Sıralama</Label>
                                <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" />
                                <InputError :message="form.errors.sort_order" />
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Switch id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                            <Label for="is_active">Aktif</Label>
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="showFormDialog = false">İptal</Button>
                            <Button type="submit" :disabled="form.processing">
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
                        <AlertDialogTitle>Birimi silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingUnit?.name }} ({{ deletingUnit?.abbreviation }})</strong> kalıcı olarak silinecektir. Bu
                            işlem geri alınamaz.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>İptal</AlertDialogCancel>
                        <AlertDialogAction @click="confirmDelete">Sil</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DefinitionsLayout>
    </AppLayout>
</template>
