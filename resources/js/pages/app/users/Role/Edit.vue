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
import { computed, ref } from 'vue';
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
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as roleIndex, update as roleUpdate, destroy as roleDelete } from '@/routes/users/roles';
import { type BreadcrumbItem, type Permission, type Role } from '@/types';

const { can } = usePermission();

const props = defineProps<{
    role: Role & { permissions: { id: string }[] };
    permissions: Record<string, Permission[]>;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Roller', href: roleIndex().url },
    { title: 'Rolü Düzenle', href: '#' },
];

const initialPermissionIds = props.role.permissions.map((p) => String(p.id));

const selectedPermissions = ref<Set<string>>(new Set(initialPermissionIds));

const selectedCount = computed(() => selectedPermissions.value.size);

const isSelected = (id: string): boolean => selectedPermissions.value.has(String(id));

const syncFormPermissions = () => {
    form.permissions = Array.from(selectedPermissions.value);
};

const form = useForm({
    label: props.role.label,
    description: props.role.description,
    permissions: initialPermissionIds,
});

const submit = () => {
    form.put(roleUpdate(props.role).url);
};

const confirmDelete = () => {
    router.delete(roleDelete(props.role).url);
};

const togglePermission = (id: string) => {
    const permissionId = String(id);
    if (selectedPermissions.value.has(permissionId)) {
        selectedPermissions.value.delete(permissionId);
    } else {
        selectedPermissions.value.add(permissionId);
    }
    selectedPermissions.value = new Set(selectedPermissions.value);
    syncFormPermissions();
};

const toggleModule = (modulePermissions: Permission[]) => {
    const moduleIds = modulePermissions.map((p) => String(p.id));
    const allSelected = isModuleFullySelected(modulePermissions);

    if (allSelected) {
        moduleIds.forEach((id) => selectedPermissions.value.delete(id));
    } else {
        moduleIds.forEach((id) => selectedPermissions.value.add(id));
    }
    selectedPermissions.value = new Set(selectedPermissions.value);
    syncFormPermissions();
};

const isModuleFullySelected = (modulePermissions: Permission[]): boolean => {
    if (!modulePermissions.length) return false;
    return modulePermissions.every((p) => selectedPermissions.value.has(String(p.id)));
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
                            <Button variant="secondary" size="icon" class="rounded-full bg-muted hover:bg-muted/80 shadow-none border">
                                <ChevronLeft class="h-5 w-5 text-foreground" />
                            </Button>
                        </Link>
                        <Heading
                            variant="small"
                            :title="`${role.label} Rolünü Düzenle`"
                            description="Rol bilgilerini ve yetki tanımlarını güncelleyin."
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <AlertDialog v-if="can('role.delete')">
                            <AlertDialogTrigger as-child>
                                <Button variant="outline" size="sm" class="h-9 text-destructive border-destructive/20 hover:bg-destructive/5 shadow-none">
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    Rolü Sil
                                </Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Bu rolü silmek istediğinize emin misiniz?</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        <div class="bg-amber-50 border border-amber-200 p-3 rounded-md text-amber-800 text-xs mt-2 space-y-1">
                                            <p class="font-bold flex items-center gap-1">
                                                <AlertTriangle class="h-3 w-3" /> Dikkat:
                                            </p>
                                            <ul class="list-disc ml-4">
                                                <li>Rolü kullanan kullanıcı varsa silinemez.</li>
                                                <li>Tanımlı yetkileri önce kaldırmanız önerilir.</li>
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

                        <Button type="submit" form="roleEditForm" :disabled="form.processing" class="shadow-none px-6">
                            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                            <Save v-else class="mr-2 h-4 w-4" />
                            Kaydet
                        </Button>
                    </div>
                </div>

                <form id="roleEditForm" @submit.prevent="submit" class="space-y-6">
                    <div class="rounded-md border border-border bg-card p-6 shadow-none space-y-4">
                        <div class="flex items-center gap-2 mb-2 text-primary font-semibold text-sm uppercase tracking-wider">
                            <ShieldCheck class="h-5 w-5" />
                            <span>Genel Bilgiler</span>
                        </div>

                        <div class="grid gap-4">
                            <div class="space-y-2">
                                <Label for="label">Rol Adı</Label>
                                <Input id="label" v-model="form.label" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.label" />
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Açıklama</Label>
                                <Textarea id="description" v-model="form.description" class="min-h-[100px] resize-none shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.description" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 px-2">
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex items-center gap-2 font-semibold">
                                <CheckCircle2 class="h-5 w-5 text-green-600" />
                                <span>Yetki Matrisi</span>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground bg-muted px-2 py-1 rounded">
                                {{ selectedCount }} Aktif Yetki
                            </span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div
                                v-for="(modulePermissions, moduleName) in permissions"
                                :key="moduleName"
                                class="rounded-md border border-border bg-card overflow-hidden shadow-none"
                            >
                                <div class="bg-muted/40 px-4 py-2.5 border-b border-border flex justify-between items-center">
                                    <h3 class="font-bold text-[12px] uppercase tracking-tight text-foreground/70">
                                        {{ moduleName }}
                                    </h3>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="h-7 text-[11px] hover:bg-primary/5 font-semibold transition-all"
                                        @click="toggleModule(modulePermissions)"
                                    >
                                        {{ isModuleFullySelected(modulePermissions) ? 'Tümünü Kaldır' : 'Tümünü Seç' }}
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
                                            :checked="isSelected(permission.id)"
                                            @update:checked="() => togglePermission(permission.id)"
                                        />
                                        <label
                                            :for="permission.id"
                                            class="text-sm font-medium leading-none cursor-pointer group-hover:text-primary transition-colors select-none"
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
