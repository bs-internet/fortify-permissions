<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2 } from 'lucide-vue-next';
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
import { index as unitRoute } from '@/routes/settings/definitions/units';
import { type BreadcrumbItem, type Unit } from '@/types';

type Props = {
    units: Unit[];
    unitTypes: Record<string, string>;
};

const props = defineProps<Props>();

const { can, canAny } = usePermission();

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

useClearFormErrors(form);

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
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading variant="small" title="Birimler" description="Sistem üzerinde kullanılan birimler." />

                    <div v-if="can('unit.create')">
                        <Button size="sm" class="h-9" @click="openCreateDialog">
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
                                    <TableHead class="text-center">Sıra</TableHead>
                                    <TableHead v-if="canAny(['unit.update', 'unit.delete'])" class="text-right w-[160px]">İşlemler</TableHead>
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
                                    <TableCell class="text-center">{{ unit.sort_order }}</TableCell>
                                    <TableCell v-if="canAny(['unit.update', 'unit.delete'])" class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button v-if="can('unit.update')" variant="outline" size="sm" class="h-8" @click="openEditDialog(unit)">
                                                <Pencil class="mr-2 h-3.5 w-3.5" />
                                                Düzenle
                                            </Button>
                                            <Button v-if="can('unit.delete')" variant="outline" size="sm" class="h-8 text-destructive border-destructive/30 hover:bg-destructive/5" @click="openDeleteDialog(unit)">
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
                        title="Birim Bulunamadı"
                        description="Sistemde henüz hiç birim oluşturulmamış."
                        :action-label="can('unit.create') ? 'Yeni Birim Ekle' : undefined"
                        @action="can('unit.create') && openCreateDialog()"
                    />
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
                                <Input id="name" v-model="form.name" placeholder="Kilogram" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="abbreviation">Kısaltma</Label>
                                <Input id="abbreviation" v-model="form.abbreviation" placeholder="kg" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.abbreviation" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="type">Birim Tipi</Label>
                                <Select v-model="form.type">
                                    <SelectTrigger class="shadow-none focus:ring-1">
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
                                <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" class="shadow-none focus-visible:ring-1" />
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
                        <AlertDialogTitle>Birimi silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingUnit?.name }} ({{ deletingUnit?.abbreviation }})</strong> kalıcı olarak silinecektir. Bu işlem geri alınamaz.
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
