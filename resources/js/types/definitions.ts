export type Language = {
    id: string;
    code: string;
    name: string;
    native_name: string;
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

export type Country = {
    id: string;
    code: string;
    name: string;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
};

export type Tax = {
    id: string;
    name: string;
    rate: number;
    is_active: boolean;
    sort_order: number;
    countries: Country[];
    created_at: string;
    updated_at: string;
};
