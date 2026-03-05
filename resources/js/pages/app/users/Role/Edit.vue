<script setup lang="ts">
import { Deferred, Head, useForm, Link, router } from '@inertiajs/vue3';
import {
    ChevronLeft,
    Save,
    Trash2,
    ShieldCheck,
    CheckCircle2,
    Loader2,
    AlertTriangle
} from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';
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
import { Skeleton } from '@/components/ui/skeleton';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as roleIndex } from '@/routes/users/roles';
import { update as roleUpdate, destroy as roleDelete } from '@/actions/App/Http/Controllers/Users/RoleController';
import { type BreadcrumbItem, type Permission, type Role } from '@/types';

const { can } = usePermission();

const props = defineProps<{
    role: Role & { permissions: { id: string | number }[] };
    permissions: Record<string, Permission[]>;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Roller', href: roleIndex().url },
    { title: 'Rolü Düzenle', href: '#' },
];

// 1. Önce boş bir reactive obje oluşturuyoruz
const selected = reactive<Record<string, boolean>>({});

// 2. Props'tan gelen mevcut izinleri hemen String key olarak objeye işliyoruz
props.role.permissions.forEach((p) => {
    selected[String(p.id)] = true;
});

// 3. Formu başlatırken doğrudan selected objesindeki TRUE olanları diziye çeviriyoruz
const form = useForm({
    label: props.role.label,
    description: props.role.description,
    permissions: Object.keys(selected).filter(id => selected[id] === true),
});

useClearFormErrors(form);

// Sayaç her zaman formdaki güncel uzunluğu takip eder
const selectedCount = computed(() => form.permissions.length);

const syncFormPermissions = () => {
    form.permissions = Object.keys(selected).filter(
        (id) => selected[id] === true,
    );
};

const submit = () => {
    form.put(roleUpdate.url(props.role));
};

const confirmDelete = () => {
    router.delete(roleDelete.url(props.role));
};

const togglePermission = (id: string | number) => {
    const key = String(id);
    selected[key] = !selected[key];
    syncFormPermissions();
};

const toggleModule = (modulePermissions: Permission[]) => {
    const allSelected = isModuleFullySelected(modulePermissions);
    modulePermissions.forEach((p) => {
        selected[String(p.id)] = !allSelected;
    });
    syncFormPermissions();
};

const isModuleFullySelected = (modulePermissions: Permission[]): boolean => {
    if (!modulePermissions.length) return false;
    return modulePermissions.every((p) => !!selected[String(p.id)]);
};

const permissionSearch = ref('');

const filteredPermissions = computed(() => {
    const query = permissionSearch.value.toLowerCase().trim();
    if (!query) return props.permissions;

    const result: Record<string, Permission[]> = {};
    for (const [moduleName, modulePermissions] of Object.entries(props.permissions)) {
        const filtered = modulePermissions.filter(
            (p) => p.label.toLowerCase().includes(query) || moduleName.toLowerCase().includes(query),
        );
        if (filtered.length > 0) {
            result[moduleName] = filtered;
        }
    }
    return result;
});
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

                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex items-center gap-2 font-semibold">
                                <CheckCircle2 class="h-5 w-5 text-green-600" />
                                <span>Yetki Matrisi</span>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground bg-muted px-2 py-1 rounded">
                                {{ selectedCount }} Aktif Yetki
                            </span>
                        </div>

                        <Input
                            v-model="permissionSearch"
                            placeholder="İzin veya modül adı ara..."
                            class="shadow-none focus-visible:ring-1"
                        />

                        <Deferred data="permissions">
                            <template #fallback>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div v-for="i in 4" :key="i" class="rounded-md border border-border bg-card overflow-hidden shadow-none">
                                        <div class="bg-muted/40 px-4 py-2.5 border-b border-border">
                                            <Skeleton class="h-4 w-24" />
                                        </div>
                                        <div class="p-4 grid gap-3">
                                            <div v-for="j in 4" :key="j" class="flex items-center space-x-3">
                                                <Skeleton class="h-4 w-4 rounded" />
                                                <Skeleton class="h-4 w-32" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div
                                    v-for="(modulePermissions, moduleName) in filteredPermissions"
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
                                                :checked="!!selected[String(permission.id)]"
                                                @update:checked="togglePermission(permission.id)"
                                            />
                                            <span
                                                class="text-sm font-medium leading-none cursor-pointer group-hover:text-primary transition-colors select-none"
                                                @click="togglePermission(permission.id)"
                                            >
                                                {{ permission.label }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Deferred>
                    </div>
                </form>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
