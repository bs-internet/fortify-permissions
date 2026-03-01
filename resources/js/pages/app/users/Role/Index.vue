<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, ChevronLeft, ChevronRight, Shield } from 'lucide-vue-next';
import Heading from '@/components/app/common/Heading.vue';
import { Button } from '@/components/ui/button';
import EmptyState from '@/components/ui/empty-state/EmptyState.vue';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
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
import { index as roleRoute, create as createRoute, edit as editRoute } from '@/routes/users/roles';
import { type BreadcrumbItem, type Role, type PaginationResponse } from '@/types';

defineProps<{
    roles: PaginationResponse<Role>;
}>();

const { can } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Roller', href: roleRoute().url },
];

function handlePageChange(page: number) {
    router.visit(roleRoute().url, {
        data: { page },
        preserveScroll: true
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Roller" />
        <UsersLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading
                        variant="small"
                        title="Rol Yönetimi"
                        description="Sistem rollerini listeleyin ve yönetin."
                    />
                    <Link v-if="roles.data.length > 0 && can('role.create')" :href="createRoute().url">
                        <Button size="sm" class="h-9">
                            <Plus class="mr-2 h-4 w-4" />
                            Yeni Rol Ekle
                        </Button>
                    </Link>
                </div>

                <template v-if="roles.data.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[250px]">Rol Adı</TableHead>
                                    <TableHead>Açıklama</TableHead>
                                    <TableHead v-if="can('role.update')" class="text-right w-[120px]">İşlemler</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="role in roles.data" :key="role.id" class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">
                                        <div class="flex items-center gap-2">
                                            <Shield class="h-4 w-4 text-muted-foreground" />
                                            {{ role.label }}
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-muted-foreground italic">
                                        {{ role.description || 'Açıklama belirtilmemiş.' }}
                                    </TableCell>
                                    <TableCell v-if="can('role.update')" class="text-right">
                                        <Link :href="editRoute(role).url">
                                            <Button variant="outline" size="sm" class="h-8">
                                                <Pencil class="mr-2 h-3.5 w-3.5" />
                                                Düzenle
                                            </Button>
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="roles.total > roles.per_page" class="flex items-center justify-between px-4 py-2">
                        <div class="text-sm text-muted-foreground">
                            Toplam <strong>{{ roles.total }}</strong> kayıttan <strong>{{ roles.from }}-{{ roles.to }}</strong> arası gösteriliyor.
                        </div>
                        <Pagination
                            :total="roles.total"
                            :items-per-page="roles.per_page"
                            :default-page="roles.current_page"
                            @update:page="handlePageChange"
                        >
                            <PaginationContent>
                                <PaginationPrevious class="cursor-pointer">
                                    <ChevronLeft class="h-4 w-4" />
                                </PaginationPrevious>
                                <template v-for="(item, index) in roles.links" :key="index">
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
                        title="Rol Bulunamadı"
                        description="Henüz bir rol tanımlanmamış veya arama kriterlerine uygun sonuç yok."
                        :icon="Shield"
                        :action-label="can('role.create') ? 'Yeni Rol Ekle' : undefined"
                        @action="can('role.create') && router.visit(createRoute().url)"
                    />
                </div>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
