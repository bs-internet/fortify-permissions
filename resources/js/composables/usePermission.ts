import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermission() {
    const page = usePage();

    const roles = computed<string[]>(() => (page.props.auth as any)?.roles ?? []);
    const permissions = computed<string[]>(() => (page.props.auth as any)?.permissions ?? []);

    function hasRole(role: string): boolean {
        return roles.value.includes(role);
    }

    function hasAnyRole(...roleNames: string[]): boolean {
        return roleNames.some((role) => roles.value.includes(role));
    }

    function hasPermission(permission: string): boolean {
        return permissions.value.includes(permission);
    }

    function hasAnyPermission(...permissionNames: string[]): boolean {
        return permissionNames.some((perm) => permissions.value.includes(perm));
    }

    return {
        roles,
        permissions,
        hasRole,
        hasAnyRole,
        hasPermission,
        hasAnyPermission,
    };
}
