<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft, Save, ShieldPlus, CheckCircle2, Loader2 } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';
import Heading from '@/components/app/common/Heading.vue';
import InputError from '@/components/app/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useClearFormErrors } from '@/composables/useClearFormErrors';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as roleIndex, store as roleStore } from '@/routes/users/roles';
import { type BreadcrumbItem, type Permission } from '@/types';

const props = defineProps<{
    permissions: Record<string, Permission[]>;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Roller', href: roleIndex().url },
    { title: 'Yeni Rol Oluştur', href: '#' },
];

const form = useForm({
    label: '',
    description: '',
    permissions: [] as string[],
});

useClearFormErrors(form);

const selected = reactive<Record<string, boolean>>({});

const selectedCount = computed(() =>
    Object.values(selected).filter(Boolean).length,
);

const syncFormPermissions = () => {
    form.permissions = Object.keys(selected).filter(
        (id) => selected[id],
    );
};

const submit = () => {
    form.post(roleStore().url);
};

const togglePermission = (id: string) => {
    selected[id] = !selected[id];
    syncFormPermissions();
};

const toggleModule = (modulePermissions: Permission[]) => {
    const allSelected = isModuleFullySelected(modulePermissions);
    modulePermissions.forEach((p) => {
        selected[p.id] = !allSelected;
    });
    syncFormPermissions();
};

const isModuleFullySelected = (modulePermissions: Permission[]): boolean => {
    if (modulePermissions.length === 0) return false;
    return modulePermissions.every((p) => !!selected[p.id]);
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
        <Head title="Yeni Rol Oluştur" />
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
                            title="Yeni Rol Oluştur"
                            description="Sistem için yeni bir yetki grubu tanımlayın."
                        />
                    </div>
                    <Button type="submit" form="roleCreateForm" :disabled="form.processing" class="shadow-none">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        <Save v-else class="mr-2 h-4 w-4" />
                        Kaydet
                    </Button>
                </div>

                <form id="roleCreateForm" @submit.prevent="submit" class="space-y-6">
                    <div class="rounded-md border border-border bg-card p-6 shadow-none space-y-4">
                        <div class="flex items-center gap-2 mb-2 text-primary font-semibold text-sm uppercase tracking-wider">
                            <ShieldPlus class="h-5 w-5" />
                            <span>Genel Bilgiler</span>
                        </div>

                        <div class="grid gap-4">
                            <div class="space-y-2">
                                <Label for="label">Rol Adı</Label>
                                <Input
                                    id="label"
                                    v-model="form.label"
                                    placeholder="Örn: Editör, Muhasebe Sorumlusu"
                                    class="shadow-none focus-visible:ring-1"
                                />
                                <InputError :message="form.errors.label" />
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Açıklama</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    placeholder="Bu rolün sorumluluklarını kısaca açıklayın..."
                                    class="min-h-[100px] resize-none shadow-none focus-visible:ring-1"
                                />
                                <InputError :message="form.errors.description" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex items-center gap-2 font-semibold">
                                <CheckCircle2 class="h-5 w-5 text-green-600" />
                                <span>Yetkilendirme Matrisi</span>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground bg-muted px-2 py-1 rounded">
                                {{ selectedCount }} Yetki Seçildi
                            </span>
                        </div>

                        <Input
                            v-model="permissionSearch"
                            placeholder="İzin veya modül adı ara..."
                            class="shadow-none focus-visible:ring-1"
                        />

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
                                            :checked="!!selected[permission.id]"
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
                    </div>
                </form>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
