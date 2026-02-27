<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Users/PermissionController';
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
import { Textarea } from '@/components/ui/textarea';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import EmptyState from '@/components/ui/empty-state/EmptyState.vue';
import { index as permissionRoute } from '@/routes/users/permissions';
import { type BreadcrumbItem, type Permission } from '@/types';

type Props = {
    permissions: Permission[];
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Yetkiler', href: permissionRoute().url },
];

const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingPermission = ref<Permission | null>(null);
const deletingPermission = ref<Permission | null>(null);

const form = useForm({
    name: '',
    label: '',
    description: '',
});

function openCreateDialog() {
    editingPermission.value = null;
    form.reset();
    form.clearErrors();
    showFormDialog.value = true;
}

function openEditDialog(permission: Permission) {
    editingPermission.value = permission;
    form.name = permission.name;
    form.label = permission.label;
    form.description = permission.description ?? '';
    form.clearErrors();
    showFormDialog.value = true;
}

function openDeleteDialog(permission: Permission) {
    deletingPermission.value = permission;
    showDeleteDialog.value = true;
}

function submitForm() {
    if (editingPermission.value) {
        form.put(update.url(editingPermission.value.id), {
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
    if (!deletingPermission.value) return;

    form.delete(destroy.url(deletingPermission.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingPermission.value = null;
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Yetkiler" />

        <UsersLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <Heading variant="small" title="Yetkiler"
                        description="Sistem kullanıcılarının sahip olduğu yetkiler." />

                    <Button @click="openCreateDialog">Yeni Yetki Ekle</Button>
                </div>

                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Teknik Ad</TableHead>
                                <TableHead>Görünen Ad</TableHead>
                                <TableHead>Açıklama</TableHead>
                                <TableHead class="text-right">İşlemler</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="permissions.length === 0">
                                <TableCell :colspan="4" class="p-0">
                                    <EmptyState title="Yetki Bulunamadı"
                                        description="Sistemde henüz hiç yetki oluşturulmamış."
                                        actionLabel="Yeni Yetki Ekle" @action="openCreateDialog" />
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="permission in permissions" :key="permission.id">
                                <TableCell class="font-mono text-sm">{{ permission.name }}</TableCell>
                                <TableCell class="font-medium">{{ permission.label }}</TableCell>
                                <TableCell class="text-muted-foreground">{{ permission.description ?? '-' }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="sm" @click="openEditDialog(permission)"> Düzenle
                                        </Button>
                                        <Button variant="ghost" size="sm" class="text-destructive"
                                            @click="openDeleteDialog(permission)">
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
                        <DialogTitle>{{ editingPermission ? 'Yetki Düzenle' : 'Yeni Yetki Ekle' }}</DialogTitle>
                        <DialogDescription>
                            {{
                                editingPermission
                                    ? 'Yetki bilgilerini güncelleyin.'
                                    : 'Sisteme yeni bir yetki ekleyin.'
                            }}
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="space-y-2">
                            <Label for="name">Teknik Ad (slug)</Label>
                            <Input id="name" v-model="form.name" placeholder="users.create" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="label">Görünen Ad</Label>
                            <Input id="label" v-model="form.label" placeholder="Kullanıcı Oluşturma" />
                            <InputError :message="form.errors.label" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Açıklama</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Bu yetki ne işe yarar..."
                                rows="2" />
                            <InputError :message="form.errors.description" />
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
                        <AlertDialogTitle>Yetkiyi silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingPermission?.label }}</strong> yetkisi kalıcı olarak silinecektir. Bu
                            işlem geri alınamaz.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>İptal</AlertDialogCancel>
                        <AlertDialogAction @click="confirmDelete">Sil</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </UsersLayout>
    </AppLayout>
</template>
