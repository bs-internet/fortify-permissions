<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import {
    ChevronLeft,
    Save,
    Trash2,
    ShieldCheck,
    CheckCircle2,
    Loader2,
    AlertTriangle
} from 'lucide-vue-next';
import { ref } from 'vue';
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
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as roleIndex, update as roleUpdate, destroy as roleDelete } from '@/routes/users/roles';
import { type BreadcrumbItem, type Permission, type Role } from '@/types';

const props = defineProps<{
    role: Role & { permissions: { id: string }[] };
    permissions: Record<string, Permission[]>;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Roller', href: roleIndex().url },
    { title: 'Rolü Düzenle', href: '#' },
];

// Mevcut yetkileri formun içine başlangıç değeri olarak atıyoruz
const form = useForm({
    label: props.role.label,
    description: props.role.description,
    permissions: props.role.permissions.map(p => p.id),
});

const submit = () => {
    form.put(roleUpdate(props.role).url);
};

// Silme işlemi için ayrı bir form durumu (processing kontrolü için)
const isDeleting = ref(false);

const confirmDelete = () => {
    router.delete(roleDelete(props.role).url, {
        onBefore: () => { isDeleting.value = true },
        onFinish: () => { isDeleting.value = false },
    });
};

const togglePermission = (id: string) => {
    const index = form.permissions.indexOf(id);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(id);
    }
};

const toggleModule = (modulePermissions: Permission[]) => {
    const moduleIds = modulePermissions.map(p => p.id);
    const allSelected = moduleIds.every(id => form.permissions.includes(id));

    if (allSelected) {
        form.permissions = form.permissions.filter(id => !moduleIds.includes(id));
    } else {
        const newIds = moduleIds.filter(id => !form.permissions.includes(id));
        form.permissions.push(...newIds);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Düzenle: ${role.label}`" />
        <UsersLayout>
            <div class="space-y-6 max-w-5xl mx-auto pb-20">
                <div class="flex items-center justify-between px-2">
                    <div class="flex items-center gap-4">
                        <Link :href="roleIndex().url">
                            <Button variant="ghost" size="icon" class="rounded-full">
                                <ChevronLeft class="h-5 w-5" />
                            </Button>
                        </Link>
                        <Heading
                            variant="small"
                            :title="`${role.label} Rolünü Düzenle`"
                            description="Rol bilgilerini ve yetki tanımlarını güncelleyin."
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <Button variant="destructive" size="sm" class="h-9">
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    Rolü Sil
                                </Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Bu rolü silmek istediğinize emin misiniz?</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        <div class="bg-amber-50 border border-amber-200 p-3 rounded-md text-amber-800 text-xs mt-2 space-y-2">
                                            <p class="font-bold flex items-center gap-1">
                                                <AlertTriangle class="h-3 w-3" /> Dikkat:
                                            </p>
                                            <ul class="list-disc ml-4">
                                                <li>Rolü kullanan kullanıcı varsa silinemez.</li>
                                                <li>Rol üzerinde hala yetki tanımlıysa silinemez (Önce tüm yetkileri kaldırmalısınız).</li>
                                            </ul>
                                        </div>
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Vazgeç</AlertDialogCancel>
                                    <AlertDialogAction @click="confirmDelete" class="bg-destructive hover:bg-destructive/90 text-white">
                                        Evet, Sil
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>

                        <Button type="submit" form="roleEditForm" :disabled="form.processing">
                            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                            <Save v-else class="mr-2 h-4 w-4" />
                            Değişiklikleri Kaydet
                        </Button>
                    </div>
                </div>

                <form id="roleEditForm" @submit.prevent="submit" class="space-y-6">
                    <div class="rounded-xl border bg-card p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 mb-2 text-primary font-semibold">
                            <ShieldCheck class="h-5 w-5" />
                            <span>Genel Bilgiler</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="label">Rol Adı</Label>
                                <Input
                                    id="label"
                                    v-model="form.label"
                                    :error="!!form.errors.label"
                                />
                                <InputError :message="form.errors.label" />
                            </div>
                            <div class="space-y-2">
                                <Label class="opacity-70">Teknik Ad (Güncellenemez)</Label>
                                <Input :value="role.name" disabled class="bg-muted cursor-not-allowed" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Açıklama</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="min-h-[100px] resize-none"
                            />
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <div class="space-y-4 px-2">
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex items-center gap-2 font-semibold">
                                <CheckCircle2 class="h-5 w-5 text-green-600" />
                                <span>Yetkileri Düzenle</span>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground">
                                {{ form.permissions.length }} Aktif Yetki
                            </span>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div
                                v-for="(modulePermissions, moduleName) in permissions"
                                :key="moduleName"
                                class="rounded-xl border bg-card overflow-hidden shadow-sm border-t-2 border-t-primary/20"
                            >
                                <div class="bg-muted/30 px-4 py-3 border-b flex justify-between items-center">
                                    <h3 class="font-bold text-xs uppercase tracking-tighter text-foreground/70">
                                        {{ moduleName }}
                                    </h3>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="h-7 text-[10px]"
                                        @click="toggleModule(modulePermissions)"
                                    >
                                        Tümünü Seç
                                    </Button>
                                </div>
                                <div class="p-4 grid gap-3">
                                    <div
                                        v-for="permission in modulePermissions"
                                        :key="permission.id"
                                        class="flex items-center space-x-3 group"
                                    >
                                        <Checkbox
                                            :id="permission.id"
                                            :checked="form.permissions.includes(permission.id)"
                                            @update:checked="togglePermission(permission.id)"
                                        />
                                        <label
                                            :for="permission.id"
                                            class="text-sm font-medium leading-none cursor-pointer group-hover:text-primary transition-colors"
                                        >
                                            {{ permission.label }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
