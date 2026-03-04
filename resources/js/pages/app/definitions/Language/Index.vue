<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2, X, Check, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';
import { store, update, destroy } from '@/actions/App/Http/Controllers/Definitions/LanguageController';
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
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as languageRoute } from '@/routes/settings/definitions/languages';
import { type BreadcrumbItem, type Language, type PaginationResponse } from '@/types';

const props = defineProps<{
    languages: PaginationResponse<Language>;
    defaultLanguageId: string | null;
}>();

const { can } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Diller', href: languageRoute().url },
];

const isSheetOpen = ref(false);
const showDeleteDialog = ref(false);
const editingLanguage = ref<Language | null>(null);

const form = useForm({
    code: '',
    name: '',
    native_name: '',
    is_active: true,
    sort_order: 0,
});

useClearFormErrors(form);

function openCreateSheet() {
    editingLanguage.value = null;
    form.reset();
    form.clearErrors();
    isSheetOpen.value = true;
}

function openEditSheet(language: Language) {
    editingLanguage.value = language;
    form.code = language.code;
    form.name = language.name;
    form.native_name = language.native_name;
    form.is_active = language.is_active;
    form.sort_order = language.sort_order;
    form.clearErrors();
    isSheetOpen.value = true;
}

function submitForm() {
    if (editingLanguage.value) {
        form.put(update.url(editingLanguage.value.id), {
            onSuccess: () => { isSheetOpen.value = false; },
        });
    } else {
        form.post(store.url(), {
            onSuccess: () => { isSheetOpen.value = false; form.reset(); },
        });
    }
}

function openDeleteDialog() {
    showDeleteDialog.value = true;
}

function confirmDelete() {
    if (!editingLanguage.value) return;
    form.delete(destroy.url(editingLanguage.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            isSheetOpen.value = false;
            editingLanguage.value = null;
        },
    });
}

function handlePageChange(page: number) {
    router.visit(languageRoute().url, {
        data: { page },
        preserveScroll: true,
        preserveState: true,
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Diller" />

        <DefinitionsLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center px-2">
                    <Heading variant="small" title="Diller" description="Sistem üzerinde kullanılan diller." />
                    <div v-if="can('language.create')">
                        <Button size="sm" class="h-9" @click="openCreateSheet">
                            <Plus class="mr-2 h-4 w-4" />
                            Yeni Dil Ekle
                        </Button>
                    </div>
                </div>

                <template v-if="languages.data.length > 0">
                    <div class="rounded-md border border-border bg-card shadow-none mx-2">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Kod</TableHead>
                                    <TableHead>Ad</TableHead>
                                    <TableHead>Yerel Ad</TableHead>
                                    <TableHead class="text-center">Varsayılan</TableHead>
                                    <TableHead class="text-center">Durum</TableHead>
                                    <TableHead class="text-center">Sıra</TableHead>
                                    <TableHead v-if="can('language.update')" class="text-right w-[100px]">İşlemler
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="language in languages.data" :key="language.id"
                                    class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">{{ language.code }}</TableCell>
                                    <TableCell>{{ language.name }}</TableCell>
                                    <TableCell>{{ language.native_name }}</TableCell>
                                    <TableCell class="text-center">
                                        <Badge v-if="language.id === defaultLanguageId" variant="default">Varsayılan
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="language.is_active ? 'default' : 'secondary'">
                                            {{ language.is_active ? 'Aktif' : 'Pasif' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">{{ language.sort_order }}</TableCell>
                                    <TableCell v-if="can('language.update')" class="text-right">
                                        <Button variant="outline" size="sm" class="h-8"
                                            @click="openEditSheet(language)">
                                            <Pencil class="mr-2 h-3.5 w-3.5" />
                                            Düzenle
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="languages.last_page > 1"
                        class="mt-4 flex flex-col items-center justify-between gap-4 sm:flex-row px-2">
                        <div class="text-sm text-muted-foreground font-medium">
                            Toplam {{ languages.total }} kayıttan {{ languages.from }}-{{ languages.to }} arası
                            gösteriliyor
                        </div>
                        <Pagination :total="languages.total" :items-per-page="languages.per_page"
                            :default-page="languages.current_page" @update:page="handlePageChange">
                            <PaginationContent>
                                <PaginationPrevious class="cursor-pointer">
                                    <ChevronLeft class="h-4 w-4" />
                                </PaginationPrevious>
                                <template v-for="(item, index) in languages.links" :key="index">
                                    <PaginationItem v-if="item.url && !isNaN(Number(item.label))" :value="index">
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
                    <EmptyState title="Dil Bulunamadı" description="Sistemde henüz hiç dil oluşturulmamış."
                        :action-label="can('language.create') ? 'Yeni Dil Ekle' : undefined"
                        @action="can('language.create') && openCreateSheet()" />
                </div>
            </div>

            <Sheet v-model:open="isSheetOpen">
                <SheetContent side="right" class="sm:max-w-[400px] p-0 flex flex-col h-full">
                    <SheetHeader class="p-6 border-b shrink-0">
                        <SheetTitle class="flex items-center gap-2 text-xl">
                            <Pencil v-if="editingLanguage" class="h-5 w-5 text-primary" />
                            <Plus v-else class="h-5 w-5 text-primary" />
                            {{ editingLanguage ? 'Dil Düzenle' : 'Yeni Dil Ekle' }}
                        </SheetTitle>
                        <SheetDescription>
                            {{ editingLanguage ? 'Dil bilgilerini güncelleyin.' : 'Sisteme yeni bir dil ekleyin.' }}
                        </SheetDescription>
                    </SheetHeader>

                    <form id="languageForm" class="flex-1 overflow-y-auto p-6 space-y-6" @submit.prevent="submitForm">
                        <div class="space-y-2">
                            <Label for="code" class="text-sm font-bold">Dil Kodu</Label>
                            <Input id="code" v-model="form.code" placeholder="tr" class="h-11" />
                            <InputError :message="form.errors.code" />
                        </div>
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-bold">Ad</Label>
                            <Input id="name" v-model="form.name" placeholder="Türkçe" class="h-11" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="native_name" class="text-sm font-bold">Yerel Ad</Label>
                            <Input id="native_name" v-model="form.native_name" placeholder="Türkçe" class="h-11" />
                            <InputError :message="form.errors.native_name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="sort_order" class="text-sm font-bold">Sıralama</Label>
                            <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0"
                                class="h-11" />
                            <InputError :message="form.errors.sort_order" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Switch id="is_active" :checked="form.is_active"
                                @update:checked="form.is_active = $event" />
                            <Label for="is_active">Aktif</Label>
                        </div>
                    </form>

                    <SheetFooter class="p-6 border-t bg-muted/10 shrink-0">
                        <div class="flex w-full flex-col gap-3">
                            <Button type="submit" form="languageForm" class="w-full h-11" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <Check v-else class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Kaydediliyor...' : (editingLanguage ? 'Değişiklikleri Kaydet' :
                                    'Dil Ekle') }}
                            </Button>
                            <Button type="button" variant="ghost" class="w-full h-11" @click="isSheetOpen = false">
                                <X class="mr-2 h-4 w-4" /> İptal
                            </Button>
                            <Button v-if="editingLanguage && can('language.delete') && languages.data.length > 1"
                                type="button" variant="outline"
                                class="w-full h-11 text-destructive border-destructive/30 hover:bg-destructive/5"
                                @click="openDeleteDialog">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Dili Sil
                            </Button>
                        </div>
                    </SheetFooter>
                </SheetContent>
            </Sheet>

            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Dili silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ editingLanguage?.name }}</strong> dili kalıcı olarak silinecektir. Bu işlem geri
                            alınamaz.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>İptal</AlertDialogCancel>
                        <AlertDialogAction class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                            @click="confirmDelete">
                            Evet, Sil
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DefinitionsLayout>
    </AppLayout>
</template>
