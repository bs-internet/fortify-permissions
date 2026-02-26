<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
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
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as languageRoute } from '@/routes/settings/definitions/languages';
import { type BreadcrumbItem, type Language } from '@/types';

type Props = {
    languages: Language[];
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Diller', href: languageRoute().url },
];

// Dialog state
const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingLanguage = ref<Language | null>(null);
const deletingLanguage = ref<Language | null>(null);

// Form
const form = useForm({
    code: '',
    name: '',
    native_name: '',
    is_default: false,
    is_active: true,
    sort_order: 0,
});

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
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <Heading variant="small" title="Diller" description="Sistem üzerinde kullanılan diller." />

                    <Button @click="openCreateDialog">Yeni Dil Ekle</Button>
                </div>

                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Kod</TableHead>
                                <TableHead>Ad</TableHead>
                                <TableHead>Yerel Ad</TableHead>
                                <TableHead class="text-center">Varsayılan</TableHead>
                                <TableHead class="text-center">Durum</TableHead>
                                <TableHead class="text-center">Sıra</TableHead>
                                <TableHead class="text-right">İşlemler</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="languages.length === 0">
                                <TableCell :colspan="7" class="text-center text-muted-foreground"> Henüz dil eklenmemiş. </TableCell>
                            </TableRow>
                            <TableRow v-for="language in languages" :key="language.id">
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
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="sm" @click="openEditDialog(language)"> Düzenle </Button>
                                        <Button variant="ghost" size="sm" class="text-destructive" @click="openDeleteDialog(language)">
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
                        <DialogTitle>{{ editingLanguage ? 'Dil Düzenle' : 'Yeni Dil Ekle' }}</DialogTitle>
                        <DialogDescription>
                            {{ editingLanguage ? 'Dil bilgilerini güncelleyin.' : 'Sisteme yeni bir dil ekleyin.' }}
                        </DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitForm">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="code">Dil Kodu</Label>
                                <Input id="code" v-model="form.code" placeholder="tr" />
                                <InputError :message="form.errors.code" />
                            </div>

                            <div class="space-y-2">
                                <Label for="sort_order">Sıralama</Label>
                                <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" />
                                <InputError :message="form.errors.sort_order" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Ad</Label>
                                <Input id="name" v-model="form.name" placeholder="Türkçe" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="native_name">Yerel Ad</Label>
                                <Input id="native_name" v-model="form.native_name" placeholder="Türkçe" />
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
                        <AlertDialogAction @click="confirmDelete">Sil</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DefinitionsLayout>
    </AppLayout>
</template>
