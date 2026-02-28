<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { update } from '@/actions/App/Http/Controllers/Users/PermissionController';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
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
const editingPermission = ref<Permission | null>(null);

const form = useForm({
    name: '',
    label: '',
    description: '',
});

function openEditDialog(permission: Permission) {
    editingPermission.value = permission;
    form.name = permission.name;
    form.label = permission.label;
    form.description = permission.description ?? '';
    form.clearErrors();
    showFormDialog.value = true;
}

function submitForm() {
    if (editingPermission.value) {
        form.put(update.url(editingPermission.value.id), {
            onSuccess: () => {
                showFormDialog.value = false;
            },
        });
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Yetkiler" />

        <UsersLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <Heading
                        variant="small"
                        title="Yetkiler"
                        description="Sistem yetkileri Enum üzerinden yönetilir. Buradan sadece görünüm bilgilerini düzenleyebilirsiniz."
                    />
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
                                    <EmptyState
                                        title="Yetki Bulunamadı"
                                        description="Sistemde henüz hiç yetki tanımlanmamış. Lütfen PermissionSeeder'ı çalıştırın."
                                    />
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="permission in permissions" :key="permission.id">
                                <TableCell class="font-mono text-xs text-muted-foreground">{{ permission.name }}</TableCell>
                                <TableCell class="font-medium">{{ permission.label }}</TableCell>
                                <TableCell class="text-muted-foreground">{{ permission.description ?? '-' }}</TableCell>
                                <TableCell class="text-right">
                                    <Button variant="outline" size="sm" @click="openEditDialog(permission)">
                                        Düzenle
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <Dialog v-model:open="showFormDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Yetkiyi Düzenle</DialogTitle>
                        <DialogDescription>
                            İznin kullanıcı arayüzünde nasıl görüneceğini belirleyin.
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="space-y-2">
                            <Label for="name">Teknik Ad (Değiştirilemez)</Label>
                            <Input id="name" v-model="form.name" disabled class="bg-muted" />
                            <p class="text-[0.7rem] text-muted-foreground">Teknik adlar sadece Enum dosyası üzerinden güncellenebilir.</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="label">Görünen Ad</Label>
                            <Input id="label" v-model="form.label" placeholder="Örn: Kullanıcıları Yönet" />
                            <InputError :message="form.errors.label" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Açıklama</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Bu yetkinin kapsamını açıklayın..."
                                rows="3"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="ghost" @click="showFormDialog = false">İptal</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Güncelleniyor...' : 'Değişiklikleri Kaydet' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </UsersLayout>
    </AppLayout>
</template>
