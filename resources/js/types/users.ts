export type Permission = {
    id: string;
    name: string;
    label: string;
    description: string | null;
    guard_name: string;
    created_at: string;
    updated_at: string;
};

export type Role = {
    id: string;
    name: string;
    label: string;
    description: string | null;
    guard_name: string;
    permissions: Permission[];
    created_at: string;
    updated_at: string;
};
