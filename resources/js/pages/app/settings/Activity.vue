<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Heading from '@/components/app/common/Heading.vue';
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

interface Props {
    activities: {
        data: any[];
        links: any[];
    };
    filters: {
        type?: string;
    };
    availableTypes: string[];
}

const props = defineProps<Props>();

// Filtreleme durumu
const selectedType = ref(props.filters.type || 'all');

// Seçim değiştiğinde sayfayı yenileyen watch
watch(selectedType, (value) => {
    router.get(
        activityIndex().url,
        { type: value === 'all' ? null : value },
        { preserveState: true, replace: true },
    );
});

/**
 * Dinamik tipleri formatlayan yardımcı fonksiyon.
 * Örn: 'profile_update' -> 'Profile Update'
 */
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Etkinlik Kayıtları" />

        <SettingsLayout>
            <div class="space-y-6">
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <Heading
                        variant="small"
                        title="Etkinlik Kayıtları"
                        description="Sistem üzerinde gerçekleştirdiğiniz tüm işlemlerin dökümü."
                    />

                    <div class="w-full sm:w-[220px] flex sm:justify-end">
                        <Select v-model="selectedType">
                            <SelectTrigger>
                                <SelectValue placeholder="Tüm İşlemler" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all"
                                    >Tüm İşlemler</SelectItem
                                >
                                <SelectItem
                                    v-for="type in availableTypes"
                                    :key="type"
                                    :value="type"
                                >
                                    {{ formatType(type) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="rounded-md border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[50%]">İşlem</TableHead>
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
                                    colspan="4"
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
