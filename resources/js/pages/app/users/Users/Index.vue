<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, ChevronLeft, ChevronRight, Users, CheckCircle2, AlertCircle } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';
import Heading from '@/components/app/common/Heading.vue';
import TableSkeleton from '@/components/app/common/TableSkeleton.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import EmptyState from '@/components/ui/empty-state/EmptyState.vue';
import { Input } from '@/components/ui/input';
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { edit as profileEdit } from '@/routes/profile';
import { bulk as bulkRoute, index as userRoute } from '@/routes/users';
import { type BreadcrumbItem, type Role } from '@/types';

type UserItem = {
    id: string;
    name: string;
    email: string;
    email_verified_at: string | null;
    title: string | null;
    status: number;
    last_login_at: string | null;
    roles: Role[];
};

type PaginatedUsers = {
    data: UserItem[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
};

type Props = {
    users: PaginatedUsers;
    filters: { search?: string; status?: string; role?: string };
    statuses: Record<number, string>;
    roles: Role[];
};

const props = defineProps<Props>();

const { can, user: authUser } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Kayıtlı Kullanıcılar', href: userRoute().url },
];

// Search & filter
const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status || 'all');
const roleFilter = ref(props.filters.role || 'all');
let debounceTimer: ReturnType<typeof setTimeout>;

function getFilterParams() {
    return {
        search: search.value || undefined,
        status: statusFilter.value === 'all' ? undefined : statusFilter.value,
        role: roleFilter.value === 'all' ? undefined : roleFilter.value,
    };
}

watch(search, (value) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(userRoute().url, getFilterParams(), { preserveState: true, replace: true });
    }, 300);
});

function onStatusChange(value: string | null) {
    statusFilter.value = value ?? 'all';
    router.get(userRoute().url, getFilterParams(), { preserveState: true, replace: true });
}

function onRoleChange(value: string | null) {
    roleFilter.value = value ?? 'all';
    router.get(userRoute().url, getFilterParams(), { preserveState: true, replace: true });
}

// Bulk Actions
const selectedUsers = ref<string[]>([]);

const isAllSelected = computed(() => {
    return props.users.data.length > 0 && selectedUsers.value.length === props.users.data.length;
});

function toggleSelectAll(checked: boolean) {
    if (checked) {
        selectedUsers.value = props.users.data.map((u) => u.id);
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
        ids: selectedUsers.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedUsers.value = [];
        },
    });
}

function handlePageChange(page: number) {
    router.visit(userRoute().url, {
        data: { page, ...getFilterParams() },
        preserveScroll: true,
    });
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

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('tr-TR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    }).format(new Date(date));
}

const tableLoading = ref(false);

router.on('start', () => { tableLoading.value = true; });
router.on('finish', () => { tableLoading.value = false; });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Kayıtlı Kullanıcılar" />

        <UsersLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading
                        variant="small"
                        title="Kayıtlı Kullanıcılar"
                        description="Sistem üzerinde kayıtlı kullanıcılar."
                    />

                    <div v-if="selectedUsers.length > 0" class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-muted-foreground mr-2">{{ selectedUsers.length }} seçili</span>
                        <Button variant="outline" size="sm" @click="handleBulkAction('activate')">Aktif Yap</Button>
                        <Button variant="outline" size="sm" @click="handleBulkAction('deactivate')">Pasif Yap</Button>
                        <Button variant="destructive" size="sm" @click="handleBulkAction('delete')">Sil</Button>
                    </div>
                    <div v-else-if="can('user.create')">
                        <Link :href="`${userRoute().url}/create`">
                            <Button size="sm" class="h-9">
                                <Plus class="mr-2 h-4 w-4" />
                                Yeni Kullanıcı Ekle
                            </Button>
                        </Link>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col gap-4 sm:flex-row px-2">
                    <Input v-model="search" placeholder="Ad, e-posta veya ünvan ara..." class="sm:max-w-xs" />
                    <Select :model-value="statusFilter" @update:model-value="(val: any) => onStatusChange(val)">
                        <SelectTrigger class="sm:w-48">
                            <SelectValue placeholder="Tüm Durumlar" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Tüm Durumlar</SelectItem>
                            <SelectItem v-for="(label, value) in statuses" :key="value" :value="String(value)">
                                {{ label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-if="roles.length > 0" :model-value="roleFilter" @update:model-value="(val: any) => onRoleChange(val)">
                        <SelectTrigger class="sm:w-48">
                            <SelectValue placeholder="Tüm Roller" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Tüm Roller</SelectItem>
                            <SelectItem v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Table -->
                <div v-if="tableLoading" class="mx-2">
                    <TableSkeleton :rows="5" :columns="8" />
                </div>
                <template v-else-if="users.data.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-12 text-center">
                                        <Checkbox :checked="isAllSelected" @update:checked="toggleSelectAll" />
                                    </TableHead>
                                    <TableHead class="w-24 text-center">Durum</TableHead>
                                    <TableHead>Ad</TableHead>
                                    <TableHead>E-posta</TableHead>
                                    <TableHead>Ünvan</TableHead>
                                    <TableHead>Roller</TableHead>
                                    <TableHead>Son Giriş</TableHead>
                                    <TableHead v-if="can('user.update')" class="text-right w-[120px]">İşlemler</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="user in users.data" :key="user.id" class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="w-12 text-center">
                                        <Checkbox
                                            :checked="selectedUsers.includes(user.id)"
                                            @update:checked="(val: boolean) => {
                                                if (val) selectedUsers.push(user.id);
                                                else selectedUsers.splice(selectedUsers.indexOf(user.id), 1);
                                            }"
                                        />
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="getStatusBadgeVariant(user.status)">
                                            {{ getStatusLabel(user.status) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="font-medium">{{ user.name }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center gap-1.5">
                                            {{ user.email }}
                                            <CheckCircle2 v-if="user.email_verified_at" class="h-3.5 w-3.5 text-green-500 shrink-0" />
                                            <AlertCircle v-else class="h-3.5 w-3.5 text-amber-500 shrink-0" />
                                        </div>
                                    </TableCell>
                                    <TableCell>{{ user.title ?? '-' }}</TableCell>
                                    <TableCell>
                                        <div class="flex flex-wrap gap-1">
                                            <Badge v-for="role in user.roles" :key="role.id" variant="secondary">
                                                {{ role.label }}
                                            </Badge>
                                            <span v-if="user.roles.length === 0" class="text-sm text-muted-foreground">-</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-sm text-muted-foreground">
                                        {{ user.last_login_at ? formatDate(user.last_login_at) : 'Hiç giriş yapmadı' }}
                                    </TableCell>
                                    <TableCell v-if="can('user.update')" class="text-right">
                                        <Link :href="user.id === authUser?.id ? profileEdit().url : `${userRoute().url}/${user.id}/edit`">
                                            <Button variant="outline" size="sm" class="h-8">
                                                <Pencil class="mr-2 h-3.5 w-3.5" />
                                                {{ user.id === authUser?.id ? 'Profil' : 'Düzenle' }}
                                            </Button>
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="users.last_page > 1" class="flex items-center justify-between px-4 py-2">
                        <div class="text-sm text-muted-foreground">
                            Toplam <strong>{{ users.total }}</strong> kayıttan <strong>{{ users.from }}-{{ users.to }}</strong> arası gösteriliyor.
                        </div>
                        <Pagination
                            :total="users.total"
                            :items-per-page="users.per_page"
                            :default-page="users.current_page"
                            @update:page="handlePageChange"
                        >
                            <PaginationContent>
                                <PaginationPrevious class="cursor-pointer">
                                    <ChevronLeft class="h-4 w-4" />
                                </PaginationPrevious>
                                <template v-for="(item, index) in users.links" :key="index">
                                    <PaginationItem v-if="item.url && !isNaN(Number(item.label))">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="h-9 w-9"
                                            :class="{ 'border': item.active }"
                                            @click="handlePageChange(Number(item.label))"
                                        >
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
                    <EmptyState
                        title="Kullanıcı Bulunamadı"
                        description="Sistemde kayıtlı veya aradığınız kritere uygun kullanıcı bulunmuyor."
                        :icon="Users"
                        :action-label="can('user.create') ? 'Yeni Kullanıcı Ekle' : undefined"
                        @action="can('user.create') && router.visit(`${userRoute().url}/create`)"
                    />
                </div>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
