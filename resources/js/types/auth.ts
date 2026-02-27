import type { User } from "@/types/user";

export type Auth = {
    user: User;
    roles: string[];
    permissions: string[];
    is_super_admin: boolean;
};
