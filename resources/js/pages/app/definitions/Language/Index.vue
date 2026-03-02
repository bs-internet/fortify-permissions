<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Loader2 } from 'lucide-vue-next';
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
import { index as languageRoute } from '@/routes/settings/definitions/languages';
import { type BreadcrumbItem, type Language } from '@/types';

type Props = {
    languages: Language[];
};

defineProps<Props>();

const { can, canAny } = usePermission();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Diller', href: languageRoute().url },
];

const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingLanguage = ref<Language | null>(null);
const deletingLanguage = ref<Language | null>(null);

const form = useForm({
    code: '',
    name: '',
    native_name: '',
    is_default: false,
    is_active: true,
    sort_order: 0,
});

useClearFormErrors(form);

function openCreateDialog() {
    editingLanguage.value = null;
    form.reset();
    form.clearErrors();
    showFormDialog.value = true;
}

function openEditDialog(language: Language) {
    editingLanguage.value = language;
    form.code = language.code;
    form.name = language.name;
    form.native_name = language.native_name;
    form.is_default = language.is_default;
    form.is_active = language.is_active;
    form.sort_order = language.sort_order;
    form.clearErrors();
    showFormDialog.value = true;
}

function openDeleteDialog(language: Language) {
    deletingLanguage.value = language;
    showDeleteDialog.value = true;
}

function submitForm() {
    if (editingLanguage.value) {
        form.put(update.url(editingLanguage.value.id), {
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
    if (!deletingLanguage.value) return;

    form.delete(destroy.url(deletingLanguage.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingLanguage.value = null;
        },
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
                        <Button size="sm" class="h-9" @click="openCreateDialog">
                            <Plus class="mr-2 h-4 w-4" />
                            Yeni Dil Ekle
                        </Button>
                    </div>
                </div>

                <template v-if="languages.length > 0">
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
                                    <TableHead v-if="canAny(['language.update', 'language.delete'])" class="text-right w-[160px]">İşlemler</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="language in languages" :key="language.id" class="hover:bg-muted/50 transition-colors">
                                    <TableCell class="font-medium">{{ language.code }}</TableCell>
                                    <TableCell>{{ language.name }}</TableCell>
                                    <TableCell>{{ language.native_name }}</TableCell>
                                    <TableCell class="text-center">
                                        <Badge v-if="language.is_default" variant="default">Varsayılan</Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="language.is_active ? 'default' : 'secondary'">
                                            {{ language.is_active ? 'Aktif' : 'Pasif' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">{{ language.sort_order }}</TableCell>
                                    <TableCell v-if="canAny(['language.update', 'language.delete'])" class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button v-if="can('language.update')" variant="outline" size="sm" class="h-8" @click="openEditDialog(language)">
                                                <Pencil class="mr-2 h-3.5 w-3.5" />
                                                Düzenle
                                            </Button>
                                            <Button v-if="can('language.delete')" variant="outline" size="sm" class="h-8 text-destructive border-destructive/30 hover:bg-destructive/5" @click="openDeleteDialog(language)">
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
                        title="Dil Bulunamadı"
                        description="Sistemde henüz hiç dil oluşturulmamış."
                        :action-label="can('language.create') ? 'Yeni Dil Ekle' : undefined"
                        @action="can('language.create') && openCreateDialog()"
                    />
                </div>
            </div>

            <!-- Create/Edit Dialog -->
            <Dialog v-model:open="showFormDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{{ editingLanguage ? 'Dil Düzenle' : 'Yeni Dil Ekle' }}</DialogTitle>
                        <DialogDescription>
                            {{ editingLanguage ? 'Dil bilgilerini güncelleyin.' : 'Sisteme yeni bir dil ekleyin.' }}
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="code">Dil Kodu</Label>
                                <Input id="code" v-model="form.code" placeholder="tr" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.code" />
                            </div>

                            <div class="space-y-2">
                                <Label for="sort_order">Sıralama</Label>
                                <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.sort_order" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Ad</Label>
                                <Input id="name" v-model="form.name" placeholder="Türkçe" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="native_name">Yerel Ad</Label>
                                <Input id="native_name" v-model="form.native_name" placeholder="Türkçe" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.native_name" />
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-2">
                                <Switch id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                <Label for="is_active">Aktif</Label>
                            </div>

                            <div class="flex items-center gap-2">
                                <Switch id="is_default" :checked="form.is_default" @update:checked="form.is_default = $event" />
                                <Label for="is_default">Varsayılan</Label>
                            </div>
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
                        <AlertDialogTitle>Dili silmek istediğinize emin misiniz?</AlertDialogTitle>
                        <AlertDialogDescription>
                            <strong>{{ deletingLanguage?.name }}</strong> dili kalıcı olarak silinecektir. Bu işlem geri alınamaz.
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
