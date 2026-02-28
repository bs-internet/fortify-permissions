<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    store,
    update,
    destroy,
} from '@/actions/App/Http/Controllers/Users/RoleController';
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as roleRoute } from '@/routes/users/roles';
import { type BreadcrumbItem, type Permission, type Role } from '@/types';

type Props = {
    roles: Role[];
    permissions: Record<string, Permission[]>;
};

const props = defineProps<Props>();

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

function togglePermission(permissionId: string) {
    const index = form.permissions.indexOf(permissionId);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permissionId);
    }
}

// Bir grubun tamamını seçmek veya kaldırmak için yardımcı fonksiyon
function toggleGroup(groupPermissions: Permission[]) {
    const groupIds = groupPermissions.map((p) => p.id);
    const allSelected = groupIds.every((id) => form.permissions.includes(id));

    if (allSelected) {
        form.permissions = form.permissions.filter(
            (id) => !groupIds.includes(id),
        );
    } else {
        const newIds = groupIds.filter((id) => !form.permissions.includes(id));
        form.permissions.push(...newIds);
    }
}

function submitForm() {
    if (editingRole.value) {
        form.put(update.url(editingRole.value.id), {
            onSuccess: () => (showFormDialog.value = false),
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
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <Heading
                        variant="small"
                        title="Roller"
                        description="Sistem kullanıcılarına atanabilecek roller ve yetki setleri."
                    />
                    <Button @click="openCreateDialog">Yeni Rol Ekle</Button>
                </div>

                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Teknik Ad</TableHead>
                                <TableHead>Görünen Ad</TableHead>
                                <TableHead>Yetkiler</TableHead>
                                <TableHead class="text-right"
                                    >İşlemler</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="role in roles" :key="role.id">
                                <TableCell
                                    class="font-mono text-xs text-muted-foreground"
                                    >{{ role.name }}</TableCell
                                >
                                <TableCell>
                                    <div class="font-medium">
                                        {{ role.label }}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ role.description ?? '-' }}
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div
                                        class="flex max-w-[400px] flex-wrap gap-1"
                                    >
                                        <Badge
                                            v-for="permission in role.permissions.slice(
                                                0,
                                                5,
                                            )"
                                            :key="permission.id"
                                            variant="outline"
                                            class="font-normal"
                                        >
                                            {{ permission.label }}
                                        </Badge>
                                        <Badge
                                            v-if="role.permissions.length > 5"
                                            variant="secondary"
                                        >
                                            +{{
                                                role.permissions.length - 5
                                            }}
                                            daha...
                                        </Badge>
                                        <span
                                            v-if="role.permissions.length === 0"
                                            class="text-muted-foreground"
                                            >-</span
                                        >
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="openEditDialog(role)"
                                            >Düzenle</Button
                                        >
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="text-destructive"
                                            @click="
                                                showDeleteDialog = true;
                                                deletingRole = role;
                                            "
                                            >Sil</Button
                                        >
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <Dialog v-model:open="showFormDialog">
                <DialogContent
                    class="flex max-h-[90vh] flex-col p-0 sm:max-w-2xl"
                >
                    <DialogHeader class="p-6 pb-0">
                        <DialogTitle>{{
                            editingRole ? 'Rol Düzenle' : 'Yeni Rol Ekle'
                        }}</DialogTitle>
                        <DialogDescription
                            >Rol bilgilerini ve yetkilerini bu ekrandan
                            yönetebilirsiniz.</DialogDescription
                        >
                    </DialogHeader>

                    <form
                        @submit.prevent="submitForm"
                        class="flex flex-col overflow-hidden"
                    >
                        <div class="flex-1 space-y-6 overflow-y-auto p-6">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="name">Teknik Ad (slug)</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        placeholder="admin"
                                        :disabled="
                                            editingRole?.name === 'Admin'
                                        "
                                    />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="label">Görünen Ad</Label>
                                    <Input
                                        id="label"
                                        v-model="form.label"
                                        placeholder="Yönetici"
                                    />
                                    <InputError :message="form.errors.label" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Açıklama</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="2"
                                />
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <Label
                                        class="text-base font-semibold text-primary"
                                        >Modül Bazlı Yetkiler</Label
                                    >
                                    <span class="text-xs text-muted-foreground"
                                        >{{ form.permissions.length }} yetki
                                        seçildi</span
                                    >
                                </div>

                                <div class="grid gap-6 rounded-lg border p-4">
                                    <div
                                        v-for="(
                                            groupPerms, groupName
                                        ) in permissions"
                                        :key="groupName"
                                        class="space-y-3"
                                    >
                                        <div
                                            class="flex items-center gap-2 border-b pb-2"
                                        >
                                            <Checkbox
                                                :id="`group-${groupName}`"
                                                :checked="
                                                    groupPerms.every((p) =>
                                                        form.permissions.includes(
                                                            p.id,
                                                        ),
                                                    )
                                                "
                                                @update:checked="
                                                    toggleGroup(groupPerms)
                                                "
                                            />
                                            <Label :for="`group-${groupName}`" class="uppercase font-bold text-xs tracking-wider cursor-pointer">
                                                {{ groupName }} YÖNETİMİ
                                            </Label>
                                        </div>

                                        <div
                                            class="grid grid-cols-1 gap-3 pl-6 sm:grid-cols-2"
                                        >
                                            <div
                                                v-for="permission in groupPerms"
                                                :key="permission.id"
                                                class="flex items-start gap-2"
                                            >
                                                <Checkbox
                                                    :id="`perm-${permission.id}`"
                                                    :checked="
                                                        form.permissions.includes(
                                                            permission.id,
                                                        )
                                                    "
                                                    @update:checked="
                                                        togglePermission(
                                                            permission.id,
                                                        )
                                                    "
                                                />
                                                <div
                                                    class="grid gap-1.5 leading-none"
                                                >
                                                    <Label
                                                        :for="`perm-${permission.id}`"
                                                        class="cursor-pointer text-sm leading-none font-medium"
                                                    >
                                                        {{ permission.label }}
                                                    </Label>
                                                    <p
                                                        v-if="
                                                            permission.description
                                                        "
                                                        class="text-xs leading-snug text-muted-foreground"
                                                    >
                                                        {{
                                                            permission.description
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <DialogFooter class="border-t bg-muted/20 p-6 pt-2">
                            <Button
                                type="button"
                                variant="outline"
                                @click="showFormDialog = false"
                                >İptal</Button
                            >
                            <Button type="submit" :disabled="form.processing">
                                {{
                                    form.processing
                                        ? 'Kaydediliyor...'
                                        : 'Kaydet'
                                }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle
                            >Rolü silmek istediğinize emin
                            misiniz?</AlertDialogTitle
                        >
                        <AlertDialogDescription>
                            <strong>{{ deletingRole?.label }}</strong> rolü
                            silinecektir. Bu işlem geri alınamaz.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>İptal</AlertDialogCancel>
                        <AlertDialogAction
                            @click="confirmDelete"
                            class="bg-destructive text-destructive-foreground"
                            >Sil</AlertDialogAction
                        >
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </UsersLayout>
    </AppLayout>
</template>
