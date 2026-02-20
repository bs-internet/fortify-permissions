<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { Monitor, Smartphone, Globe, ShieldCheck, AlertTriangle } from 'lucide-vue-next'
import { ref } from 'vue'
import Heading from '@/components/app/common/Heading.vue'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog'
import AppLayout from '@/layouts/AppLayout.vue'
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue'
import { type BreadcrumbItem } from '@/types'

type Session = {
    id: number
    ip_address: string
    location: string | null
    user_agent: string
    is_current_device: boolean
    login_at: string
    login_successful: boolean
}

interface Props {
    sessions: {
        data: Session[]
        links: Array<{
            url: string | null
            label: string
            active: boolean
        }>
    }
}

const props = defineProps<Props>()

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Oturum Güvenliği',
        href: '/profile/sessions',
    },
]

// Modal Durumları
const selectedSession = ref<Session | null>(null)
const showSingleDialog = ref(false)
const showBulkDialog = ref(false)

// Tekli oturum kapatma başlat
function confirmDelete(session: Session) {
    selectedSession.value = session
    showSingleDialog.value = true
}

// Tekli oturum kapatma onayla
function deleteSession() {
    if (!selectedSession.value) return
    router.delete(`/profile/sessions/${selectedSession.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showSingleDialog.value = false
            selectedSession.value = null
        },
    })
}

// Tüm diğer oturumları kapatma onayla
function terminateOtherSessions() {
    router.delete('/profile/sessions', {
        preserveScroll: true,
        onSuccess: () => {
            showBulkDialog.value = false
        },
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Oturum Güvenliği" />

        <ProfileLayout>
            <div class="space-y-6">
                <div class="flex items-start justify-between">
                    <Heading
                        variant="small"
                        title="Aktif Oturumlar"
                        description="Hesabınıza erişimi olan cihazları buradan yönetebilirsiniz."
                    />
                    
                </div>

                <Button
                    variant="outline"
                    size="sm"
                    @click="showBulkDialog = true"
                    class="w-full cursor-pointer text-xs font-medium border-destructive/20 text-destructive hover:bg-destructive/5 hover:text-destructive"
                >
                    Tüm Diğer Oturumları Kapat
                </Button>

                <div class="grid gap-3">
                    <div
                        v-for="session in sessions.data"
                        :key="session.id"
                        class="relative flex flex-col gap-3 rounded-lg border p-4 transition-all"
                        :class="[session.is_current_device ? 'border-primary/20' : 'hover:border-border/80']"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                                    <Monitor v-if="session.user_agent.includes('Windows') || session.user_agent.includes('Macintosh')" class="h-4 w-4" />
                                    <Smartphone v-else class="h-4 w-4" />
                                </div>

                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold leading-none">
                                            {{ session.location || session.ip_address }}
                                        </span>

                                        <span
                                            v-if="session.is_current_device"
                                            class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-medium text-primary uppercase tracking-wider"
                                        >
                                            <span class="mr-1 h-1 w-1 rounded-full bg-current" />
                                            Şu anki cihaz
                                        </span>
                                    </div>
                                    <div class="mt-1.5 flex items-center gap-2 text-xs text-muted-foreground">
                                        <span v-if="session.location" class="flex items-center gap-1">
                                            <Globe class="h-3 w-3" /> {{ session.ip_address }}
                                        </span>
                                        <span v-if="session.location">•</span>
                                        <span>{{ session.login_at }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span v-if="!session.login_successful" class="text-[10px] font-bold text-destructive uppercase">
                                    Başarısız
                                </span>

                                <Button
                                    v-if="!session.is_current_device"
                                    variant="ghost"
                                    size="sm"
                                    @click="confirmDelete(session)"
                                    class="h-8 px-2 text-xs text-destructive hover:text-destructive hover:bg-destructive/5"
                                >
                                    Oturumu Kapat
                                </Button>
                                
                                <ShieldCheck v-else class="h-4 w-4 text-primary/40" />
                            </div>
                        </div>

                        <div class="truncate border-t pt-2 text-[10px] text-muted-foreground/60 antialiased font-mono">
                            {{ session.user_agent }}
                        </div>
                    </div>
                </div>

                <nav v-if="sessions.links.length > 3" class="flex justify-center gap-1">
                    <button
                        v-for="(link, k) in sessions.links"
                        :key="k"
                        v-html="link.label"
                        :disabled="!link.url || link.active"
                        @click="router.visit(link.url!)"
                        class="flex h-8 min-w-[32px] items-center justify-center rounded-md border px-2 text-xs transition-colors"
                        :class="[
                            link.active ? 'bg-primary text-primary-foreground border-primary' : 'hover:bg-accent',
                            !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                        ]"
                    />
                </nav>
            </div>
        </ProfileLayout>

        <Dialog v-model:open="showSingleDialog">
            <DialogContent class="sm:max-w-[400px]">
                <DialogHeader>
                    <DialogTitle>Oturumu Kapat</DialogTitle>
                    <DialogDescription>
                        Seçilen cihazın hesabınıza erişimi kesilecek. Bu cihazda çalışan aktif işlemleriniz varsa kaydedilmemiş olabilir.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="flex gap-2 sm:gap-0">
                    <Button variant="outline" @click="showSingleDialog = false" class="flex-1">Vazgeç</Button>
                    <Button variant="destructive" @click="deleteSession" class="flex-1">Oturumu Sonlandır</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="showBulkDialog">
            <DialogContent class="sm:max-w-[400px]">
                <DialogHeader>
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                        <AlertTriangle class="h-6 w-6" />
                    </div>
                    <DialogTitle class="text-center">Tüm Cihazlardan Çıkış Yap</DialogTitle>
                    <DialogDescription class="text-center">
                        Bu işlem, **şu an kullandığınız cihaz dışındaki** tüm açık oturumları sonlandıracaktır. Devam etmek istediğinize emin misiniz?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="mt-4 flex flex-col gap-2 sm:flex-row sm:gap-0">
                    <Button variant="outline" @click="showBulkDialog = false" class="sm:flex-1">Vazgeç</Button>
                    <Button variant="destructive" @click="terminateOtherSessions" class="sm:flex-1">Evet, Hepsini Kapat</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>