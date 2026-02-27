export type User = {
    id: string;
    name: string;
    email: string;
    title: string | null;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Role[];
    can?: Record<string, boolean>;
    [key: string]: unknown;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};

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
