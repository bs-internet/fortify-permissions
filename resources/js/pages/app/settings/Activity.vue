<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Heading from '@/components/app/common/Heading.vue';
import TableSkeleton from '@/components/app/common/TableSkeleton.vue';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/pages/app/settings/partials/Layout.vue';
import { index as activityIndex } from '@/routes/settings/activities';
import { type BreadcrumbItem } from '@/types';

interface ActivityUser {
    id: string;
    name: string;
}

interface Props {
    activities: {
        data: any[];
        links: any[];
    };
    filters: {
        type?: string;
        user_id?: string;
        date_from?: string;
        date_to?: string;
    };
    availableTypes: string[];
    availableUsers: ActivityUser[];
}

const props = defineProps<Props>();

const selectedType = ref(props.filters.type || 'all');
const selectedUser = ref(props.filters.user_id || 'all');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

function getFilterParams() {
    return {
        type: selectedType.value === 'all' ? undefined : selectedType.value,
        user_id: selectedUser.value === 'all' ? undefined : selectedUser.value,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    };
}

function applyFilters() {
    router.get(activityIndex().url, getFilterParams(), {
        preserveState: true,
        replace: true,
    });
}

watch(selectedType, () => applyFilters());
watch(selectedUser, () => applyFilters());

let dateDebounce: ReturnType<typeof setTimeout>;
watch([dateFrom, dateTo], () => {
    clearTimeout(dateDebounce);
    dateDebounce = setTimeout(() => applyFilters(), 500);
});

const formatType = (type: string) => {
    return type
        .replace(/_/g, ' ')
        .split(' ')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Ayarlar', href: '#' },
    { title: 'Etkinlik Kayıtları', href: activityIndex().url },
];

const tableLoading = ref(false);
router.on('start', () => { tableLoading.value = true; });
router.on('finish', () => { tableLoading.value = false; });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Etkinlik Kayıtları" />

        <SettingsLayout>
            <div class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <Heading
                        variant="small"
                        title="Etkinlik Kayıtları"
                        description="Sistem üzerinde gerçekleştirdiğiniz tüm işlemlerin dökümü."
                    />
                </div>

                <!-- Filtreler -->
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <Select v-model="selectedType">
                        <SelectTrigger class="sm:w-[200px]">
                            <SelectValue placeholder="Tüm İşlemler" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Tüm İşlemler</SelectItem>
                            <SelectItem
                                v-for="type in availableTypes"
                                :key="type"
                                :value="type"
                            >
                                {{ formatType(type) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-if="availableUsers.length > 0" v-model="selectedUser">
                        <SelectTrigger class="sm:w-[200px]">
                            <SelectValue placeholder="Tüm Kullanıcılar" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Tüm Kullanıcılar</SelectItem>
                            <SelectItem
                                v-for="user in availableUsers"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Input
                        v-model="dateFrom"
                        type="date"
                        class="sm:w-[170px]"
                    />
                    <Input
                        v-model="dateTo"
                        type="date"
                        class="sm:w-[170px]"
                    />
                </div>

                <TableSkeleton v-if="tableLoading" :rows="5" :columns="5" />
                <div v-else class="rounded-md border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[40%]">İşlem</TableHead>
                                <TableHead>Kullanıcı</TableHead>
                                <TableHead>Tip</TableHead>
                                <TableHead>IP Adresi</TableHead>
                                <TableHead class="text-right">Tarih</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="item in activities.data"
                                :key="item.id"
                                class="group"
                            >
                                <TableCell class="text-sm font-medium">
                                    {{ item.description }}
                                </TableCell>
                                <TableCell class="text-sm text-muted-foreground">
                                    {{ item.user_name ?? 'Sistem' }}
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="inline-flex items-center rounded-md border bg-muted/50 px-2 py-0.5 text-[10px] font-bold tracking-tight text-muted-foreground uppercase transition-colors group-hover:bg-background"
                                    >
                                        {{ item.type }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <code
                                        class="rounded bg-muted px-1.5 py-0.5 text-[11px] text-muted-foreground"
                                    >
                                        {{ item.ip_address }}
                                    </code>
                                </TableCell>
                                <TableCell
                                    class="text-right text-xs whitespace-nowrap text-muted-foreground italic"
                                >
                                    {{ item.date_human }}
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="activities.data.length === 0">
                                <TableCell
                                    colspan="5"
                                    class="h-32 text-center text-muted-foreground"
                                >
                                    Henüz bir etkinlik kaydı bulunmuyor.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <nav
                    v-if="activities.links.length > 3"
                    class="flex justify-center gap-1.5 pt-4"
                >
                    <button
                        v-for="(link, k) in activities.links"
                        :key="k"
                        v-html="link.label"
                        :disabled="!link.url || link.active"
                        @click="router.visit(link.url!)"
                        class="flex h-9 min-w-[36px] items-center justify-center rounded-md border px-3 text-xs font-medium shadow-sm transition-all"
                        :class="[
                            link.active
                                ? 'pointer-events-none border-primary bg-primary text-primary-foreground'
                                : 'bg-background hover:bg-accent hover:text-accent-foreground',
                            !link.url
                                ? 'cursor-not-allowed border-dashed opacity-40'
                                : 'cursor-pointer',
                        ]"
                    />
                </nav>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
