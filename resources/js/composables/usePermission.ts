import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import type { User } from "@/types";

type Permissions = Record<string, boolean>;

export function usePermission() {

    const page = usePage();

    const user = computed<User | null>(
        () => page.props.auth.user ?? null
    );

    const permissions = computed<Permissions>(
        () => user.value?.can ?? {}
    );

    const isSuperAdmin = computed(
        () => page.props.auth.is_super_admin === true
    );

    const can = (permission: string): boolean =>
        isSuperAdmin.value || permissions.value[permission] === true;

    const canAny = (perms: string[]): boolean =>
        isSuperAdmin.value || perms.some(can);

    const canAll = (perms: string[]): boolean =>
        isSuperAdmin.value || perms.every(can);

    return {
        can,
        canAny,
        canAll,
        permissions,
        user,
    };
}
