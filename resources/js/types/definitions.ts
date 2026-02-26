export type Language = {
    id: string;
    code: string;
    name: string;
    native_name: string;
    is_default: boolean;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
};

export type Currency = {
    id: string;
    code: string;
    name: string;
    symbol: string;
    decimal_places: number;
    thousand_separator: string;
    decimal_separator: string;
    is_default: boolean;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
};

export type Unit = {
    id: string;
    name: string;
    abbreviation: string;
    type: string;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
};
