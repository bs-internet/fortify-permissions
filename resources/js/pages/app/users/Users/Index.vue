<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Users/UserController';
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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
import { bulk as bulkRoute, index as userRoute } from '@/routes/users';
import { type BreadcrumbItem, type Language, type Permission, type Role } from '@/types';

type UserItem = {
    id: string;
    name: string;
    email: string;
    title: string | null;
    status: number;
    language_id: string | null;
    language: Language | null;
    roles: Role[];
    permissions: Permission[];
    created_at: string;
};

type PaginatedUsers = {
    data: UserItem[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
};

type Props = {
    users: PaginatedUsers;
    filters: { search?: string; status?: string };
    roles: Role[];
    permissions: Permission[];
    languages: Language[];
    statuses: Record<number, string>;
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Kayıtlı Kullanıcılar', href: userRoute().url },
];

// Search & filter
const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');
let debounceTimer: ReturnType<typeof setTimeout>;

watch(search, (value) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(userRoute().url, { search: value || undefined, status: statusFilter.value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

function onStatusChange(value: string | null) {
    const statusValue = value ?? '';
    statusFilter.value = statusValue;
    router.get(userRoute().url, { search: search.value || undefined, status: statusValue || undefined }, { preserveState: true, replace: true });
}

// Bulk Actions state
const selectedUsers = ref<string[]>([]);

const isAllSelected = computed(() => {
    return props.users.data.length > 0 && selectedUsers.value.length === props.users.data.length;
});

function toggleSelectAll(checked: boolean) {
    if (checked) {
        selectedUsers.value = props.users.data.map(u => u.id);
    } else {
        selectedUsers.value = [];
    }
}

function handleBulkAction(action: 'delete' | 'activate' | 'deactivate') {
    if (selectedUsers.value.length === 0) return;

    if (action === 'delete') {
        if (!confirm('Seçili kullanıcıları silmek istediğinize emin misiniz?')) return;
    }

    router.post(bulkRoute().url, {
        action: action,
        ids: selectedUsers.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedUsers.value = [];
        }
    });
}

// Dialog state
const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingUser = ref<UserItem | null>(null);
const deletingUser = ref<UserItem | null>(null);

const form = useForm({
    name: '',
    email: '',
    title: '',
    password: '',
    password_confirmation: '',
    status: 1,
    language_id: '',
    roles: [] as string[],
    permissions: [] as string[],
});

function openCreateDialog() {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    showFormDialog.value = true;
}

function openEditDialog(user: UserItem) {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.title = user.title ?? '';
    form.password = '';
    form.password_confirmation = '';
    form.status = user.status;
    form.language_id = user.language_id ?? '';
    form.roles = user.roles.map((r) => r.id);
    form.permissions = user.permissions.map((p) => p.id);
    form.clearErrors();
    showFormDialog.value = true;
}

function openDeleteDialog(user: UserItem) {
    deletingUser.value = user;
    showDeleteDialog.value = true;
}

function toggleRole(roleId: string) {
    const index = form.roles.indexOf(roleId);
    if (index > -1) {
        form.roles.splice(index, 1);
    } else {
        form.roles.push(roleId);
    }
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
    if (editingUser.value) {
        form.put(update.url(editingUser.value.id), {
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
    if (!deletingUser.value) return;

    form.delete(destroy.url(deletingUser.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingUser.value = null;
        },
    });
}

function exportData() {
    const params = new URLSearchParams();
    if (search.value) params.append('search', search.value);
    if (statusFilter.value) params.append('status', statusFilter.value);
    window.location.href = `/users/export?${params.toString()}`;
}

function getStatusLabel(status: number): string {
    return props.statuses[status] ?? String(status);
}

function getStatusBadgeVariant(status: number): 'default' | 'secondary' | 'destructive' | 'outline' {
    switch (status) {
        case 1:
            return 'default';
        case 2:
            return 'outline';
        default:
            return 'secondary';
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Kayıtlı Kullanıcılar" />

        <UsersLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <Heading variant="small" title="Kayıtlı Kullanıcılar"
                        description="Sistem üzerinde kayıtlı kullanıcılar." />

                    <div v-if="selectedUsers.length > 0" class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-muted-foreground mr-2">{{ selectedUsers.length }} seçili</span>
                        <Button variant="outline" size="sm" @click="handleBulkAction('activate')">Aktif Yap</Button>
                        <Button variant="outline" size="sm" @click="handleBulkAction('deactivate')">Pasif Yap</Button>
                        <Button variant="destructive" size="sm" @click="handleBulkAction('delete')">Sil</Button>
                    </div>
                    <div v-else class="flex gap-2">
                        <Button variant="outline" @click="exportData">
                            <span class="mr-2">📥</span>
                            Dışa Aktar
                        </Button>
                        <Button @click="openCreateDialog">Yeni Kullanıcı Ekle</Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col gap-4 sm:flex-row">
                    <Input v-model="search" placeholder="Ad, e-posta veya ünvan ara..." class="sm:max-w-xs" />
                    <Select :model-value="statusFilter" @update:model-value="(val: any) => onStatusChange(val)">
                        <SelectTrigger class="sm:w-48">
                            <SelectValue placeholder="Tüm Durumlar" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Tüm Durumlar</SelectItem>
                            <SelectItem v-for="(label, value) in statuses" :key="value" :value="String(value)">
                                {{ label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Table -->
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-12 text-center">
                                    <Checkbox :checked="isAllSelected" @update:checked="toggleSelectAll" />
                                </TableHead>
                                <TableHead>Ad</TableHead>
                                <TableHead>E-posta</TableHead>
                                <TableHead>Ünvan</TableHead>
                                <TableHead class="text-center">Durum</TableHead>
                                <TableHead>Roller</TableHead>
                                <TableHead>Doğrudan Yetkiler</TableHead>
                                <TableHead>Dil</TableHead>
                                <TableHead class="text-right">İşlemler</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="users.data.length === 0">
                                <TableCell :colspan="8" class="p-0">
                                    <EmptyState title="Kullanıcı Bulunamadı"
                                        description="Sistemde kayıtlı veya aradığınız kritere uygun kullanıcı bulunmuyor."
                                        actionLabel="Yeni Kullanıcı Ekle" @action="openCreateDialog" />
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="user in users.data" :key="user.id">
                                <TableCell class="w-12 text-center">
                                    <Checkbox :checked="selectedUsers.includes(user.id)" @update:checked="(val: boolean) => {
                                        if (val) selectedUsers.push(user.id);
                                        else selectedUsers.splice(selectedUsers.indexOf(user.id), 1);
                                    }" />
                                </TableCell>
                                <TableCell class="font-medium">{{ user.name }}</TableCell>
                                <TableCell>{{ user.email }}</TableCell>
                                <TableCell>{{ user.title ?? '-' }}</TableCell>
                                <TableCell class="text-center">
                                    <Badge :variant="getStatusBadgeVariant(user.status)">
                                        {{ getStatusLabel(user.status) }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="flex flex-wrap gap-1">
                                        <Badge v-for="role in user.roles" :key="role.id" variant="secondary">
                                            {{ role.label }}
                                        </Badge>
                                        <span v-if="user.roles.length === 0"
                                            class="text-sm text-muted-foreground">-</span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="flex flex-wrap gap-1">
                                        <Badge v-for="perm in user.permissions" :key="perm.id" variant="outline">
                                            {{ perm.label }}
                                        </Badge>
                                        <span v-if="user.permissions.length === 0"
                                            class="text-sm text-muted-foreground">-</span>
                                    </div>
                                </TableCell>
                                <TableCell>{{ user.language?.name ?? '-' }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="sm" @click="openEditDialog(user)"> Düzenle
                                        </Button>
                                        <Button variant="ghost" size="sm" class="text-destructive"
                                            @click="openDeleteDialog(user)">
                                            Sil
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination -->
                <div v-if="users.last_page > 1" class="flex justify-center gap-1">
                    <template v-for="link in users.links" :key="link.label">
                        <Button v-if="link.url" variant="outline" size="sm"
                            :class="{ 'bg-primary text-primary-foreground': link.active }"
                            @click="router.get(link.url, {}, { preserveState: true })" v-html="link.label" />
                        <Button v-else variant="outline" size="sm" disabled v-html="link.label" />
                    </template>
                </div>
            </div>

            <!-- Create/Edit Dialog -->
            <Dialog v-model:open="showFormDialog">
                <DialogContent class="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>{{ editingUser ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı Ekle' }}</DialogTitle>
                        <DialogDescription>
                            {{
                                editingUser
                                    ? 'Kullanıcı bilgilerini güncelleyin.'
                                    : 'Sisteme yeni bir kullanıcı ekleyin.'
                            }}
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Ad Soyad</Label>
                                <Input id="name" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email">E-posta</Label>
                                <Input id="email" v-model="form.email" type="email" />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="title">Ünvan</Label>
                                <Input id="title" v-model="form.title" />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Durum</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Durum seçin" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="(label, value) in statuses" :key="value"
                                            :value="Number(value)">
                                            {{ label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.status" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="password">
                                    Şifre
                                    <span v-if="editingUser" class="text-xs text-muted-foreground">(boş bırakılırsa
                                        değişmez)</span>
                                </Label>
                                <Input id="password" v-model="form.password" type="password" />
                                <InputError :message="form.errors.password" />
                            </div>

                            <div class="space-y-2">
                                <Label for="password_confirmation">Şifre Tekrar</Label>
                                <Input id="password_confirmation" v-model="form.password_confirmation"
                                    type="password" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="language_id">Dil</Label>
                            <Select v-model="form.language_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Dil seçin (opsiyonel)" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">Seçilmemiş</SelectItem>
                                    <SelectItem v-for="lang in languages" :key="lang.id" :value="lang.id">
                                        {{ lang.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.language_id" />
                        </div>

                        <!-- Roles -->
                        <div v-if="roles.length > 0" class="space-y-2">
                            <Label>Roller</Label>
                            <div class="max-h-36 space-y-2 overflow-y-auto rounded-md border p-3">
                                <div v-for="role in roles" :key="role.id" class="flex items-center gap-2">
                                    <Checkbox :id="`role-${role.id}`" :checked="form.roles.includes(role.id)"
                                        @update:checked="toggleRole(role.id)" />
                                    <Label :for="`role-${role.id}`" class="cursor-pointer font-normal">
                                        {{ role.label }}
                                        <span v-if="role.description" class="text-xs text-muted-foreground">
                                            - {{ role.description }}
                                        </span>
                                    </Label>
                                </div>
                            </div>
                            <InputError :message="form.errors.roles" />
                        </div>

                        <!-- Direct Permissions -->
                        <div v-if="permissions.length > 0" class="space-y-2">
                            <Label>Doğrudan Yetkiler</Label>
                            <p class="text-xs text-muted-foreground">Rol üzerinden gelen yetkilere ek olarak doğrudan
                                atanan
                                yetkiler.</p>
                            <div class="max-h-36 space-y-2 overflow-y-auto rounded-md border p-3">
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
                        <AlertDialogTitle>Kullanıcıyı silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingUser?.name }} ({{ deletingUser?.email }})</strong> kullanıcısı
                            silinecektir.
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
