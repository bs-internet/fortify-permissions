<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Pencil, X, Check, Loader2, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, onUnmounted } from 'vue';
import { update } from '@/actions/App/Http/Controllers/Users/PermissionController';
import Heading from '@/components/app/common/Heading.vue';
import TableSkeleton from '@/components/app/common/TableSkeleton.vue';
import { usePermission } from '@/composables/usePermission';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import EmptyState from '@/components/ui/empty-state/EmptyState.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetFooter,
} from '@/components/ui/sheet';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as permissionRoute } from '@/routes/users/permissions';
import { type BreadcrumbItem, type Permission, type PaginationResponse } from '@/types';

const props = defineProps<{
    permissions: PaginationResponse<Permission>;
}>();

const { can } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Yetkiler', href: permissionRoute().url },
];

const isSheetOpen = ref(false);
const editingPermission = ref<Permission | null>(null);

const form = useForm({
    label: '',
    description: '',
});

useClearFormErrors(form);

function openEditSheet(permission: Permission) {
    editingPermission.value = permission;
    form.label = permission.label;
    form.description = permission.description ?? '';
    form.clearErrors();
    isSheetOpen.value = true;
}

function submitForm() {
    if (editingPermission.value) {
        form.put(update.url(editingPermission.value.id), {
            onSuccess: () => {
                isSheetOpen.value = false;
            },
        });
    }
}

function handlePageChange(page: number) {
    if (page > props.permissions.last_page || page < 1) return;

    router.visit(permissionRoute().url, {
        data: { page },
        preserveScroll: true,
    });
}

const tableLoading = ref(false);

const offStart = router.on('start', () => { tableLoading.value = true; });
const offFinish = router.on('finish', () => { tableLoading.value = false; });
onUnmounted(() => { offStart(); offFinish(); });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Yetkiler" />

        <UsersLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading variant="small" title="Yetkiler"
                        description="Sistem yetkilerinin görünüm bilgilerini buradan düzenleyebilirsiniz." />
                </div>

                <div v-if="tableLoading" class="mx-2">
                    <TableSkeleton :rows="10" :columns="3" />
                </div>
                <template v-else-if="permissions.data.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[350px]">Görünen Ad</TableHead>
                                    <TableHead>Açıklama</TableHead>
                                    <TableHead v-if="can('permission.update')" class="text-right w-[140px]">İşlemler
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="permission in permissions.data" :key="permission.id" class="group/row">
                                    <TableCell class="font-medium">
                                        {{ permission.label }}
                                    </TableCell>
                                    <TableCell class="text-muted-foreground text-sm">
                                        {{ permission.description ?? '-' }}
                                    </TableCell>
                                    <TableCell v-if="can('permission.update')" class="text-right">
                                        <Button variant="outline" size="sm" @click="openEditSheet(permission)">
                                            <Pencil class="mr-2 h-4 w-4" />
                                            Düzenle
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="permissions.total > permissions.per_page" class="flex items-center justify-between px-4 py-2">
                        <div class="text-sm text-muted-foreground">
                            Toplam <strong>{{ permissions.total }}</strong> kayıttan
                            <strong>{{ permissions.from }}-{{ permissions.to }}</strong> arası gösteriliyor.
                        </div>
                        <Pagination :total="permissions.total" :items-per-page="permissions.per_page"
                            :default-page="permissions.current_page" @update:page="handlePageChange">
                            <PaginationContent>
                                <PaginationPrevious class="cursor-pointer">
                                    <ChevronLeft class="h-4 w-4" />
                                </PaginationPrevious>
                                <template v-for="(item, index) in permissions.links" :key="index">
                                    <PaginationItem v-if="item.url && !isNaN(Number(item.label))">
                                        <Button size="icon" variant="ghost" class="h-9 w-9"
                                            :class="{ 'border': item.active }"
                                            @click="handlePageChange(Number(item.label))">
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
                    <EmptyState title="Yetki Bulunamadı"
                        description="Sistemde henüz hiç yetki tanımlanmamış." />
                </div>
            </div>

            <Sheet v-model:open="isSheetOpen">
                <SheetContent side="right" class="sm:max-w-[400px] p-0 flex flex-col h-full">
                    <SheetHeader class="p-6 border-b shrink-0">
                        <SheetTitle class="flex items-center gap-2 text-xl">
                            <Pencil class="h-5 w-5 text-primary" /> Yetkiyi Düzenle
                        </SheetTitle>
                        <SheetDescription>
                            Sistem genelindeki yetki etiketlerini ve açıklamalarını güncelleyin.
                        </SheetDescription>
                    </SheetHeader>

                    <form id="permissionForm" @submit.prevent="submitForm" class="flex-1 overflow-y-auto p-6 space-y-6">
                        <div class="space-y-2">
                            <Label for="label" class="text-sm font-bold">Görünen Ad</Label>
                            <Input id="label" v-model="form.label" placeholder="Yetki adını giriniz..." class="h-11" />
                            <InputError :message="form.errors.label" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description" class="text-sm font-bold">Açıklama</Label>
                            <Textarea id="description" v-model="form.description" rows="10"
                                placeholder="Bu yetkinin ne işe yaradığını detaylandırın..."
                                class="resize-none focus-visible:ring-1" />
                            <InputError :message="form.errors.description" />
                        </div>
                    </form>

                    <SheetFooter class="p-6 border-t bg-muted/10 shrink-0">
                        <div class="flex w-full flex-col gap-3">
                            <Button type="submit" form="permissionForm" class="w-full h-11" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <Check v-else class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Güncelleniyor...' : 'Değişiklikleri Kaydet' }}
                            </Button>
                            <Button type="button" variant="ghost" class="w-full h-11" @click="isSheetOpen = false">
                                <X class="mr-2 h-4 w-4" /> İptal
                            </Button>
                        </div>
                    </SheetFooter>
                </SheetContent>
            </Sheet>
        </UsersLayout>
    </AppLayout>
</template>
