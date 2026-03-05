<script setup lang="ts">
import { Deferred, Head, useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, Save, UserCog, Shield, CheckCircle2, AlertCircle, Loader2, Clock } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Skeleton } from '@/components/ui/skeleton';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as userIndex } from '@/routes/users';
import { update as userUpdate, verifyEmail as verifyEmailAction } from '@/actions/App/Http/Controllers/Users/UserController';
import { type BreadcrumbItem, type Language, type Permission, type Role } from '@/types';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import { usePermission } from '@/composables/usePermission';

type EditUser = {
    id: string;
    name: string;
    email: string;
    email_verified_at: string | null;
    title: string | null;
    status: number;
    language_id: string | null;
    last_login_at: string | null;
    roles: { id: string }[];
    permissions: { id: string }[];
};

const props = defineProps<{
    user: EditUser;
    roles: Role[];
    permissions: Record<string, Permission[]>;
    languages: Language[];
    statuses: Record<number, string>;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Kayıtlı Kullanıcılar', href: userIndex().url },
    { title: 'Kullanıcı Düzenle', href: '#' },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    title: props.user.title ?? '',
    status: props.user.status,
    language_id: props.user.language_id ?? '',
    roles: props.user.roles.map((r) => String(r.id)),
    permissions: props.user.permissions.map((p) => String(p.id)),
});

useClearFormErrors(form);

const selectedRoles = reactive<Record<string, boolean>>({});
props.user.roles.forEach((r) => { selectedRoles[String(r.id)] = true; });

const selectedPermissions = reactive<Record<string, boolean>>({});
props.user.permissions.forEach((p) => { selectedPermissions[String(p.id)] = true; });

const selectedRoleCount = computed(() =>
    Object.values(selectedRoles).filter(Boolean).length,
);

const selectedPermissionCount = computed(() =>
    Object.values(selectedPermissions).filter(Boolean).length,
);

const syncForm = () => {
    form.roles = Object.keys(selectedRoles).filter((id) => selectedRoles[id]);
    form.permissions = Object.keys(selectedPermissions).filter((id) => selectedPermissions[id]);
};

const toggleRole = (id: string) => {
    selectedRoles[id] = !selectedRoles[id];
    syncForm();
};

const togglePermission = (id: string) => {
    selectedPermissions[id] = !selectedPermissions[id];
    syncForm();
};

const togglePermissionModule = (modulePermissions: Permission[]) => {
    const allSelected = isPermissionModuleFullySelected(modulePermissions);
    modulePermissions.forEach((p) => {
        selectedPermissions[String(p.id)] = !allSelected;
    });
    syncForm();
};

const isPermissionModuleFullySelected = (modulePermissions: Permission[]): boolean => {
    if (modulePermissions.length === 0) return false;
    return modulePermissions.every((p) => !!selectedPermissions[String(p.id)]);
};

const { can, user: authUser } = usePermission();
const isSelf = computed(() => props.user.id === authUser.value?.id);

const showLanguageField = computed(() => props.languages.length > 1);
const showDirectPermissions = ref(props.user.permissions.length > 0);

const formatDate = (date: string): string => {
    return new Intl.DateTimeFormat('tr-TR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    }).format(new Date(date));
};

const verifyingEmail = ref(false);
const handleVerifyEmail = () => {
    verifyingEmail.value = true;
    router.post(verifyEmailAction.url(props.user.id), {}, {
        preserveScroll: true,
        onFinish: () => { verifyingEmail.value = false; },
    });
};

const submit = () => {
    form.put(userUpdate.url(props.user.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Düzenle: ${user.name}`" />
        <UsersLayout>
            <div class="space-y-6 max-w-5xl mx-auto pb-20">
                <div class="flex items-center justify-between px-2">
                    <div class="flex items-center gap-4">
                        <Link :href="userIndex().url">
                            <Button variant="secondary" size="icon" class="rounded-full bg-muted hover:bg-muted/80 shadow-none border">
                                <ChevronLeft class="h-5 w-5 text-foreground" />
                            </Button>
                        </Link>
                        <Heading
                            variant="small"
                            :title="`${user.name} Kullanıcısını Düzenle`"
                            description="Kullanıcı bilgilerini ve yetkilerini güncelleyin."
                        />
                    </div>
                    <Button type="submit" form="userEditForm" :disabled="form.processing" class="shadow-none px-6">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        <Save v-else class="mr-2 h-4 w-4" />
                        Kaydet
                    </Button>
                </div>

                <form id="userEditForm" @submit.prevent="submit" class="space-y-6">
                    <!-- Genel Bilgiler -->
                    <div class="rounded-md border border-border bg-card p-6 shadow-none space-y-4">
                        <div class="flex items-center gap-2 mb-2 text-primary font-semibold text-sm uppercase tracking-wider">
                            <UserCog class="h-5 w-5" />
                            <span>Genel Bilgiler</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Ad Soyad</Label>
                                <Input id="name" v-model="form.name" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email">E-posta</Label>
                                <Input id="email" v-model="form.email" type="email" class="shadow-none focus-visible:ring-1" :disabled="!can('user.change-email')" />
                                <div class="flex items-center gap-2 text-xs">
                                    <span v-if="user.email_verified_at" class="text-green-600 dark:text-green-400 flex items-center gap-1">
                                        <CheckCircle2 class="h-3.5 w-3.5" /> Doğrulanmış
                                    </span>
                                    <template v-else>
                                        <span class="text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                            <AlertCircle class="h-3.5 w-3.5" /> Doğrulanmamış
                                        </span>
                                        <Button
                                            v-if="can('user.verify-email')"
                                            type="button"
                                            variant="link"
                                            size="sm"
                                            class="h-auto p-0 text-xs"
                                            :disabled="verifyingEmail"
                                            @click="handleVerifyEmail"
                                        >
                                            <Loader2 v-if="verifyingEmail" class="mr-1 h-3 w-3 animate-spin" />
                                            Onayla
                                        </Button>
                                    </template>
                                </div>
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="title">Ünvan</Label>
                                <Input id="title" v-model="form.title" class="shadow-none focus-visible:ring-1" />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div class="space-y-2">
                                <Label for="status">Durum</Label>
                                <Select v-model="form.status" :disabled="!can('user.change-status') || isSelf">
                                    <SelectTrigger class="shadow-none focus:ring-1">
                                        <SelectValue placeholder="Durum seçin" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="(label, value) in statuses" :key="value" :value="Number(value)">
                                            {{ label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="isSelf" class="text-xs text-muted-foreground">Kendi hesabınızın durumunu değiştiremezsiniz.</p>
                                <InputError :message="form.errors.status" />
                            </div>
                        </div>

                        <div v-if="showLanguageField" class="space-y-2">
                            <Label for="language_id">Dil</Label>
                            <Select v-model="form.language_id">
                                <SelectTrigger class="shadow-none focus:ring-1">
                                    <SelectValue placeholder="Dil seçin" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="lang in languages" :key="lang.id" :value="lang.id">
                                        {{ lang.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.language_id" />
                        </div>
                    </div>

                    <!-- Roller -->
                    <div v-if="roles.length > 0" class="rounded-md border border-border bg-card p-6 shadow-none space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-primary font-semibold text-sm uppercase tracking-wider">
                                <Shield class="h-5 w-5" />
                                <span>Roller</span>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground bg-muted px-2 py-1 rounded">
                                {{ selectedRoleCount }} Rol Seçildi
                            </span>
                        </div>

                        <p v-if="isSelf" class="text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-md px-3 py-2">
                            Kendi hesabınızın rol ve yetkilerini düzenleyemezsiniz.
                        </p>

                        <div class="grid gap-3 sm:grid-cols-2" :class="{ 'opacity-50 pointer-events-none': isSelf }">
                            <div
                                v-for="role in roles"
                                :key="role.id"
                                class="rounded-md border border-border p-3 flex items-start gap-3 cursor-pointer hover:bg-muted/30 transition-colors"
                                @click="toggleRole(role.id)"
                            >
                                <Checkbox
                                    :checked="!!selectedRoles[role.id]"
                                    @update:checked="() => toggleRole(role.id)"
                                    class="mt-0.5"
                                />
                                <div>
                                    <span class="text-sm font-medium">{{ role.label }}</span>
                                    <p v-if="role.description" class="text-xs text-muted-foreground mt-0.5">
                                        {{ role.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doğrudan Yetkiler (Switch ile açılır) -->
                    <div class="rounded-md border border-border bg-card p-6 shadow-none space-y-4" :class="{ 'opacity-50 pointer-events-none': isSelf }">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-primary font-semibold text-sm uppercase tracking-wider">
                                <CheckCircle2 class="h-5 w-5" />
                                <span>Doğrudan Yetkiler</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span v-if="showDirectPermissions" class="text-xs font-medium text-muted-foreground bg-muted px-2 py-1 rounded">
                                    {{ selectedPermissionCount }} Yetki Seçildi
                                </span>
                                <Switch :checked="showDirectPermissions" @update:checked="(val: boolean) => showDirectPermissions = val" :disabled="isSelf" />
                            </div>
                        </div>

                        <p v-if="!showDirectPermissions" class="text-sm text-muted-foreground">
                            Rol üzerinden gelen yetkilere ek olarak doğrudan yetki atamak için bu alanı aktif edin.
                        </p>

                        <Deferred v-if="showDirectPermissions" data="permissions">
                            <template #fallback>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div v-for="i in 4" :key="i" class="rounded-md border border-border overflow-hidden shadow-none">
                                        <div class="bg-muted/40 px-4 py-2.5 border-b border-border">
                                            <Skeleton class="h-4 w-24" />
                                        </div>
                                        <div class="p-4 grid gap-3">
                                            <div v-for="j in 3" :key="j" class="flex items-center space-x-3">
                                                <Skeleton class="h-4 w-4 rounded" />
                                                <Skeleton class="h-4 w-32" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div
                                    v-for="(modulePermissions, moduleName) in permissions"
                                    :key="moduleName"
                                    class="rounded-md border border-border overflow-hidden shadow-none"
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
                                            @click="togglePermissionModule(modulePermissions)"
                                        >
                                            {{ isPermissionModuleFullySelected(modulePermissions) ? 'Tümünü Kaldır' : 'Tümünü Seç' }}
                                        </Button>
                                    </div>
                                    <div class="p-4 grid gap-3">
                                        <div
                                            v-for="permission in modulePermissions"
                                            :key="permission.id"
                                            class="flex items-center space-x-3 group"
                                        >
                                            <Checkbox
                                                :checked="!!selectedPermissions[String(permission.id)]"
                                                @update:checked="() => togglePermission(permission.id)"
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

                <!-- Son Giriş Bilgisi -->
                <div class="rounded-md border border-border bg-muted/30 p-4 flex items-center gap-2 text-sm text-muted-foreground">
                    <Clock class="h-4 w-4 shrink-0" />
                    <span v-if="user.last_login_at">Son giriş: <strong class="text-foreground">{{ formatDate(user.last_login_at) }}</strong></span>
                    <span v-else>Henüz giriş yapmadı</span>
                </div>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
