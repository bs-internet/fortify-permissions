<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Users/RoleController';
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
import { Checkbox } from '@/components/ui/checkbox';
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
import { index as roleRoute } from '@/routes/users/roles';
import { type BreadcrumbItem, type Permission, type Role } from '@/types';

type Props = {
    roles: Role[];
    permissions: Permission[];
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Roller', href: roleRoute().url },
];

const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingRole = ref<Role | null>(null);
const deletingRole = ref<Role | null>(null);

const form = useForm({
    name: '',
    label: '',
    description: '',
    permissions: [] as string[],
});

function openCreateDialog() {
    editingRole.value = null;
    form.reset();
    form.clearErrors();
    showFormDialog.value = true;
}

function openEditDialog(role: Role) {
    editingRole.value = role;
    form.name = role.name;
    form.label = role.label;
    form.description = role.description ?? '';
    form.permissions = role.permissions.map((p) => p.id);
    form.clearErrors();
    showFormDialog.value = true;
}

function openDeleteDialog(role: Role) {
    deletingRole.value = role;
    showDeleteDialog.value = true;
}

function togglePermission(permissionId: string) {
    const index = form.permissions.indexOf(permissionId);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permissionId);
    }
}

function submitForm() {
    if (editingRole.value) {
        form.put(update.url(editingRole.value.id), {
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
    if (!deletingRole.value) return;

    form.delete(destroy.url(deletingRole.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingRole.value = null;
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Roller" />

        <UsersLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <Heading variant="small" title="Roller" description="Sistem kullanıcılarına atanabilecek roller." />

                    <Button @click="openCreateDialog">Yeni Rol Ekle</Button>
                </div>

                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Teknik Ad</TableHead>
                                <TableHead>Görünen Ad</TableHead>
                                <TableHead>Açıklama</TableHead>
                                <TableHead>Yetkiler</TableHead>
                                <TableHead class="text-right">İşlemler</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="roles.length === 0">
                                <TableCell :colspan="5" class="p-0">
                                    <EmptyState title="Rol Bulunamadı"
                                        description="Sistemde henüz hiç rol oluşturulmamış." actionLabel="Yeni Rol Ekle"
                                        @action="openCreateDialog" />
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="role in roles" :key="role.id">
                                <TableCell class="font-mono text-sm">{{ role.name }}</TableCell>
                                <TableCell class="font-medium">{{ role.label }}</TableCell>
                                <TableCell class="text-muted-foreground">{{ role.description ?? '-' }}</TableCell>
                                <TableCell>
                                    <div class="flex flex-wrap gap-1">
                                        <Badge v-for="permission in role.permissions" :key="permission.id"
                                            variant="secondary">
                                            {{ permission.label }}
                                        </Badge>
                                        <span v-if="role.permissions.length === 0"
                                            class="text-sm text-muted-foreground">-</span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="sm" @click="openEditDialog(role)"> Düzenle
                                        </Button>
                                        <Button variant="ghost" size="sm" class="text-destructive"
                                            @click="openDeleteDialog(role)">
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
                <DialogContent class="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>{{ editingRole ? 'Rol Düzenle' : 'Yeni Rol Ekle' }}</DialogTitle>
                        <DialogDescription>
                            {{
                                editingRole
                                    ? 'Rol bilgilerini ve yetkilerini güncelleyin.'
                                    : 'Sisteme yeni bir rol ekleyin.'
                            }}
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Teknik Ad (slug)</Label>
                                <Input id="name" v-model="form.name" placeholder="admin" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="label">Görünen Ad</Label>
                                <Input id="label" v-model="form.label" placeholder="Yönetici" />
                                <InputError :message="form.errors.label" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Açıklama</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Bu rolün açıklaması..."
                                rows="2" />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div v-if="permissions.length > 0" class="space-y-2">
                            <Label>Yetkiler</Label>
                            <div class="max-h-48 space-y-2 overflow-y-auto rounded-md border p-3">
                                <div v-for="permission in permissions" :key="permission.id"
                                    class="flex items-center gap-2">
                                    <Checkbox :id="`perm-${permission.id}`"
                                        :checked="form.permissions.includes(permission.id)"
                                        @update:checked="togglePermission(permission.id)" />
                                    <Label :for="`perm-${permission.id}`" class="cursor-pointer font-normal">
                                        {{ permission.label }}
                                        <span v-if="permission.description" class="text-xs text-muted-foreground">
                                            - {{ permission.description }}
                                        </span>
                                    </Label>
                                </div>
                            </div>
                            <InputError :message="form.errors.permissions" />
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
                        <AlertDialogTitle>Rolü silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingRole?.label }}</strong> rolü kalıcı olarak silinecektir. Bu işlem geri
                            alınamaz.
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
